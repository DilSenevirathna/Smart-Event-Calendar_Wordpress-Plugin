<?php
/**
 * Event Form Template
 * 
 * Handles the add/edit event modal form
 *
 * @package Smart Event Calendar
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="sec-event-form-container" style="display: none;">
    <div class="sec-event-form-overlay"></div>
    
    <div class="sec-event-form">
        <button type="button" class="sec-close-form" aria-label="<?php esc_attr_e('Close form', 'smart-event-calendar'); ?>">
            <span class="dashicons dashicons-no-alt"></span>
        </button>
        
        <div class="sec-form-header">
            <h3 class="sec-form-title"><?php esc_html_e('Add New Event', 'smart-event-calendar'); ?></h3>
            <p class="sec-form-date-display">
                <span class="dashicons dashicons-calendar"></span>
                <span id="sec-selected-date"><?php echo esc_html(date_i18n('F j, Y')); ?></span>
            </p>
        </div>
        
<form id="sec-event-form" class="sec-event-form-content" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="sec_save_event">
            <?php wp_nonce_field('sec_save_event_nonce', 'sec_save_event_nonce_field'); ?>
            <input type="hidden" name="event_id" id="sec-event-id" value="">
            <input type="hidden" name="event_date" id="sec-event-date" value="<?php echo esc_attr(date('Y-m-d')); ?>">
            
            <div class="sec-form-group">
                <label for="sec-event-title" class="sec-form-label">
                    <?php esc_html_e('Event Title', 'smart-event-calendar'); ?>
                    <span class="required">*</span>
                </label>
                <input type="text" 
                       id="sec-event-title" 
                       name="title" 
                       class="sec-form-input" 
                       required 
                       placeholder="<?php esc_attr_e('Enter event title', 'smart-event-calendar'); ?>">
                <div class="sec-form-error" id="sec-title-error"></div>
            </div>
            
            <div class="sec-form-row">
                <div class="sec-form-group sec-form-col">
                    <label for="sec-event-datepicker" class="sec-form-label">
                        <?php esc_html_e('Date', 'smart-event-calendar'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="date" 
                           id="sec-event-datepicker" 
                           name="datepicker" 
                           class="sec-form-input"
                           required>
                </div>
                
                <div class="sec-form-group sec-form-col">
                    <label for="sec-event-time" class="sec-form-label">
                        <?php esc_html_e('Time', 'smart-event-calendar'); ?>
                    </label>
                    <input type="time" 
                           id="sec-event-time" 
                           name="time" 
                           class="sec-form-input" 
                           value="12:00">
                </div>
            </div>
            
            <div class="sec-form-group">
                <label for="sec-event-description" class="sec-form-label">
                    <?php esc_html_e('Description', 'smart-event-calendar'); ?>
                </label>
                <textarea id="sec-event-description" 
                          name="description" 
                          class="sec-form-textarea" 
                          rows="4" 
                          placeholder="<?php esc_attr_e('Enter event details (optional)', 'smart-event-calendar'); ?>"></textarea>
            </div>
            
            <div class="sec-form-group">
                <label for="sec-event-reminder" class="sec-form-label">
                    <?php esc_html_e('Reminder', 'smart-event-calendar'); ?>
                </label>
                <select id="sec-event-reminder" name="reminder" class="sec-form-select">
                    <option value=""><?php esc_html_e('No reminder', 'smart-event-calendar'); ?></option>
                    <option value="30 minutes"><?php esc_html_e('30 minutes before', 'smart-event-calendar'); ?></option>
                    <option value="1 hour" selected><?php esc_html_e('1 hour before', 'smart-event-calendar'); ?></option>
                    <option value="2 hours"><?php esc_html_e('2 hours before', 'smart-event-calendar'); ?></option>
                    <option value="1 day"><?php esc_html_e('1 day before', 'smart-event-calendar'); ?></option>
                    <option value="1 week"><?php esc_html_e('1 week before', 'smart-event-calendar'); ?></option>
                    <option value="custom"><?php esc_html_e('Custom reminder', 'smart-event-calendar'); ?></option>
                </select>
                
                <div class="sec-custom-reminder" style="display: none;">
                    <div class="sec-form-row">
                        <div class="sec-form-group sec-form-col">
                            <input type="number" 
                                   id="sec-custom-reminder-value" 
                                   name="custom_reminder_value" 
                                   class="sec-form-input" 
                                   min="1" 
                                   max="365" 
                                   placeholder="10">
                        </div>
                        <div class="sec-form-group sec-form-col">
                            <select id="sec-custom-reminder-unit" name="custom_reminder_unit" class="sec-form-select">
                                <option value="minutes"><?php esc_html_e('Minutes', 'smart-event-calendar'); ?></option>
                                <option value="hours"><?php esc_html_e('Hours', 'smart-event-calendar'); ?></option>
                                <option value="days"><?php esc_html_e('Days', 'smart-event-calendar'); ?></option>
                                <option value="weeks"><?php esc_html_e('Weeks', 'smart-event-calendar'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="sec-form-group">
                <label class="sec-form-label">
                    <?php esc_html_e('Event Color', 'smart-event-calendar'); ?>
                </label>
                <div class="sec-color-picker">
                    <?php 
                    $colors = array(
                        '#4e7bd8' => __('Blue', 'smart-event-calendar'),
                        '#d84e4e' => __('Red', 'smart-event-calendar'),
                        '#4ed84e' => __('Green', 'smart-event-calendar'),
                        '#d8d84e' => __('Yellow', 'smart-event-calendar'),
                        '#d84ed8' => __('Purple', 'smart-event-calendar')
                    );
                    
                    foreach ($colors as $hex => $label) : ?>
                        <div class="sec-color-option">
                            <input type="radio" 
                                   id="sec-color-<?php echo esc_attr(str_replace('#', '', $hex)); ?>" 
                                   name="event_color" 
                                   value="<?php echo esc_attr($hex); ?>"
                                   <?php echo $hex === '#4e7bd8' ? 'checked' : ''; ?>>
                            <label for="sec-color-<?php echo esc_attr(str_replace('#', '', $hex)); ?>" 
                                   style="background-color: <?php echo esc_attr($hex); ?>"
                                   title="<?php echo esc_attr($label); ?>"></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="sec-form-actions">
                <button type="button" class="sec-form-cancel button">
                    <?php esc_html_e('Cancel', 'smart-event-calendar'); ?>
                </button>
                <button type="submit" class="sec-form-submit button button-primary">
                    <span class="dashicons dashicons-yes"></span>
                    <?php esc_html_e('Save Event', 'smart-event-calendar'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.sec-event-form-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: none;
}

.sec-event-form-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.sec-event-form {
    position: relative;
    max-width: 500px;
    width: 90%;
    margin: 50px auto;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    max-height: 90vh;
    overflow-y: auto;
}

.sec-close-form {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    padding: 5px;
    cursor: pointer;
    color: #757575;
}

.sec-close-form:hover {
    color: #d63638;
}

.sec-form-header {
    padding: 20px 20px 15px;
    border-bottom: 1px solid #eee;
}

.sec-form-title {
    margin: 0;
    font-size: 18px;
    color: #1d2327;
}

.sec-form-date-display {
    margin: 5px 0 0;
    font-size: 13px;
    color: #646970;
    display: flex;
    align-items: center;
    gap: 5px;
}

.sec-event-form-content {
    padding: 20px;
}

.sec-form-group {
    margin-bottom: 15px;
}

.sec-form-label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #1d2327;
}

.sec-form-label .required {
    color: #d63638;
}

.sec-form-input,
.sec-form-select,
.sec-form-textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #8c8f94;
    border-radius: 3px;
    background: #fff;
    font-size: 14px;
}

.sec-form-input:focus,
.sec-form-select:focus,
.sec-form-textarea:focus {
    border-color: #2271b1;
    box-shadow: 0 0 0 1px #2271b1;
    outline: none;
}

.sec-form-textarea {
    min-height: 80px;
    resize: vertical;
}

.sec-form-error {
    color: #d63638;
    font-size: 12px;
    margin-top: 5px;
    display: none;
}

.sec-form-row {
    display: flex;
    gap: 15px;
}

.sec-form-col {
    flex: 1;
}

.sec-color-picker {
    display: flex;
    gap: 10px;
    margin-top: 5px;
}

.sec-color-option input[type="radio"] {
    display: none;
}

.sec-color-option label {
    display: inline-block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s ease;
}

.sec-color-option input[type="radio"]:checked + label {
    border-color: #1d2327;
    transform: scale(1.1);
}

.sec-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

/* Dark mode styles */
.smart-event-calendar.dark-mode .sec-event-form {
    background: #1d2327;
    border-color: #2c3338;
}

