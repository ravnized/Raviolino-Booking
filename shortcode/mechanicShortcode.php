<?php

add_shortcode( 'mechanic_booking', 'render_mechanic_booking' );

function render_mechanic_booking() {
    ob_start();
    include plugin_dir_path(__FILE__) . '../templates/mechanicBookingTemplate.php';
    return ob_get_clean();
}