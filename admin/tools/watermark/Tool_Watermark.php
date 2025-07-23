<?php

namespace Ilove_Pdf_WP\Watermark;

/**
 * Manages the watermark process.
 *
 * @package Ilove_Pdf_WP
 * @since 3.0.0
 */
class Tool_Watermark {
    /**
     * Post meta key for tracking the watermark status of an attachment.
     *
     * @var string
     * @since 3.0.0
     */
    public $db_key_status = '_ipdf_attachment_watermark_status';
}