.smart-event-calendar.dark-mode .sec-form-title,
.smart-event-calendar.dark-mode .sec-form-label {
    color: #f0f0f1;
}

.smart-event-calendar.dark-mode .sec-form-input,
.smart-event-calendar.dark-mode .sec-form-select,
.smart-event-calendar.dark-mode .sec-form-textarea {
    background: #2c3338;
    border-color: #3c434a;
    color: #f0f0f1;
}

.smart-event-calendar.dark-mode .sec-form-input:focus,
.smart-event-calendar.dark-mode .sec-form-select:focus,
.smart-event-calendar.dark-mode .sec-form-textarea:focus {
    border-color: #72aee6;
    box-shadow: 0 0 0 1px #72aee6;
}

.smart-event-calendar.dark-mode .sec-form-header,
.smart-event-calendar.dark-mode .sec-form-actions {
    border-color: #2c3338;
}

.smart-event-calendar.dark-mode .sec-form-date-display {
    color: #9ea3a8;
}

.smart-event-calendar.dark-mode .sec-close-form {
    color: #9ea3a8;
}

.smart-event-calendar.dark-mode .sec-close-form:hover {
    color: #ffabaf;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle custom reminder fields
    $('#sec-event-reminder').on('change', function() {
        if ($(this).val() === 'custom') {
            $('.sec-custom-reminder').show();
        } else {
            $('.sec-custom-reminder').hide();
        }
    });
    
    // Date picker sync with hidden field
    $('#sec-event-datepicker').on('change', function() {
        const date = $(this).val();
        $('#sec-event-date').val(date);
        $('#sec-selected-date').text(new Date(date).toLocaleDateString());
    });
});
</script>