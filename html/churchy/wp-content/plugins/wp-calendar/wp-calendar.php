<?php

/*
Plugin Name: Calendar Events
Description: Simple event calendar with database table and AJAX fetch.
Version: 1.0
Author: Pedro Echavarria
*/

// Create table on plugin activation
register_activation_hook(__FILE__, 'calendar_events_create_table');

function calendar_events_create_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'events';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        event_date DATE NOT NULL,
        end_date DATE DEFAULT NULL,
        description TEXT
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Shortcode to display the calendar
add_shortcode('calendar_events', 'calendar_events_shortcode');
function calendar_events_shortcode() {
    ob_start();
    ?>
    <div id="calendar"></div>    
    <!-- ✅ Modal for viewing event -->
    <div id="viewEventModal" style="display: none;">
    <h2 id="viewEventTitle"></h2>
    <p id="viewEventDate"></p>
    <p id="viewEventDescription"></p>
    <button id="closeViewModal">Close</button>
    </div>
    <!-- FullCalendar & JS includes below -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://kit.fontawesome.com/a329bb9f5d.js" crossorigin="anonymous"></script>
    
    <?php
    return ob_get_clean();
}
function calendar_events_enqueue_assets() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'calendar_events')) {

        wp_enqueue_style(
            'calendar-events-css',
            plugins_url('assets/CSS/calendar.css', __FILE__)
        );
        wp_enqueue_script(
            'calendar-events-js',
            plugins_url('assets/js/calendar.js', __FILE__),
            array('jquery'), // dependencies
            false,
            true
        );
        // Pass ajaxurl to JS
        wp_localize_script('calendar-events-js', 'calendarEventsAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
add_action('wp_enqueue_scripts', 'calendar_events_enqueue_assets');

require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';
