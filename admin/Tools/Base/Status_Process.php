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
    protected function set_status_in_process( $file_id, $db_key ) {
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
    protected function set_status_error( $file_id, $db_key ) {
        update_post_meta( $file_id, $db_key, $this->allowed_status[2] );
        delete_post_meta( $file_id, $db_key . '_started' );
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
