<?php

// Aggiungi la pagina di impostazioni del plugin al menu di amministrazione
add_action('admin_menu', 'booking_add_admin_page');
function booking_add_admin_page() {
    add_submenu_page(
        'edit.php?post_type=booking',
        'Impostazioni',
        'Impostazioni',
        'manage_options',
        'booking_settings',
        'booking_render_settings_page'
    );
}

// Registra le impostazioni
add_action('admin_init', 'booking_register_settings');
function booking_register_settings() {
    register_setting('booking_group', 'booking_options');
    
    add_settings_section(
        'booking_section',
        'Impostazioni Orari e Giorni di Chiusura',
        'booking_section_callback',
        'booking'
    );
}

// Callback per la sezione
function booking_section_callback() {
    echo 'Configura gli orari di apertura e i giorni di chiusura di tutte le sedi';
}

// Renderizza la pagina di impostazioni
function booking_render_settings_page() {
    $options = get_option('booking_options', array());
    
    // Imposta i valori di default se non esistono
    $defaults = array(
        'closed_days' => array(0), // domenica
        'closed_dates' => '', // date formato YYYY-MM-DD separate da virgola
        'morning_start' => '08:00',
        'morning_end' => '12:00',
        'afternoon_start' => '14:00',
        'afternoon_end' => '18:00',
        'lunch_break_start' => '12:00',
        'lunch_break_end' => '14:00',
    );
    
    $options = wp_parse_args($options, $defaults);
    
    $days_of_week = array(
        0 => 'Domenica',
        1 => 'Lunedì',
        2 => 'Martedì',
        3 => 'Mercoledì',
        4 => 'Giovedì',
        5 => 'Venerdì',
        6 => 'Sabato'
    );
    
    ?>
    <div class="wrap">
        <h1>Raviolino Booking - Impostazioni</h1>
        
        <form method="post" action="options.php">
            <?php settings_fields('booking_group'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="closed_days">Giorni di Chiusura:</label>
                    </th>
                    <td>
                        <fieldset>
                            <?php foreach ($days_of_week as $day_num => $day_name): ?>
                                <label>
                                    <input type="checkbox" name="booking_options[closed_days][]" 
                                           value="<?php echo $day_num; ?>"
                                           <?php checked(in_array($day_num, (array)$options['closed_days'])); ?> />
                                    <?php echo $day_name; ?>
                                </label><br />
                            <?php endforeach; ?>
                        </fieldset>
                        <p class="description">Seleziona i giorni della settimana in cui l'officina è chiusa</p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">
                        <label for="closed_dates">Festivi e Date di Chiusura:</label>
                    </th>
                    <td>
                        <textarea name="booking_options[closed_dates]" 
                                  rows="4" cols="50"
                                  placeholder="Inserisci le date in formato YYYY-MM-DD, separate da virgole o con un'a capo per riga"><?php echo esc_textarea($options['closed_dates']); ?></textarea>
                        <p class="description">Es: 2024-12-25, 2024-01-01, 2025-04-25</p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row" colspan="2">
                        <h2 style="margin-top: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Orari di Apertura</h2>
                    </th>
                </tr>
                
                <tr valign="top">
                    <th scope="row">
                        <label for="morning_start">Apertura Mattino:</label>
                    </th>
                    <td>
                        <input type="time" name="booking_options[morning_start]" 
                               value="<?php echo esc_attr($options['morning_start']); ?>" />
                        <p class="description">Ora di apertura del mattino</p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">
                        <label for="lunch_break_start">Inizio Pausa Pranzo:</label>
                    </th>
                    <td>
                        <input type="time" name="booking_options[lunch_break_start]" 
                               value="<?php echo esc_attr($options['lunch_break_start']); ?>" />
                        <p class="description">Inizio della pausa pranzo (fine mattino)</p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">
                        <label for="lunch_break_end">Fine Pausa Pranzo:</label>
                    </th>
                    <td>
                        <input type="time" name="booking_options[lunch_break_end]" 
                               value="<?php echo esc_attr($options['lunch_break_end']); ?>" />
                        <p class="description">Fine della pausa pranzo (inizio pomeriggio)</p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">
                        <label for="afternoon_end">Chiusura Pomeriggio:</label>
                    </th>
                    <td>
                        <input type="time" name="booking_options[afternoon_end]" 
                               value="<?php echo esc_attr($options['afternoon_end']); ?>" />
                        <p class="description">Ora di chiusura del pomeriggio</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Salva Impostazioni'); ?>
        </form>
        
        <div style="margin-top: 30px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
            <h3>Riepilogo Impostazioni Attuali:</h3>
            <p><strong>Giorni chiusi:</strong> <?php 
                $closed_day_names = array_map(function($day) use ($days_of_week) {
                    return $days_of_week[$day];
                }, (array)$options['closed_days']);
                echo implode(', ', $closed_day_names);
            ?></p>
            <p><strong>Orari mattino:</strong> <?php echo $options['morning_start']; ?> - <?php echo $options['lunch_break_start']; ?></p>
            <p><strong>Pausa pranzo:</strong> <?php echo $options['lunch_break_start']; ?> - <?php echo $options['lunch_break_end']; ?></p>
            <p><strong>Orari pomeriggio:</strong> <?php echo $options['lunch_break_end']; ?> - <?php echo $options['afternoon_end']; ?></p>
            <?php if (!empty($options['closed_dates'])): ?>
                <p><strong>Festivi/Date chiuse:</strong> <?php echo esc_html($options['closed_dates']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// Funzione helper per ottenere le impostazioni
function raviolino_booking_get_settings() {
    $options = get_option('booking_options', array());
    
    $defaults = array(
        'closed_days' => array(0),
        'closed_dates' => '',
        'morning_start' => '08:00',
        'morning_end' => '12:00',
        'afternoon_start' => '14:00',
        'afternoon_end' => '18:00',
        'lunch_break_start' => '12:00',
        'lunch_break_end' => '14:00',
    );
    
    return wp_parse_args($options, $defaults);
}

// Funzione per verificare se una data è un giorno feriale
function raviolino_booking_is_working_day($date_string) {
    $date = new DateTime($date_string);
    $settings = raviolino_booking_get_settings();
    
    $day_of_week = (int)$date->format('w');
    
    // Controlla se è un giorno chiuso
    if (in_array($day_of_week, (array)$settings['closed_days'])) {
        return false;
    }
    
    // Controlla se è una data festiva
    $closed_dates = array_map('trim', explode(',', $settings['closed_dates']));
    $closed_dates = array_filter($closed_dates);
    
    if (in_array($date_string, $closed_dates)) {
        return false;
    }
    
    return true;
}

// Funzione per ottenere gli slot orari disponibili per un giorno
function raviolino_booking_get_time_slots($date_string, $interval = 30) {
    $settings = raviolino_booking_get_settings();
    
    // Controlla se il giorno è lavorativo
    if (!raviolino_booking_is_working_day($date_string)) {
        return array();
    }
    
    $slots = array();
    $interval_minutes = intval($interval);
    
    // Slot mattino
    $morning_start = DateTime::createFromFormat('H:i', $settings['morning_start']);
    $morning_end = DateTime::createFromFormat('H:i', $settings['lunch_break_start']);
    
    if ($morning_start && $morning_end) {
        while ($morning_start < $morning_end) {
            $slots[] = $morning_start->format('H:i');
            $morning_start->modify("+{$interval_minutes} minutes");
        }
    }
    
    // Slot pomeriggio
    $afternoon_start = DateTime::createFromFormat('H:i', $settings['lunch_break_end']);
    $afternoon_end = DateTime::createFromFormat('H:i', $settings['afternoon_end']);
    
    if ($afternoon_start && $afternoon_end) {
        while ($afternoon_start < $afternoon_end) {
            $slots[] = $afternoon_start->format('H:i');
            $afternoon_start->modify("+{$interval_minutes} minutes");
        }
    }
    
    return $slots;
}
