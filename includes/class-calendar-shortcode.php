<?php
class Smart_Event_Calendar_Shortcode {
    private static $instance = null;

    private function __construct() {
        // Private constructor to prevent direct instantiation
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function render_calendar($atts) {
        $atts = shortcode_atts(array(
            'month' => date('n'),
            'year' => date('Y'),
            'show_filter' => true,
            'show_search' => true
        ), $atts, 'smart_event_calendar');

        ob_start();
        
        // Get current user ID or generate a guest token
        $user_id = is_user_logged_in() ? get_current_user_id() : self::get_instance()->get_guest_token();
        
        // Get events for the current month
        $start_date = date('Y-m-01', strtotime($atts['year'] . '-' . $atts['month'] . '-01'));
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $events = Smart_Event_Calendar_Core::get_instance()->get_events($user_id, $start_date, $end_date);
        
        // Include the calendar template
        include SMART_EVENT_CALENDAR_PLUGIN_DIR . 'templates/calendar-view.php';
        
        return ob_get_clean();
    }
    
    private function get_guest_token() {
        if (!isset($_COOKIE['sec_guest_token'])) {
            $token = md5(uniqid('sec_', true));
            setcookie('sec_guest_token', $token, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
            return $token;
        }
        return $_COOKIE['sec_guest_token'];
    }
}