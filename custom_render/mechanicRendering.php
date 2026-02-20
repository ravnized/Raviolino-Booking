<?php

add_action( 'add_meta_boxes', 'add_meta_box_booking' );
function add_meta_box_booking() {
    add_meta_box(
        'booking_details',       
        'Booking Details',        
        'render_meta_box_booking',
        'booking',                         
        'normal',                                
        'high'                                   
    );
}

function render_meta_box_booking( $post ) {
    wp_nonce_field( 'booking_save_data', 'booking_nonce' );
    $id = $post->ID;
    $name = get_post_meta( $post->ID, '_name', true );
    $place = get_post_meta( $post->ID, '_place', true );
    $plate = get_post_meta( $post->ID, '_plate', true );
    $type_booking = get_post_meta( $post->ID, '_type_booking', true );
    $date = get_post_meta( $post->ID, '_date', true );
    $hour = get_post_meta( $post->ID, '_hour', true );
    $minutes = get_post_meta( $post->ID, '_minutes', true );

   echo '<style>
        .booking-row { margin-bottom: 15px; }
        .booking-row label { display: inline-block; width: 150px; font-weight: bold; }
        .booking-row input, .booking-row select { width: 100%; max-width: 300px; padding: 5px; }
    </style>';

    // Disegniamo i campi
    ?>
    <div class="booking-row">
        <label for="booking_id">ID Prenotazione:</label>
        <input type="text" id="booking_id" value="<?php echo esc_attr( $id ); ?>" readonly />
    </div>

    <div class="booking-row">
        <label for="booking_name">Nome Cliente:</label>
        <input type="text" id="booking_name" name="booking_name" value="<?php echo esc_attr( $name ); ?>" placeholder="es. Mario Rossi" />
    </div>

    <div class="booking-row">
        <label for="booking_place">Sede Officina:</label>
        <select id="booking_place" name="booking_place">
            <option value="Cerro Maggiore" <?php selected( $place, 'Cerro Maggiore' ); ?>>Cerro Maggiore</option>
            <option value="Cantalupo" <?php selected( $place, 'Cantalupo' ); ?>>Cantalupo</option>
        </select>
    </div>

    <div class="booking-row">
        <label for="booking_plate">Targa:</label>
        <input type="text" id="booking_plate" name="booking_plate" value="<?php echo esc_attr( $plate ); ?>" placeholder="es. AB123CD" />
    </div>

    <div class="booking-row">
        <label for="booking_type_booking">Tipo Intervento:</label>
        <select id="booking_type_booking" name="booking_type_booking">
            <option value="Tagliando" <?php selected( $type_booking, 'Tagliando' ); ?>>Tagliando</option>
            <option value="Cambio Gomme" <?php selected( $type_booking, 'Cambio Gomme' ); ?>>Cambio Gomme</option>
            <option value="Revisione" <?php selected( $type_booking, 'Revisione' ); ?>>Revisione</option>
            <option value="Riparazione Meccanica" <?php selected( $type_booking, 'Riparazione Meccanica' ); ?>>Riparazione Meccanica</option>
        </select>
    </div>

    <div class="booking-row">
        <label for="booking_date">Data Appuntamento:</label>
        <input type="date" id="booking_date" name="booking_date" value="<?php echo esc_attr( $date ); ?>" />
    </div>

    <div class="booking-row">
        <label for="booking_hour">Ora Appuntamento:</label>
        <input type="text" id="booking_hour" name="booking_hour" value="<?php echo esc_attr( $hour ); ?>" placeholder="es. 10" />
    </div>

    <div class="booking-row">
        <label for="booking_minutes">Minuti:</label>
        <input type="number" id="booking_minutes" name="booking_minutes" value="<?php echo esc_attr( $minutes ); ?>" min="0" max="59" />
    </div>
    
    <?php
}


add_action( 'save_post', 'save_data_booking' );
function save_data_booking( $post_id ) {
    
    if ( ! isset( $_POST['booking_nonce'] ) || ! wp_verify_nonce( $_POST['booking_nonce'], 'booking_save_data' ) ) {
        return;
    }

    
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    if ( isset( $_POST['booking_place'] ) ) {
        update_post_meta( $post_id, '_place', sanitize_text_field( $_POST['booking_place'] ) );
    }
    if ( isset( $_POST['booking_plate'] ) ) {
        update_post_meta( $post_id, '_plate', sanitize_text_field( $_POST['booking_plate'] ) );
    }
    if ( isset( $_POST['booking_type_booking'] ) ) {
        update_post_meta( $post_id, '_type_booking', sanitize_text_field( $_POST['booking_type_booking'] ) );
    }
    
    if ( isset( $_POST['booking_date'] ) ) {
        update_post_meta( $post_id, '_date', sanitize_text_field( $_POST['booking_date'] ) );
    }
    if ( isset( $_POST['booking_hour'] ) ) {
        update_post_meta( $post_id, '_hour', sanitize_text_field( $_POST['booking_hour'] ) );
    }
    if ( isset( $_POST['booking_minutes'] ) ) {
        update_post_meta( $post_id, '_minutes', sanitize_text_field( $_POST['booking_minutes'] ) );
    }
}