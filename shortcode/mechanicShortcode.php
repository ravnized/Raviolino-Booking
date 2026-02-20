<?php


add_shortcode('mechanic_booking', 'render_mechanic_booking');


// AJAX handler per ottenere le date con prenotazioni
add_action('wp_ajax_get_booking_dates', 'get_booking_dates');
add_action('wp_ajax_nopriv_get_booking_dates', 'get_booking_dates');

function get_booking_dates() {
    check_ajax_referer('booking_availability_nonce', 'nonce');
    
    $place = sanitize_text_field($_POST['place']);
    
    // Query per trovare tutte le prenotazioni per quella sede
    $args = array(
        'post_type' => 'booking',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_place',
                'value' => $place,
                'compare' => '='
            )
        )
    );
    
    $bookings = get_posts($args);
    $dates_info = array();
    
    foreach ($bookings as $booking) {
        $date = get_post_meta($booking->ID, '_date', true);
        
        if (!isset($dates_info[$date])) {
            $dates_info[$date] = 0;
        }
        $dates_info[$date]++;
    }

    error_log('Date info for place ' . $place . ': ' . print_r($dates_info, true));
    
    wp_send_json_success(array('dates_info' => $dates_info));
}

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
            $minutes = 30; // Durata fissa di 30 minuti per prenotazioni utente

            // Verifica che l'orario sia ancora disponibile
            $args = array(
                'post_type' => 'booking',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => '_date',
                        'value' => $date,
                        'compare' => '='
                    ),
                    array(
                        'key' => '_place',
                        'value' => $place,
                        'compare' => '='
                    )
                )
            );
            
            $existing_bookings = get_posts($args);
            $is_available = true;
            
            $new_start = strtotime($hour);
            $new_end = $new_start + (intval($minutes) * 60);
            
            foreach ($existing_bookings as $booking) {
                $existing_hour = get_post_meta($booking->ID, '_hour', true);
                $existing_minutes = intval(get_post_meta($booking->ID, '_minutes', true));
                
                $existing_start = strtotime($existing_hour);
                $existing_end = $existing_start + ($existing_minutes * 60);
                
                // Controlla sovrapposizione
                if (!($new_end <= $existing_start || $new_start >= $existing_end)) {
                    $is_available = false;
                    break;
                }
            }
            
            if (!$is_available) {
                echo '<p style="color: red;">Spiacenti, l\'orario selezionato non è più disponibile. Ricarica la pagina e scegli un altro orario.</p>';
                return ob_get_clean();
            }

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
        select option:disabled { background: #f0f0f0; color: #999; }
        .time-slot-occupied { background: #ffcccc !important; color: #cc0000 !important; }
        .loading-message { color: #0073aa; font-style: italic; }
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
                <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>" disabled>
            </div>
            
            <div class="booking-form-group">
                <label for="hour">Ora di Arrivo *</label>
                <select id="hour" name="hour" required disabled>
                    <option value="">-- Prima seleziona data e sede --</option>
                </select>
            </div>

            <button type="submit" name="booking_submit" class="booking-btn">Richiedi Appuntamento</button>
        </form>
    </div>
        <script
			  src="https://code.jquery.com/jquery-4.0.0.min.js"
			  integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao="
			  crossorigin="anonymous"></script>
    <script>
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var nonce = '<?php echo wp_create_nonce('booking_availability_nonce'); ?>';
        
        // Genera tutti gli orari disponibili (08:00 - 18:00, ogni 30 minuti)
        function generateTimeSlots() {
            var slots = [];
            for (var h = 8; h <= 17; h++) {
                for (var m = 0; m < 60; m += 30) {
                    if (h === 17 && m === 30) break; // Stop at 17:30
                    var hour = h.toString().padStart(2, '0');
                    var min = m.toString().padStart(2, '0');
                    slots.push(hour + ':' + min);
                }
            }
            return slots;
        }
        
        // Popola il select dell'ora
        function populateHourSelect(occupiedSlots) {
            var $hourSelect = $('#hour');
            $hourSelect.empty();
            $hourSelect.append('<option value="">-- Seleziona ora --</option>');
            
            var allSlots = generateTimeSlots();
            occupiedSlots = occupiedSlots || [];
            
            allSlots.forEach(function(slot) {
                var isOccupied = occupiedSlots.indexOf(slot) !== -1;
                var option = $('<option></option>')
                    .val(slot)
                    .text(slot + (isOccupied ? ' (Non disponibile)' : ''));
                
                if (isOccupied) {
                    option.prop('disabled', true);
                    option.addClass('time-slot-occupied');
                }
                
                $hourSelect.append(option);
            });
            
            $hourSelect.prop('disabled', false);
        }
        
        // Verifica disponibilità quando cambiano data o sede

        function checkAvailabilityDate(){
            var place = $('#place').val();
            if (!place) {
                $('#hour').empty().append('<option value="">-- Prima seleziona la sede --</option>').prop('disabled', true);
                return;
            }
            
            $.post(ajaxurl, {
                action: 'get_booking_dates',
                nonce: nonce,
                place: place
            }, function(response) {
                if (response.success) {
                    var datesInfo = response.data.dates_info;
                    // Puoi usare datesInfo per evidenziare le date con molte prenotazioni
                    // Ad esempio, potresti disabilitare le date con più di 5 prenotazioni
                    // o mostrare un messaggio accanto alla data
                }
            });
        }



        
        
        // Event listeners
        $('#place').on('change', checkAvailabilityDate);
        
        // Disabilita le domeniche e le date passate
        var today = new Date().toISOString().split('T')[0];
        $('#date').attr('min', today);
        
        $('#date').on('input', function() {
            var selectedDate = new Date($(this).val());
            var day = selectedDate.getDay();
            
            // Se è domenica (0), mostra alert e resetta
            if (day === 0) {
                alert('La domenica l\'officina è chiusa. Seleziona un altro giorno.');
                $(this).val('');
            }
        });
    });
    </script>

    <?php
    return ob_get_clean();
}