<?php




function register_booking() {
    $labels = array(
        'name'          => 'Bookings',
        'singular_name' => 'Booking',
        'add_new'       => 'Add Booking',
        'add_new_item'  => 'Add New Booking',
        'edit_item'     => 'Edit Booking',
        'all_items'     => 'All Bookings',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => false,
        'show_ui'       => true,
        'menu_position' => 20,
        'menu_icon'     => 'dashicons-calendar-alt',
        'supports'      => array(),
        'has_archive'   => false,
    );

    register_post_type( 'booking', $args );
}

add_action( 'init', 'disable_booking_editor_fields', 20 );
function disable_booking_editor_fields() {
    remove_post_type_support( 'booking', 'title' );
    remove_post_type_support( 'booking', 'editor' );    
}