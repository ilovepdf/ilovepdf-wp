<?php

namespace Ilove_Pdf_WP\Tools\Base;

/**
 * Status Managment.
 *
 * Provides status management functionality for tools that require process state tracking.
 * Includes a predefined list of allowed status values and a method to set and persist them.
 *
 * @package Ilove_Pdf_WP\Tools\Base
 */
trait Status_Process {
	/**
     * List of allowed status values for the process.
     *
     * Possible values:
     * - 'in_process' → the process is currently running
     * - 'ready' → the process completed successfully
     * - 'error' → the process failed
     *
     * @var string[]
     */
    private $allowed_status = array(
        'in_process',
		'ready',
		'error',
    );

    /**
     * Retrieves the 'ready' status value.
     *
     * @return string
     */
    protected function get_ready_status() {
        return $this->allowed_status[1];
    }

    /**
     * Sets the status 'in_process' and saves it in the database under the provided key.
     *
     * Also records a start timestamp so the auto-reset sweep can distinguish genuinely
     * in-flight rows from orphaned ones left by an interrupted batch.
     *
     * @param int    $file_id File ID.
     * @param string $db_key Key where the status will be stored in the database.
     */
    protected function set_status_in_process( int $file_id, string $db_key ) {
        update_post_meta( $file_id, $db_key, $this->allowed_status[0] );
        update_post_meta( $file_id, $db_key . '_started', time() );
    }

    /**
     * Sets the status 'ready' and saves it in the database under the provided key.
     *
     * Deletes the companion start timestamp set by set_status_in_process().
     *
     * @param int    $file_id File ID.
     * @param string $db_key Key where the status will be stored in the database.
     */
    protected function set_status_ready( $file_id, $db_key ) {
        update_post_meta( $file_id, $db_key, $this->allowed_status[1] );
        delete_post_meta( $file_id, $db_key . '_started' );
    }

    /**
     * Sets the status 'error' and saves it in the database under the provided key.
     *
     * Deletes the companion start timestamp set by set_status_in_process().
     *
     * @param int    $file_id File ID.
     * @param string $db_key Key where the status will be stored in the database.
     */
    protected function set_status_error( int $file_id, string $db_key ) {
        update_post_meta( $file_id, $db_key, $this->allowed_status[2] );
        delete_post_meta( $file_id, $db_key . '_started' );
    }

    /**
     * Verifies the AJAX request nonce, capabilities, and post ID, then returns the sanitized post ID.
     *
     * Performs, in order:
     *  1. Nonce verification (401 on failure).
     *  2. `upload_files` capability check (403 on failure).
     *  3. `post_id` presence check (400 on failure).
     *  4. `absint` sanitization and >= 1 range check (400 on failure).
     *  5. `edit_post` capability check for the specific post (403 on failure).
     *
     * Terminates via wp_send_json_error on any failure — identical to the per-handler
     * guards this replaces. Returns the sanitized int post ID on success.
     *
     * @param string $nonce_action The nonce action string to verify against.
     * @return int Sanitized post ID (>= 1).
     */
    protected function verify_request_or_die( string $nonce_action ): int {
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $nonce_action ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            wp_send_json_error( _x( 'Couldn\'t complete the request. Please refresh and try again.', 'Error message, invalid nonce code.', 'ilove-pdf' ), 401 );
        }

        if ( ! current_user_can( 'upload_files' ) ) {
            wp_send_json_error(
                __( 'You do not have permission to process files.', 'ilove-pdf' ),
                403
            );
        }

        if ( ! isset( $_POST['post_id'] ) ) {
            wp_send_json_error( __( 'Could not process the request. Missing file ID.', 'ilove-pdf' ), 400 );
        }

        $post_id = absint( $_POST['post_id'] );

        if ( $post_id < 1 ) {
            wp_send_json_error( __( 'Could not process the request. Invalid file ID.', 'ilove-pdf' ), 400 );
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( __( 'You do not have permission to edit this file.', 'ilove-pdf' ), 403 );
        }

        return $post_id;
    }

    /**
     * Resolves the current status of a single attachment, performing a per-attachment stale check.
     *
     * If the status is 'in_process' and the companion _started timestamp is missing or older
     * than IPDF_PROCESS_TIMEOUT seconds, the stale meta is cleared (returning the attachment
     * to a retriable, never-processed state) and an empty string is returned. A fresh in-flight
     * row is left untouched and 'in_process' is returned. For all other statuses the stored
     * value is returned unchanged.
     *
     * This method is intentionally scoped to a single attachment (O(1)) so it is safe to call
     * on every poll request. It NEVER starts, re-runs, or triggers the processing pipeline.
     *
     * @param int    $file_id The attachment post ID.
     * @param string $db_key  The post meta key used to store the process status.
     * @return string Resolved status: 'in_process', 'ready', 'error', or '' (never-processed / stale-reset).
     */
    protected function resolve_status( int $file_id, string $db_key ): string {
        $status      = (string) get_post_meta( (int) $file_id, $db_key, true );
        $started_key = $db_key . '_started';

        if ( 'in_process' === $status ) {
            $started = (int) get_post_meta( (int) $file_id, $started_key, true );
            // Guard: a fresh timestamp means a live request — leave it alone.
            if ( $started && ( time() - $started ) < IPDF_PROCESS_TIMEOUT ) {
                return 'in_process';
            }
            // Timestamp missing or stale: clear the orphaned meta and treat as never-processed.
            delete_post_meta( (int) $file_id, $db_key );
            delete_post_meta( (int) $file_id, $started_key );
            return '';
        }

        return $status;
    }

    /**
     * Resets orphaned 'in_process' PDF attachments back to their pristine/retriable state.
     *
     * Queries PDF attachments whose status meta equals 'in_process' and clears the status
     * meta (and the companion _started timestamp) for any that have a missing or stale
     * timestamp — i.e. rows not associated with a genuinely in-flight request. Rows whose
     * _started timestamp is fresher than IPDF_PROCESS_TIMEOUT seconds are left untouched
     * so a live browser-driven request is never interrupted by a concurrent page load.
     *
     * Deleting the status meta returns the attachment to the same pre-processing state as
     * a never-processed file, which the UI already treats as retriable.
     *
     * @param string $db_key The post meta key used to store the process status.
     */
    protected function reset_stale_in_process( $db_key ) {
        $started_key = $db_key . '_started';

        $ids = get_posts(
            array(
                'post_type'      => 'attachment',
                'post_mime_type' => 'application/pdf',
                'fields'         => 'ids',
                'posts_per_page' => 500, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
                'no_found_rows'  => true,
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
                'meta_query'     => array(
                    array(
                        'key'   => $db_key,
                        'value' => $this->allowed_status[0],
                    ),
                ),
            )
        );

        foreach ( $ids as $id ) {
            $post_id = (int) $id;
            $started = (int) get_post_meta( $post_id, $started_key, true );
            // Guard: skip genuinely in-flight rows — a fresh timestamp means a live request.
            if ( $started && ( time() - $started ) < IPDF_PROCESS_TIMEOUT ) {
                continue;
            }
            delete_post_meta( $post_id, $db_key );
            delete_post_meta( $post_id, $started_key );
        }
    }
}
