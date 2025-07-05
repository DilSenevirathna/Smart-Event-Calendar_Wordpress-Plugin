jQuery(document).ready(function($) {
    'use strict';
    
    class SmartEventCalendar {
        constructor(element) {
            this.$el = $(element);
            this.userId = this.$el.data('user-id');
            this.currentDate = new Date();
            this.currentMonth = this.currentDate.getMonth();
            this.currentYear = this.currentDate.getFullYear();
            this.events = [];
            this.theme = this.getThemePreference();
            
            this.init();
        }
        
        init() {
            this.applyTheme();
            this.renderCalendar();
            this.bindEvents();
            this.loadEvents();
        }
        
        applyTheme() {
            if (this.theme === 'dark') {
                this.$el.addClass('dark-mode');
            } else {
                this.$el.removeClass('dark-mode');
            }
        }
        
        getThemePreference() {
            const cookieValue = document.cookie
                .split('; ')
                .find(row => row.startsWith('sec_theme_preference='));
            
            if (cookieValue) {
                return cookieValue.split('=')[1];
            }
            return 'light';
        }
        
        bindEvents() {
            // Navigation
            this.$el.on('click', '.sec-prev-month', () => this.prevMonth());
            this.$el.on('click', '.sec-next-month', () => this.nextMonth());
            this.$el.on('click', '.sec-today', () => this.goToToday());
            
            // Event actions
            this.$el.on('click', '.sec-add-event', () => this.showEventForm());
            this.$el.on('click', '.sec-day.sec-current-month', (e) => {
                if (!$(e.target).closest('.sec-event-item').length) {
                    const date = $(e.currentTarget).data('date');
                    this.showEventForm(date);
                }
            });
            
            // Event form
            this.$el.on('click', '.sec-close-form, .sec-cancel-event', () => this.hideEventForm());
/* Disabled form submit handler to allow standard form submission */
            this.$el.on('submit', '#sec-event-form', (e) => {
                e.preventDefault();
                this.saveEvent();
            });
            this.$el.on('click', '.sec-delete-event', () => this.deleteEvent());
            
            // Event list actions
            this.$el.on('click', '.sec-event-item', (e) => {
                const eventId = $(e.currentTarget).data('event-id') || 
                               $(e.currentTarget).find('.sec-edit-event').data('event-id');
                if (eventId) {
                    this.editEvent(eventId);
                }
            });
            
            // Theme toggle
            this.$el.on('click', '.sec-toggle-theme', () => this.toggleTheme());
            
            // Export
            this.$el.on('click', '.sec-export-events', () => this.exportEvents());
            
            // Filter and search
            this.$el.on('change', '.sec-filter-type', (e) => this.handleFilterChange(e));
            this.$el.on('click', '.sec-apply-range', () => this.applyDateRange());
            this.$el.on('click', '.sec-search-button', () => this.searchEvents());
            this.$el.on('keypress', '.sec-search-input', (e) => {
                if (e.which === 13) this.searchEvents();
            });
        }
        
        renderCalendar() {
            // Adjust firstDay to match PHP's Monday=1 to Sunday=7 indexing
            let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
            firstDay = firstDay === 0 ? 7 : firstDay; // Sunday (0) becomes 7

            const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();

            // Calculate previous month details
            const prevMonth = this.currentMonth === 0 ? 11 : this.currentMonth - 1;
            const prevYear = this.currentMonth === 0 ? this.currentYear - 1 : this.currentYear;
            const daysInPrevMonth = new Date(prevYear, prevMonth + 1, 0).getDate();

            // Update month/year display
            this.$el.find('.sec-month-year').text(
                new Date(this.currentYear, this.currentMonth).toLocaleDateString('default', {
                    month: 'long',
                    year: 'numeric'
                })
            );

            // Clear existing calendar grid
            const $daysGrid = this.$el.find('.sec-days-grid');
            $daysGrid.empty();

            // Total cells (6 weeks * 7 days)
            const totalCells = 42;

            // Fill calendar grid
            let dayCounter = 1;
            let nextMonthDay = 1;

            for (let i = 1; i <= totalCells; i++) {
                let dateClass = '';
                let dateContent = '';
                let currentDate = null;

                if (i < firstDay) {
                    // Previous month days
                    const day = daysInPrevMonth - (firstDay - i - 1);
                    dateClass = 'sec-prev-month';
                    dateContent = day;
                    currentDate = new Date(prevYear, prevMonth, day);
                } else if (dayCounter <= daysInMonth) {
                    // Current month days
                    dateClass = 'sec-current-month';
                    dateContent = dayCounter;
                    currentDate = new Date(this.currentYear, this.currentMonth, dayCounter);

                    // Highlight today
                    const today = new Date();
                    if (
                        currentDate.getDate() === today.getDate() &&
                        currentDate.getMonth() === today.getMonth() &&
                        currentDate.getFullYear() === today.getFullYear()
                    ) {
                        dateClass += ' sec-today';
                    }

                    dayCounter++;
                } else {
                    // Next month days
                    dateClass = 'sec-next-month';
                    dateContent = nextMonthDay;
                    const nextMonth = this.currentMonth === 11 ? 0 : this.currentMonth + 1;
                    const nextYear = this.currentMonth === 11 ? this.currentYear + 1 : this.currentYear;
                    currentDate = new Date(nextYear, nextMonth, nextMonthDay);
                    nextMonthDay++;
                }

                const dateStr = this.formatDate(currentDate);

                const $day = $(`
                    <div class="sec-day ${dateClass}" data-date="${dateStr}">
                        <div class="sec-date">${dateContent}</div>
                        <div class="sec-events"></div>
                    </div>
                `);

                $daysGrid.append($day);
            }

            // Clear current events highlights
            this.$el.find('.sec-day').removeClass('sec-has-events');
            this.$el.find('.sec-events').empty();

            // Removed call to loadEventsForView to unify event loading and rendering
        }
        
        prevMonth() {
            this.currentMonth--;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }
            this.renderCalendar();
        }
        
        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }
            this.renderCalendar();
        }
        
        goToToday() {
            const today = new Date();
            this.currentMonth = today.getMonth();
            this.currentYear = today.getFullYear();
            this.renderCalendar();
        }
        
        loadEvents() {
            const startDate = new Date(this.currentYear, this.currentMonth - 1, 1);
            const endDate = new Date(this.currentYear, this.currentMonth + 2, 0);
            
            $.ajax({
                url: sec_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'sec_get_events',
                    start_date: this.formatDate(startDate),
                    end_date: this.formatDate(endDate),
                    nonce: sec_ajax.nonce
                },
                success: (response) => {
                    if (response.success) {
                        console.log('Events loaded:', response.data);
                        if (response.data.length > 0) {
                            console.log('First event object:', response.data[0]);
                        }
                        this.events = response.data;
                        this.renderEvents();
                        this.renderEventsList();
                        this.renderMonthEvents(this.events);
                    } else {
                        console.error('Failed to load events:', response.data);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('AJAX error loading events:', status, error);
                }
            });
        }
        
        loadEventsForView(startDate, endDate) {
            $.ajax({
                url: sec_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'sec_get_events',
                    start_date: startDate,
                    end_date: endDate,
                    nonce: sec_ajax.nonce
                },
                success: (response) => {
                    if (response.success) {
                        const viewEvents = response.data;
                        this.events = viewEvents;
                        this.renderMonthEvents(viewEvents);
                    }
                }
            });
        }
        
        renderEvents() {
            // This will be used for the events list and other views
        }
        
        renderMonthEvents(events) {
            this.$el.find('.sec-day').removeClass('sec-has-events');
            this.$el.find('.sec-events').empty();
            
            events.forEach(event => {
                const eventDate = event.event_date;
                const $day = this.$el.find(`.sec-day[data-date="${eventDate}"]`);
                
                if ($day.length) {
                    $day.addClass('sec-has-events');
                    
                    const time = event.event_time ? event.event_time.substring(0, 5) : '';
                    const title = event.title.length > 15 ? event.title.substring(0, 15) + '...' : event.title;
                    
                    $day.find('.sec-events').append(`
                        <div class="sec-event-item" data-event-id="${event.id || event.event_id}">
                            <span class="sec-event-time">${time}</span>
                            <span class="sec-event-title">${title}</span>
                        </div>
                    `);
                }
            });
        }
        
        renderEventsList() {
            console.log('Rendering events list with events:', this.events);
            const $container = this.$el.find('.sec-events-container');
            $container.empty();
            
            // Sort events by date
            const sortedEvents = [...this.events].sort((a, b) => {
                return new Date(a.event_date + ' ' + a.event_time) - new Date(b.event_date + ' ' + b.event_time);
            });
            
            // Filter future and current events
            const now = new Date();
            const upcomingEvents = sortedEvents.filter(event => {
                const eventDate = new Date(event.event_date + ' ' + (event.event_time || '00:00:00'));
                return eventDate >= now;
            });
            
            if (upcomingEvents.length === 0) {
                $container.append('<p>No upcoming events</p>');
                return;
            }
            
            // Group events by date
            const eventsByDate = {};
            upcomingEvents.forEach(event => {
                if (!eventsByDate[event.event_date]) {
                    eventsByDate[event.event_date] = [];
                }
                eventsByDate[event.event_date].push(event);
            });
            
            // Render grouped events
            for (const [date, events] of Object.entries(eventsByDate)) {
                const formattedDate = new Date(date).toLocaleDateString('default', { 
                    weekday: 'long', 
                    month: 'long', 
                    day: 'numeric' 
                });
                
                const $dateHeader = $('<h3>').text(formattedDate);
                $container.append($dateHeader);
                
                events.forEach(event => {
                    const time = event.event_time ? event.event_time.substring(0, 5) : 'All day';
                    const $eventItem = $('<div>').addClass('sec-event-item');
                    const $timeSpan = $('<span>').addClass('sec-event-time').text(time);
                    const $titleSpan = $('<span>').addClass('sec-event-title').text(event.title);
                    
                    $eventItem.append($timeSpan, $titleSpan);
                    $container.append($eventItem);
                });
            }
        }
        
