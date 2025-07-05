<?php
class Smart_Event_Calendar_Core {
    private static $instance = null;
    private $db_version = '1.0';
    private $table_name;

    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'smart_events';
        
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_shortcodes'));
        add_action('wp_footer', array($this, 'render_templates'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function activate() {
        self::create_tables();
        self::schedule_cron();
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('smart_event_calendar_daily_reminders');
    }

    private static function create_tables() {
        global $wpdb;
        $instance = self::get_instance();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$instance->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            event_date date NOT NULL,
            event_time time DEFAULT NULL,
            title varchar(255) NOT NULL,
            description text,
            reminder_time varchar(50) DEFAULT NULL,
            reminder_sent tinyint(1) DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY event_date (event_date)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        add_option('smart_event_calendar_db_version', $instance->db_version);
    }

    private static function schedule_cron() {
        if (!wp_next_scheduled('smart_event_calendar_daily_reminders')) {
            wp_schedule_event(time(), 'hourly', 'smart_event_calendar_daily_reminders');
        }
    }

    public function register_post_types() {
        // Register any custom post types if needed
    }

    public function register_shortcodes() {
        add_shortcode('smart_event_calendar', array('Smart_Event_Calendar_Shortcode', 'render_calendar'));
    }

    public function render_templates() {
        // Render any hidden templates needed for JS
        include SMART_EVENT_CALENDAR_PLUGIN_DIR . 'templates/event-form.php';
        include SMART_EVENT_CALENDAR_PLUGIN_DIR . 'templates/event-list.php';
    }

    public function add_event($user_id, $data) {
        global $wpdb;
        
        $defaults = array(
            'event_date' => date('Y-m-d'),
            'event_time' => '00:00:00',
            'title' => '',
            'description' => '',
            'reminder_time' => null
        );
        
        $data = wp_parse_args($data, $defaults);
        
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'user_id' => $user_id,
                'event_date' => $data['event_date'],
                'event_time' => $data['event_time'],
                'title' => sanitize_text_field($data['title']),
                'description' => sanitize_textarea_field($data['description']),
                'reminder_time' => $data['reminder_time'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        return $result ? $wpdb->insert_id : false;
    }

    public function get_events($user_id, $start_date, $end_date) {
        global $wpdb;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
            WHERE user_id = %d 
            AND event_date BETWEEN %s AND %s 
            ORDER BY event_date, event_time",
            $user_id, $start_date, $end_date
        ));
    }

    public function delete_event($user_id, $event_id) {
        global $wpdb;
        
        return $wpdb->delete(
            $this->table_name,
            array(
                'id' => $event_id,
                'user_id' => $user_id
            ),
            array('%d', '%d')
        );
    }

    public function send_reminders() {
        global $wpdb;
        
        $now = current_time('mysql');
        $events = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} 
            WHERE reminder_sent = 0 
            AND reminder_time IS NOT NULL"
        );
        
        foreach ($events as $event) {
            if ($this->should_send_reminder($event, $now)) {
                $this->send_reminder($event);
                $wpdb->update(
                    $this->table_name,
                    array('reminder_sent' => 1),
                    array('id' => $event->id),
                    array('%d'),
                    array('%d')
                );
            }
        }
    }
    
    private function should_send_reminder($event, $current_time) {
        // Implement reminder logic based on reminder_time
        // Compare with current time to determine if reminder should be sent
        return true; // Simplified for this example
    }
    
    private function send_reminder($event) {
        $user = get_user_by('id', $event->user_id);
        if (!$user) return;
        
        $subject = sprintf(__('Reminder: %s', 'smart-event-calendar'), $event->title);
        $message = sprintf(
            __("This is a reminder for your event:\n\n%s\n\nDate: %s\nTime: %s\n\nDescription:\n%s", 'smart-event-calendar'),
            $event->title,
            date_i18n(get_option('date_format'), strtotime($event->event_date)),
            date_i18n(get_option('time_format'), strtotime($event->event_time)),
            $event->description
        );
        
        wp_mail($user->user_email, $subject, $message);
    }
}