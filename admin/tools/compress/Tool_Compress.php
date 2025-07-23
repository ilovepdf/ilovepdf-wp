<?php

namespace Ilove_Pdf_WP;

/**
 * Manages the compression process.
 *
 * @package Ilove_Pdf_WP
 * @since 3.0.0
 */
class Tool_Compress {
    /**
     * Post meta key for tracking the compression status of an attachment.
     *
     * @var string
     * @since 3.0.0
     */
    public $db_key_status = '_ipdf_attachment_compress_status';


    /**
     * Post meta key for storing the compression process ID or reference.
     *
     * @var string
     * @since 3.0.0
     */
    public $db_key_process = '_ipdf_attachment_compress_process';
}