showEventForm(date = '', event = null) {
    const $form = this.$el.find('.sec-event-form-container');
    const $formFields = $form.find('#sec-event-form');
    
    $formFields[0].reset();
    $formFields.find('input[name="event_id"]').val('');
    $formFields.find('.sec-delete-event').hide();
    
    if (date) {
        $formFields.find('input[name="event_date"]').val(date);
        $formFields.find('#sec-event-datepicker').val(date);
    } else {
        const today = this.formatDate(new Date());
        $formFields.find('input[name="event_date"]').val(today);
        $formFields.find('#sec-event-datepicker').val(today);
    }
    
    if (event) {
        $formFields.find('input[name="event_id"]').val(event.id || event.event_id);
        $formFields.find('input[name="title"]').val(event.title);
        $formFields.find('input[name="time"]').val(event.event_time ? event.event_time.substring(0, 5) : '12:00');
        $formFields.find('textarea[name="description"]').val(event.description || '');
        $formFields.find('select[name="reminder"]').val(event.reminder_time || '1 hour');
        $formFields.find('.sec-delete-event').show();
    }
    
    $form.fadeIn();
}
        
        hideEventForm() {
            this.$el.find('.sec-event-form-container').fadeOut();
        }
        
        saveEvent() {
            const $form = this.$el.find('#sec-event-form');
            const title = $form.find('input[name="title"]').val().trim();

            if (!title) {
                this.showNotification('Event title is required', true);
                return;
            }

            $.ajax({
                url: sec_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'sec_add_event',
                    title: title,
                    description: $form.find('textarea[name="description"]').val(),
                    event_date: $form.find('input[name="event_date"]').val(),
                    time: $form.find('input[name="time"]').val(),
                    reminder: $form.find('select[name="reminder"]').val(),
                    nonce: sec_ajax.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.hideEventForm();
                        // Add the new event to our local events array
                        this.events.push(response.data.event);
                        // Re-render the calendar with the new event
                        this.renderMonthEvents(this.events);
                        this.showNotification(response.data.message);
                    } else {
                        this.showNotification(response.data, true);
                    }
                },
                error: () => {
                    this.showNotification('Error saving event. Please try again.', true);
                }
            });
        }
        
        editEvent(eventId) {
            const event = this.events.find(e => (e.id || e.event_id) == eventId);
            if (event) {
                this.showEventForm(event.event_date, event);
            }
        }
        
        deleteEvent() {
            if (!confirm('Are you sure you want to delete this event?')) {
                return;
            }
            
            const eventId = this.$el.find('#sec-event-form input[name="event_id"]').val();
            
            $.ajax({
                url: sec_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'sec_delete_event',
                    event_id: eventId,
                    nonce: sec_ajax.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.hideEventForm();
                        this.loadEvents();
                        this.showNotification(response.data);
                    } else {
                        this.showNotification(response.data, true);
                    }
                }
            });
        }
        
        toggleTheme() {
            const $button = this.$el.find('.sec-toggle-theme');
            const currentTheme = $button.data('theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            $button.data('theme', newTheme);
            
            $.ajax({
                url: sec_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'sec_toggle_theme',
                    theme: newTheme,
                    nonce: sec_ajax.nonce
                },
                success: () => {
                    this.theme = newTheme;
                    this.applyTheme();
                }
            });
        }
        
        exportEvents() {
            const format = prompt('Export format (json or ical):', 'json');
            
            if (!format || !['json', 'ical'].includes(format.toLowerCase())) {
                alert('Please enter either "json" or "ical"');
                return;
            }
            
            if (format === 'json') {
                this.exportToJson();
            } else {
                this.exportToIcal();
            }
        }
        
        exportToJson() {
            const dataStr = JSON.stringify(this.events, null, 2);
            const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
            
            const exportFileDefaultName = `events-${this.formatDate(new Date())}.json`;
            
            this.downloadData(dataUri, exportFileDefaultName);
        }
        
        exportToIcal() {
            window.open(sec_ajax.ajax_url + '?action=sec_export_events&format=ical&nonce=' + sec_ajax.nonce, '_blank');
        }
        
        downloadData(data, filename) {
            const link = document.createElement('a');
            link.setAttribute('href', data);
            link.setAttribute('download', filename);
            link.click();
        }
        
        handleFilterChange(e) {
            const filterType = $(e.target).val();
            const $rangeContainer = this.$el.find('.sec-date-range');
            
            if (filterType === 'range') {
                $rangeContainer.show();
            } else {
                $rangeContainer.hide();
                this.applyFilter(filterType);
            }
        }
        
        applyFilter(filterType) {
            const now = new Date();
            let filteredEvents = [];
            
            switch (filterType) {
                case 'all':
                    filteredEvents = this.events;
                    break;
                case 'upcoming':
                    filteredEvents = this.events.filter(event => {
                        const eventDate = new Date(event.event_date + ' ' + (event.event_time || '00:00:00'));
                        return eventDate >= now;
                    });
                    break;
                case 'past':
                    filteredEvents = this.events.filter(event => {
                        const eventDate = new Date(event.event_date + ' ' + (event.event_time || '00:00:00'));
                        return eventDate < now;
                    });
                    break;
            }
            
            this.renderMonthEvents(filteredEvents);
        }
        
        applyDateRange() {
            const startDate = this.$el.find('.sec-start-date').val();
            const endDate = this.$el.find('.sec-end-date').val();
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }
            
            const filteredEvents = this.events.filter(event => {
                return event.event_date >= startDate && event.event_date <= endDate;
            });
            
            this.renderMonthEvents(filteredEvents);
        }
        
        searchEvents() {
            const searchTerm = this.$el.find('.sec-search-input').val().toLowerCase();
            
            if (!searchTerm) {
                this.renderMonthEvents(this.events);
                return;
            }
            
            const filteredEvents = this.events.filter(event => {
                return event.title.toLowerCase().includes(searchTerm) || 
                       (event.description && event.description.toLowerCase().includes(searchTerm));
            });
            
            this.renderMonthEvents(filteredEvents);
        }
        
        showNotification(message, isError = false) {
            const $notification = $(`
                <div class="sec-notification ${isError ? 'sec-error' : 'sec-success'}">
                    ${message}
                </div>
            `);
            
            this.$el.append($notification);
            
            setTimeout(() => {
                $notification.fadeOut(() => $notification.remove());
            }, 3000);
        }
        
        formatDate(date) {
            if (!(date instanceof Date)) {
                date = new Date(date);
            }
            
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            
            return `${year}-${month}-${day}`;
        }
    }
    
    // Initialize the calendar
    $('.smart-event-calendar').each(function() {
        new SmartEventCalendar(this);
    });
});
