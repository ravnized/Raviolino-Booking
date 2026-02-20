<?php
/**
 * Plugin Name: Raviolino-Booking
 * Description: A booking plugin for WordPress.
 * Version: 1.0.0
 * Author: Ravnized
 * License: GPL3
 */



if(!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

function log_message($message) {
    if (WP_DEBUG === true) {
        error_log($message);
    }
}


//import custom types from the custom_types folder and the functions from the functions folder
require_once plugin_dir_path(__FILE__) . 'custom_types/mechanicBooking.php';


add_action( 'init', 'register_booking' );

// log message 
log_message('Raviolino-Booking plugin loaded successfully.');