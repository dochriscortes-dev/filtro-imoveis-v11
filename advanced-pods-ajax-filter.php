<?php
/**
 * Plugin Name: Advanced Pods AJAX Filter
 * Description: V11 - Advanced filtering for 'imovel' post type with Pods.
 * Version: 11.0
 * Author: Jules
 * Text Domain: advanced-pods-ajax-filter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define Plugin Constants
define( 'APAF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APAF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include AJAX Handler
require_once APAF_PLUGIN_DIR . 'includes/ajax-handler.php';

/**
 * Enqueue Scripts and Styles
 */
function apaf_enqueue_scripts() {
    // Enqueue jQuery
    wp_enqueue_script( 'jquery' );

    // Enqueue jQuery UI Slider
    wp_enqueue_script( 'jquery-ui-slider' );
    wp_enqueue_style( 'jquery-ui-style', 'https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css' );

    // Enqueue Select2
    wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
    wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );

    // Custom CSS
    wp_enqueue_style( 'apaf-style', APAF_PLUGIN_URL . 'assets/css/style.css', array(), '11.0' );

    // Custom JS
    wp_enqueue_script( 'apaf-script', APAF_PLUGIN_URL . 'assets/js/script.js', array( 'jquery', 'jquery-ui-slider', 'select2-js' ), '11.0', true );

    // Localize Script for AJAX
    wp_localize_script( 'apaf-script', 'apaf_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'apaf_filter_nonce' )
    ));
}
add_action( 'wp_enqueue_scripts', 'apaf_enqueue_scripts' );

/**
 * Register Shortcode
 */
function apaf_shortcode_callback( $atts ) {
    ob_start();
    include APAF_PLUGIN_DIR . 'includes/filter-template.php';
    return ob_get_clean();
}
add_shortcode( 'advanced_pods_filter', 'apaf_shortcode_callback' );
