CREATE TABLE `wp_smart_events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `reminder_time` varchar(50) DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
