<?php
// filepath: wp-calendar/admin/admin-page.php


function calendar_events_admin_menu() {
    add_menu_page(
        'Calendar Events',
        'Calendar Events',
        'manage_options',
        'calendar-events-admin',
        'calendar_events_admin_page',
        'dashicons-calendar-alt',
        26
    );

}
add_action('admin_menu', 'calendar_events_admin_menu');


function calendar_events_admin_page() {
    ?>
        
        <!-- Modal for adding event -->
        <div id="eventModal"  >
            <div>
                <form id="eventForm">
                    <h1 class="admin-header">Calendar Events</h1>
                    <input type="hidden" id="event_date" name="event_date" />
                    <div>
                        <label for="event_title"></label>
                        <input type="text" id="event_title" name="event_title" required placeholder="Add title" />
                    </div>
                    <div id="selectedDay">
                        <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                        <span id="selectedDayText"></span>
                    </div>
                    <div>
                        <label for="event_description">Description:</label>
                        <textarea id="event_description" name="event_description"></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="pill-btn">Save</button>
                        <button type="button" id="closeModal">Cancel</button> 
                        <!-- when clicked clear the inputs -->
                    </div>
                </form>
            </div>
            <!-- add here a list of events  -->
        </div>
    <?php
}

function calendar_events_admin_enqueue_assets($hook) {
    
    if ($hook === 'toplevel_page_calendar-events-admin') {
        wp_enqueue_style(
            'calendar-events-css',
            plugin_dir_url(__DIR__) . 'assets/CSS/calendar.css'
        );
        wp_enqueue_script(
            'admin-calendar-events-js',
            plugin_dir_url(__DIR__) . 'assets/js/admin-calendar.js',
            array('jquery'),
            false,
            true
        );
        wp_localize_script('admin-calendar-events-js', 'calendarEventsAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
add_action('admin_enqueue_scripts', 'calendar_events_admin_enqueue_assets');
