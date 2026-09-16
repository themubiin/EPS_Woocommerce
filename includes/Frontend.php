<?php
namespace MCoder\EPS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Frontend handler class
 */
class Frontend {

    /**
     * Initialize the class
     */
    function __construct() {
        new Frontend\Shortcode();
    }
}
