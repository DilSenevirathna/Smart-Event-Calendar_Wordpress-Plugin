<?php
/**
 * Event List Template
 * 
 * Contains templates for event items and notifications
 *
 * @package Smart Event Calendar
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="sec-event-templates" style="display: none;">
    
    <!-- Single Event Item Template -->
    <div class="sec-event-item-template">
        <div class="sec-event-item" data-event-id="{event_id}">
            <div class="sec-event-time">{event_time}</div>
            <div class="sec-event-details">
                <h4 class="sec-event-title">{event_title}</h4>
                <p class="sec-event-description">{event_description}</p>
                <div class="sec-event-meta">
                    <span class="sec-event-date">{event_date}</span>
                    <?php if ('{event_reminder}' !== '') : ?>
                        <span class="sec-event-reminder">
                            <span class="dashicons dashicons-clock"></span>
                            {event_reminder}
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="sec-event-actions">
                <button class="sec-edit-event" title="<?php esc_attr_e('Edit', 'smart-event-calendar'); ?>">
                    <span class="dashicons dashicons-edit"></span>
                </button>
                <button class="sec-delete-event" title="<?php esc_attr_e('Delete', 'smart-event-calendar'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Empty State Template -->
    <div class="sec-empty-events-template">
        <div class="sec-empty-events">
            <div class="sec-empty-icon">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <h4><?php esc_html_e('No events scheduled', 'smart-event-calendar'); ?></h4>
            <p><?php esc_html_e('Click on a date to add your first event', 'smart-event-calendar'); ?></p>
            <button class="sec-add-first-event button button-primary">
                <?php esc_html_e('Add New Event', 'smart-event-calendar'); ?>
            </button>
        </div>
    </div>

    <!-- Notification Template -->
    <div class="sec-notification-template">
        <div class="sec-notification sec-notification-{type}">
            <div class="sec-notification-content">
                <span class="sec-notification-icon dashicons dashicons-{icon}"></span>
                <div class="sec-notification-message">{message}</div>
            </div>
            <button class="sec-notification-close">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
    </div>

    <!-- Confirm Delete Dialog -->
    <div class="sec-confirm-delete-template">
        <div class="sec-confirm-dialog">
            <div class="sec-confirm-content">
                <h4><?php esc_html_e('Delete Event', 'smart-event-calendar'); ?></h4>
                <p><?php esc_html_e('Are you sure you want to delete this event?', 'smart-event-calendar'); ?></p>
                <p class="sec-event-to-delete"><strong>{event_title}</strong> - {event_date}</p>
            </div>
            <div class="sec-confirm-actions">
                <button class="sec-confirm-cancel button"><?php esc_html_e('Cancel', 'smart-event-calendar'); ?></button>
                <button class="sec-confirm-delete button button-danger"><?php esc_html_e('Delete', 'smart-event-calendar'); ?></button>
            </div>
        </div>
    </div>

</div>

<style>
.sec-event-templates {
    display: none !important;
}

.sec-event-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    margin-bottom: 10px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.sec-event-item:hover {
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

.sec-event-time {
    min-width: 60px;
    font-weight: bold;
    color: #2c3338;
}

.sec-event-details {
    flex: 1;
    margin: 0 15px;
}

.sec-event-title {
    margin: 0 0 5px 0;
    font-size: 15px;
    color: #1d2327;
}

.sec-event-description {
    margin: 0;
    font-size: 13px;
    color: #646970;
}

.sec-event-meta {
    display: flex;
    gap: 15px;
    margin-top: 5px;
    font-size: 12px;
    color: #757575;
}

.sec-event-actions {
    display: flex;
    gap: 5px;
}

.sec-event-actions button {
    background: none;
    border: none;
    padding: 5px;
    cursor: pointer;
    color: #757575;
}

.sec-event-actions button:hover {
    color: #2271b1;
}

/* Empty state styles */
.sec-empty-events {
    text-align: center;
    padding: 30px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.sec-empty-icon {
    font-size: 50px;
    color: #ccd0d4;
    margin-bottom: 15px;
}

/* Notification styles */
.sec-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    max-width: 350px;
    padding: 15px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    z-index: 9999;
    animation: secNotificationFadeIn 0.3s ease-out;
}

.sec-notification-content {
    display: flex;
    align-items: center;
    flex: 1;
}

.sec-notification-icon {
    font-size: 24px;
    margin-right: 10px;
}

.sec-notification-success {
    border-left: 4px solid #00a32a;
}

.sec-notification-error {
    border-left: 4px solid #d63638;
}

.sec-notification-close {
    background: none;
    border: none;
    padding: 0;
    margin-left: 10px;
    cursor: pointer;
    color: #757575;
}

/* Confirm delete dialog */
.sec-confirm-dialog {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border-radius: 4px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    z-index: 99999;
    max-width: 400px;
    width: 90%;
}

.sec-confirm-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.button-danger {
    background: #d63638;
    border-color: #d63638;
    color: #fff;
}

.button-danger:hover {
    background: #b32d2e;
    border-color: #b32d2e;
}

@keyframes secNotificationFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>
</div>