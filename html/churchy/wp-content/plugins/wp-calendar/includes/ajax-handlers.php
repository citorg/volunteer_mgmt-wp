<?php
add_action('wp_ajax_calendar_events_fetch', 'calendar_events_fetch');
add_action('wp_ajax_nopriv_calendar_events_fetch', 'calendar_events_fetch');
function calendar_events_fetch() {
        global $wpdb;
    $table = $wpdb->prefix . 'events';
    $results = $wpdb->get_results("
        SELECT
            id,
            title, 
            event_date as start, 
            end_date as end, 
            description  
        FROM $table", 
        ARRAY_A);
    wp_send_json($results);
}

add_action('wp_ajax_calendar_events_add', 'calendar_events_add');
add_action('wp_ajax_nopriv_calendar_events_add', 'calendar_events_add');
function calendar_events_add() {
    global $wpdb;
    $table = $wpdb->prefix . 'events';
    $title = sanitize_text_field($_POST['title']);
    $description = sanitize_textarea_field($_POST['description']);
    $event_date = sanitize_text_field($_POST['event_date']);
    $end_date = $event_date;
    $result = $wpdb->insert($table, [
        'title' => $title,
        'event_date' => $event_date,
        'end_date' => $end_date,
        'description' => $description
    ]);
    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}