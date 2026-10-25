-- Jalankan sekali pada database SMAGAEDU sebelum menggunakan fitur Chat Personal.
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_uuid` varchar(100) NOT NULL,
  `recipient_uuid` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `chat_messages_sender_recipient_id` (`sender_uuid`, `recipient_uuid`, `id`),
  KEY `chat_messages_recipient_sender_id` (`recipient_uuid`, `sender_uuid`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `chat_reads` (
  `user_uuid` varchar(100) NOT NULL,
  `contact_uuid` varchar(100) NOT NULL,
  `last_read_message_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_uuid`, `contact_uuid`),
  KEY `chat_reads_last_read_message_id` (`last_read_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
