<?php
class Smart_Event_Calendar_Form_Handler {
    public static function init() {
        add_action('admin_post_sec_save_event', array(__CLASS__, 'handle_save_event'));
        add_action('admin_post_nopriv_sec_save_event', array(__CLASS__, 'handle_save_event'));
    }

    public static function handle_save_event() {
        if (!isset($_POST['sec_save_event_nonce_field']) || !wp_verify_nonce($_POST['sec_save_event_nonce_field'], 'sec_save_event_nonce')) {
            wp_die(__('Nonce verification failed', 'smart-event-calendar'));
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_die(__('You must be logged in to add events.', 'smart-event-calendar'));
        }

        $data = array(
            'title' => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'event_date' => isset($_POST['datepicker']) ? sanitize_text_field($_POST['datepicker']) : '',
            'event_time' => isset($_POST['time']) ? sanitize_text_field($_POST['time']) : '00:00:00',
            'reminder_time' => isset($_POST['reminder']) ? sanitize_text_field($_POST['reminder']) : null,
            // You can add event_color and other fields here if needed
        );

        $event_id = Smart_Event_Calendar_Core::get_instance()->add_event($user_id, $data);

if ($event_id) {
            wp_send_json_success(array('event_id' => $event_id));
        } else {
            wp_send_json_error(__('Failed to add event.', 'smart-event-calendar'));
        }
    }
}

Smart_Event_Calendar_Form_Handler::init();
