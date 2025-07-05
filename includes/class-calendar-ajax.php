<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Smart_Event_Calendar_Ajax {
    public static function init() {
        add_action('wp_ajax_sec_get_events', array(__CLASS__, 'handle_get_events'));
        add_action('wp_ajax_nopriv_sec_get_events', array(__CLASS__, 'handle_get_events'));
        add_action('wp_ajax_sec_add_event', array(__CLASS__, 'handle_add_event'));
        add_action('wp_ajax_nopriv_sec_add_event', array(__CLASS__, 'handle_add_event'));
    }

    public static function handle_get_events() {
        check_ajax_referer('smart-event-calendar-nonce', 'nonce');

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(__('User not logged in', 'smart-event-calendar'));
        }

        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';

        if (empty($start_date) || empty($end_date)) {
            wp_send_json_error(__('Invalid date range', 'smart-event-calendar'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'smart_events';

        $query = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND event_date BETWEEN %s AND %s ORDER BY event_date ASC, event_time ASC",
            $user_id,
            $start_date,
            $end_date
        );

        $events = $wpdb->get_results($query);

        wp_send_json_success($events);
    }

    public static function handle_add_event() {
        check_ajax_referer('smart-event-calendar-nonce', 'nonce');

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(__('User not logged in', 'smart-event-calendar'));
        }

        $data = array(
            'title' => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'event_date' => isset($_POST['event_date']) ? sanitize_text_field($_POST['event_date']) : '',
            'event_time' => isset($_POST['time']) ? sanitize_text_field($_POST['time']) : '00:00:00',
            'reminder_time' => isset($_POST['reminder']) ? sanitize_text_field($_POST['reminder']) : null,
        );

        $event_id = Smart_Event_Calendar_Core::get_instance()->add_event($user_id, $data);

        if ($event_id) {
            $new_event = array(
                'id' => $event_id,
                'title' => $data['title'],
                'description' => $data['description'],
                'event_date' => $data['event_date'],
                'event_time' => $data['event_time'],
                'reminder_time' => $data['reminder_time']
            );
            wp_send_json_success(array(
                'message' => __('Event added successfully!', 'smart-event-calendar'),
                'event' => $new_event
            ));
        } else {
            wp_send_json_error(__('Failed to add event.', 'smart-event-calendar'));
        }
    }
}

Smart_Event_Calendar_Ajax::init();
