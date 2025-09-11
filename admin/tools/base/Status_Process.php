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
     * @param int    $file_id File ID.
     * @param string $db_key Key where the status will be stored in the database.
     */
    protected function set_status_in_process( $file_id, $db_key ) {
        update_post_meta( $file_id, $db_key, $this->allowed_status[0] );
    }

    /**
     * Sets the status 'ready' and saves it in the database under the provided key.
     *
     * @param int    $file_id File ID.
     * @param string $db_key Key where the status will be stored in the database.
     */
    protected function set_status_ready( $file_id, $db_key ) {
        update_post_meta( $file_id, $db_key, $this->allowed_status[1] );
    }

    /**
     * Sets the status 'error' and saves it in the database under the provided key.
     *
     * @param int    $file_id File ID.
     * @param string $db_key Key where the status will be stored in the database.
     */
    protected function set_status_error( $file_id, $db_key ) {
        update_post_meta( $file_id, $db_key, $this->allowed_status[2] );
    }
}
