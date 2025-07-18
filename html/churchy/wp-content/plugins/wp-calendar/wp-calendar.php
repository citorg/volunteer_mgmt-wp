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
    <!-- Modal for adding event -->
    <div id="eventModal">
        <div>
            <h3>Add Event</h3>
            <form id="eventForm">
                <input type="hidden" id="event_date" name="event_date" />
                <div>
                    <label for="event_title">Title:</label>
                    <input type="text" id="event_title" name="event_title" required />
                </div>
                <div>
                    <label for="event_description">Description:</label>
                    <textarea id="event_description" name="event_description"></textarea>
                </div>
                <div>
                    <button type="submit">Save</button>
                    <button type="button" id="closeModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script>
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <script>
    jQuery(document).ready(function($) {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: false,
            eventLimit: true,
            selectable: true,
            selectHelper: true,
            events: ajaxurl + '?action=calendar_events_fetch',
            select: function(start, end) {
                $('#event_date').val(start.format('YYYY-MM-DD'));
                $('#event_title').val('');
                $('#event_description').val('');
                $('#eventModal').show();
            }
        });

        $('#closeModal').on('click', function() {
            $('#eventModal').hide();
            $('#calendar').fullCalendar('unselect');
        });

        $('#eventForm').on('submit', function(e) {
            e.preventDefault();
            var title = $('#event_title').val();
            var description = $('#event_description').val();
            var event_date = $('#event_date').val();
            if (title) {
                $.post(ajaxurl, {
                    action: 'calendar_events_add',
                    title: title,
                    description: description,
                    event_date: event_date
                }, function(response) {
                    if (response.success) {
                        $('#calendar').fullCalendar('refetchEvents');
                        $('#eventModal').hide();
                    } else {
                        alert('Failed to add event.');
                    }
                });
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
function calendar_events_enqueue_styles() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'calendar_events')) {
        wp_enqueue_style(
            'calendar-events-css',
            plugins_url('assets/CSS/calendar.css', __FILE__)
        );
    }
}
add_action('wp_enqueue_scripts', 'calendar_events_enqueue_styles');

// AJAX handler to fetch events
add_action('wp_ajax_calendar_events_fetch', 'calendar_events_fetch');
add_action('wp_ajax_nopriv_calendar_events_fetch', 'calendar_events_fetch');
function calendar_events_fetch() {
    global $wpdb;
    $table = $wpdb->prefix . 'events';
    $results = $wpdb->get_results("SELECT id, title, event_date as start FROM $table", ARRAY_A);
    wp_send_json($results);
}

// AJAX handler to add events
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
