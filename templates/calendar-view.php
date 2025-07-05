<?php
/**
 * Calendar View Template
 *
 * This template renders the calendar UI including navigation, filter, search, calendar grid, and event list.
 *
 * Variables expected:
 * - $user_id: current user ID
 * - $start_date: the first date of the calendar view (YYYY-MM-DD)
 * - $atts: shortcode attributes including show_filter and show_search
 */
?>

<div class="smart-event-calendar" data-user-id="<?php echo esc_attr($user_id); ?>">
    <div class="sec-header">
        <div class="sec-nav">
            <button class="sec-prev-month">&larr;</button>
            <h2 class="sec-month-year"><?php echo date_i18n('F Y', strtotime($start_date)); ?></h2>
            <button class="sec-next-month">&rarr;</button>
            <button class="sec-today"><?php _e('Today', 'smart-event-calendar'); ?></button>
        </div>
        
        <div class="sec-actions">
            <button class="sec-add-event"><?php _e('+ Add Event', 'smart-event-calendar'); ?></button>
            <button class="sec-toggle-theme" data-theme="<?php echo isset($_COOKIE['sec_theme_preference']) ? esc_attr($_COOKIE['sec_theme_preference']) : 'light'; ?>">
                <span class="light-icon">☀️</span>
                <span class="dark-icon">🌙</span>
            </button>
            <button class="sec-export-events"><?php _e('Export', 'smart-event-calendar'); ?></button>
</div>
<?php include SMART_EVENT_CALENDAR_PLUGIN_DIR . 'templates/event-form.php'; ?>
</div>
    
    <div class="sec-filter-search" style="<?php echo $atts['show_filter'] || $atts['show_search'] ? '' : 'display: none;'; ?>">
        <?php if ($atts['show_filter']) : ?>
        <div class="sec-filter">
            <select class="sec-filter-type">
                <option value="all"><?php _e('All Events', 'smart-event-calendar'); ?></option>
                <option value="upcoming"><?php _e('Upcoming', 'smart-event-calendar'); ?></option>
                <option value="past"><?php _e('Past', 'smart-event-calendar'); ?></option>
                <option value="range"><?php _e('Date Range', 'smart-event-calendar'); ?></option>
            </select>
            <div class="sec-date-range" style="display: none;">
                <input type="date" class="sec-start-date">
                <span><?php _e('to', 'smart-event-calendar'); ?></span>
                <input type="date" class="sec-end-date">
                <button class="sec-apply-range"><?php _e('Apply', 'smart-event-calendar'); ?></button>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($atts['show_search']) : ?>
        <div class="sec-search">
            <input type="text" class="sec-search-input" placeholder="<?php _e('Search events...', 'smart-event-calendar'); ?>">
            <button class="sec-search-button"><?php _e('Search', 'smart-event-calendar'); ?></button>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="sec-calendar-wrapper">
        <div class="sec-weekdays">
            <?php 
            $weekdays = array(
                
                __('Mon', 'smart-event-calendar'),
                __('Tue', 'smart-event-calendar'),
                __('Wed', 'smart-event-calendar'),
                __('Thu', 'smart-event-calendar'),
                __('Fri', 'smart-event-calendar'),
                __('Sat', 'smart-event-calendar'),
                __('Sun', 'smart-event-calendar')
            );
            
            foreach ($weekdays as $day) {
                echo '<div class="sec-weekday">' . esc_html($day) . '</div>';
            }
            ?>
        </div>
        
        <div class="sec-days-grid">
            <?php
            $first_day = date('N', strtotime($start_date));
            $days_in_month = date('t', strtotime($start_date));
            $current_day = 1;
            
            // Previous month days
            $prev_month = date('Y-m', strtotime('-1 month', strtotime($start_date)));
            $prev_month_days = date('t', strtotime($prev_month));
            
            // Next month days
            $next_month_days = 42 - ($first_day + $days_in_month); // 6 weeks display
            
            // Total cells (6 weeks * 7 days)
            $total_cells = 42;
            
            for ($i = 1; $i <= $total_cells; $i++) {
                $date_class = '';
                $date_content = '';
                $current_date = '';
                
                if ($i < $first_day) {
                    // Previous month
                    $day = $prev_month_days - ($first_day - $i - 1);
                    $date_class = 'sec-prev-month';
                    $date_content = $day;
                    $current_date = date('Y-m-d', strtotime($prev_month . '-' . $day));
                } elseif ($i >= $first_day && $current_day <= $days_in_month) {
                    // Current month
                    $day = $current_day;
                    $date_class = 'sec-current-month';
                    $date_content = $day;
                    
                    $current_date = date('Y-m-d', strtotime($start_date . " + " . ($current_day - 1) . " days"));
                    if ($current_date == date('Y-m-d')) {
                        $date_class .= ' sec-today';
                    }
                    
                    $current_day++;
                } else {
                    // Next month
                    $day = $i - ($first_day + $days_in_month - 1);
                    $date_class = 'sec-next-month';
                    $date_content = $day;
                    $next_month = date('Y-m', strtotime('+1 month', strtotime($start_date)));
                    $current_date = date('Y-m-d', strtotime($next_month . '-' . $day));
                }
                
                echo '<div class="sec-day ' . esc_attr($date_class) . '" data-date="' . esc_attr($current_date) . '">';
                echo '<div class="sec-date">' . esc_html($date_content) . '</div>';
                echo '<div class="sec-events"></div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    
    <div class="sec-events-list">
        <h3><?php _e('Upcoming Events', 'smart-event-calendar'); ?></h3>
        <div class="sec-events-container"></div>
    </div>
</div>
