CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  KEY `user_roles_role_id` (`role_id`),
  CONSTRAINT `user_roles_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`)
SELECT `id`, `role_id` FROM `users`;

INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`)
SELECT `users`.`id`, 3
FROM `users`
INNER JOIN `user_profiles` ON `user_profiles`.`user_id` = `users`.`id`
WHERE `users`.`role_id` = 2
  AND (
    NULLIF(TRIM(`user_profiles`.`mapel_uuid`), '') IS NOT NULL
    OR `user_profiles`.`nip` IS NOT NULL
    OR `user_profiles`.`jenis_kelamin` IS NOT NULL
  );
