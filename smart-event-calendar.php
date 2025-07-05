<?php
/*
Plugin Name: Smart Event Calendar – Mark, Track & Remind
Plugin URI: https://yourwebsite.com/smart-event-calendar
Description: A powerful calendar plugin that lets users mark events, set reminders, and track important dates.
Version: 1.0.0
Author: Dilmi Senevirathna
Author URI: https://yourwebsite.com
License: GPLv2 or later
Text Domain: smart-event-calendar
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SMART_EVENT_CALENDAR_VERSION', '1.0.0');
define('SMART_EVENT_CALENDAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMART_EVENT_CALENDAR_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SMART_EVENT_CALENDAR_PLUGIN_DIR . 'includes/class-calendar-core.php';
require_once SMART_EVENT_CALENDAR_PLUGIN_DIR . 'includes/class-calendar-shortcode.php';
require_once SMART_EVENT_CALENDAR_PLUGIN_DIR . 'includes/class-calendar-ajax.php';
require_once SMART_EVENT_CALENDAR_PLUGIN_DIR . 'includes/class-calendar-admin.php';
require_once SMART_EVENT_CALENDAR_PLUGIN_DIR . 'includes/class-calendar-form-handler.php';

// Initialize the plugin
function smart_event_calendar_init() {
    $core = Smart_Event_Calendar_Core::get_instance();
    $shortcode = Smart_Event_Calendar_Shortcode::get_instance();
    $admin = Smart_Event_Calendar_Admin::get_instance();
    
    // Load text domain
    load_plugin_textdomain('smart-event-calendar', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'smart_event_calendar_init');

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('Smart_Event_Calendar_Core', 'activate'));
register_deactivation_hook(__FILE__, array('Smart_Event_Calendar_Core', 'deactivate'));

// Enqueue scripts and styles
function smart_event_calendar_enqueue_assets() {
    // CSS
    wp_enqueue_style('smart-event-calendar', SMART_EVENT_CALENDAR_PLUGIN_URL . 'assets/css/calendar.css', array(), SMART_EVENT_CALENDAR_VERSION);
    
    // JS
    wp_enqueue_script('smart-event-calendar', SMART_EVENT_CALENDAR_PLUGIN_URL . 'assets/js/calendar.js', array('jquery'), SMART_EVENT_CALENDAR_VERSION, true);
    wp_enqueue_script('ical-js', SMART_EVENT_CALENDAR_PLUGIN_URL . 'assets/js/ical.min.js', array(), '1.4.0', true);
    
    // Localize script for AJAX
    wp_localize_script('smart-event-calendar', 'sec_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('smart-event-calendar-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'smart_event_calendar_enqueue_assets');

// Add admin assets
function smart_event_calendar_admin_assets($hook) {
    if ('toplevel_page_smart-event-calendar' !== $hook) {
        return;
    }
    
    wp_enqueue_style('smart-event-calendar-admin', SMART_EVENT_CALENDAR_PLUGIN_URL . 'assets/css/admin.css', array(), SMART_EVENT_CALENDAR_VERSION);
    wp_enqueue_script('smart-event-calendar-admin', SMART_EVENT_CALENDAR_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), SMART_EVENT_CALENDAR_VERSION, true);
}
add_action('admin_enqueue_scripts', 'smart_event_calendar_admin_assets');