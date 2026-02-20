<?php


add_shortcode('mechanic_booking', 'render_mechanic_booking');

function render_mechanic_booking()
{
    ob_start();

    if (isset($_POST['booking_submit'])) {

        if (!isset($_POST['nonce_front']) || !wp_verify_nonce($_POST['nonce_front'], 'save_booking_front')) {
            echo '<p style="color: red;">Errore di sicurezza. Riprova.</p>';
        } else {
            $name = sanitize_text_field($_POST['name']);
            $place = sanitize_text_field($_POST['place']);
            $plate = sanitize_text_field($_POST['plate']);
            $type_booking = sanitize_text_field($_POST['type_booking']);
            $date = sanitize_text_field($_POST['date']);
            $hour = sanitize_text_field($_POST['hour']);
            $minutes = sanitize_text_field($_POST['minutes']);

            $booking_data = array(
                'post_title' => $name,
                'post_type' => 'booking',
                'post_status' => 'publish',
            );

            $booking_id = wp_insert_post($booking_data);

            if ($booking_id) {
                update_post_meta($booking_id, '_place', $place);
                update_post_meta($booking_id, '_plate', $plate);
                update_post_meta($booking_id, '_type_booking', $type_booking);
                update_post_meta($booking_id, '_date', $date);
                update_post_meta($booking_id, '_hour', $hour);
                update_post_meta($booking_id, '_minutes', $minutes);

                echo '<p class="success-message" style="color: green;">Prenotazione salvata con successo!</p>';
                return ob_get_clean();
            } else {
                echo '<p style="color: red;">Errore durante il salvataggio della prenotazione. Riprova.</p>';
            }
        }
    }

    ?>

    <style>
        .booking-form-box { max-width: 500px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        .booking-form-group { margin-bottom: 15px; }
        .booking-form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .booking-form-group input, .booking-form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .booking-btn { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
        .booking-btn:hover { background: #005177; }
    </style>

    <div class="booking-form-box">
        <form action="" method="POST">
            <?php wp_nonce_field('save_booking_front', 'nonce_front'); ?>

            <div class="booking-form-group">
                <label for="name">Nome e Cognome *</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="booking-form-group">
                <label for="place">Sede Officina *</label>
                <select id="place" name="place" required>
                    <option value="">-- Seleziona la sede --</option>
                    <option value="Cerro Maggiore">Cerro Maggiore</option>
                    <option value="Cantalupo">Cantalupo</option>
                </select>
            </div>

            <div class="booking-form-group">
                <label for="plate">Targa Veicolo *</label>
                <input type="text" id="plate" name="plate" placeholder="es. AB123CD" required>
            </div>

            <div class="booking-form-group">
                <label for="modello">Modello Auto *</label>
                <input type="text" id="modello" name="modello" placeholder="es. Fiat Panda" required>
            </div>

            <div class="booking-form-group">
                <label for="type_booking">Intervento Richiesto *</label>
                <select id="type_booking" name="type_booking" required>
                    <option value="">-- Seleziona --</option>
                    <option value="Tagliando">Tagliando</option>
                    <option value="Cambio Gomme">Cambio Gomme</option>
                    <option value="Revisione">Revisione</option>
                    <option value="Riparazione Meccanica">Riparazione Meccanica</option>
                </select>
            </div>

            <div class="booking-form-group">
                <label for="date">Data Appuntamento *</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="booking-form-group">
                <label for="hour">Ora Appuntamento *</label>
                <input type="hour" id="hour" name="hour" required>
            </div>
                <div class="booking-form-group">
                    <label for="minutes">Durata Intervento (minuti) *</label>
                    <input type="number" id="minutes" name="minutes" min="1" required>
                </div>

            <button type="submit" name="booking_submit" class="booking-btn">Richiedi Appuntamento</button>
        </form>
    </div>

    <?php
    return ob_get_clean();
}