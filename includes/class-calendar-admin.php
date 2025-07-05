<?php
class Smart_Event_Calendar_Admin {
    private static $instance = null;

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Smart Event Calendar', 'smart-event-calendar'),
            __('Event Calendar', 'smart-event-calendar'),
            'manage_options',
            'smart-event-calendar',
            array($this, 'render_admin_page'),
            'dashicons-calendar-alt',
            30
        );
    }

    public function register_settings() {
        register_setting('smart_event_calendar_settings', 'sec_enable_email_reminders');
        register_setting('smart_event_calendar_settings', 'sec_default_reminder_time');
        register_setting('smart_event_calendar_settings', 'sec_default_view');
        
        add_settings_section(
            'sec_general_settings',
            __('General Settings', 'smart-event-calendar'),
            array($this, 'render_general_settings_section'),
            'smart-event-calendar'
        );
        
        add_settings_field(
            'sec_enable_email_reminders',
            __('Enable Email Reminders', 'smart-event-calendar'),
            array($this, 'render_enable_email_reminders_field'),
            'smart-event-calendar',
            'sec_general_settings'
        );
        
        add_settings_field(
            'sec_default_reminder_time',
            __('Default Reminder Time', 'smart-event-calendar'),
            array($this, 'render_default_reminder_time_field'),
            'smart-event-calendar',
            'sec_general_settings'
        );
        
        add_settings_field(
            'sec_default_view',
            __('Default Calendar View', 'smart-event-calendar'),
            array($this, 'render_default_view_field'),
            'smart-event-calendar',
            'sec_general_settings'
        );
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Smart Event Calendar Settings', 'smart-event-calendar'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('smart_event_calendar_settings');
                do_settings_sections('smart-event-calendar');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_general_settings_section() {
        echo '<p>' . esc_html__('Configure general settings for the Smart Event Calendar plugin.', 'smart-event-calendar') . '</p>';
    }

    public function render_enable_email_reminders_field() {
        $value = get_option('sec_enable_email_reminders', '1');
        ?>
        <label>
            <input type="checkbox" name="sec_enable_email_reminders" value="1" <?php checked('1', $value); ?>>
            <?php esc_html_e('Send email reminders to users', 'smart-event-calendar'); ?>
        </label>
        <?php
    }

    public function render_default_reminder_time_field() {
        $value = get_option('sec_default_reminder_time', '1 hour');
        $options = array(
            '30 minutes' => __('30 minutes before', 'smart-event-calendar'),
            '1 hour' => __('1 hour before', 'smart-event-calendar'),
            '1 day' => __('1 day before', 'smart-event-calendar'),
            '1 week' => __('1 week before', 'smart-event-calendar')
        );
        ?>
        <select name="sec_default_reminder_time">
            <?php foreach ($options as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function render_default_view_field() {
        $value = get_option('sec_default_view', 'month');
        $options = array(
            'month' => __('Month', 'smart-event-calendar'),
            'week' => __('Week', 'smart-event-calendar'),
            'day' => __('Day', 'smart-event-calendar')
        );
        ?>
        <select name="sec_default_view">
            <?php foreach ($options as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}