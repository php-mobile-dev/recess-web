CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `email` varchar(255),
  `password` varchar(255),
  `mobile_no` varchar(255),
  `mobile_no_verified` boolean DEFAULT false,
  `type` varchar(255) DEFAULT "app_user",
  `avatar` varchar(255),
  `bio` varchar(255),
  `address` varchar(255),
  `longitude` float8,
  `latitude` float8,
  `search_radius` int DEFAULT 0,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `devices` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `device_id` varchar(255),
  `device_type` varchar(255) DEFAULT "A"
);

CREATE TABLE `notifications` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `text` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `cards` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `stripe_id` varchar(255),
  `is_default` boolean DEFAULT false,
  `card_no` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `bank_details` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `bank_name` varchar(255),
  `ifsc` boolean DEFAULT false,
  `account_no` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `invitations` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `email` varchar(255),
  `code` varchar(255),
  `event_id` int,
  `acceptance_status` boolean DEFAULT false,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `friends` (
  `user_id` int,
  `friend_id` int
);

CREATE TABLE `posts` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `type` varchar(255) DEFAULT "feed",
  `post_text` text,
  `parent_id` int,
  `font_size` int,
  `background_color` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp,
  `deleted_at` timestamp
);

CREATE TABLE `post_media` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `post_id` int,
  `filename` varchar(255),
  `mime_type` varchar(255)
);

CREATE TABLE `comments` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `post_id` int,
  `user_id` int,
  `comment` varchar(255),
  `parent_id` int,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `post_user` (
  `post_id` int,
  `user_id` int
);

CREATE TABLE `report_post` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `post_id` int,
  `user_id` int,
  `report_reason` varchar(255),
  `status` varchar(255) DEFAULT "pending",
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `activity_categories` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `active` boolean DEFAULT false
);

CREATE TABLE `activity_types` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `active` boolean DEFAULT false
);

CREATE TABLE `activities` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `active` boolean DEFAULT false,
  `description` text
);

CREATE TABLE `activity_user` (
  `activity_id` int,
  `user_id` int
);

CREATE TABLE `events` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `activity_category_id` int,
  `activity_type_id` int,
  `name` varchar(255),
  `starts_on` timestamp,
  `ends_on` timestamp,
  `address` varchar(255),
  `longitude` float8,
  `latitude` float8,
  `no_of_participants` int,
  `description` text,
  `days` varchar(255) DEFAULT "[]",
  `fees` float4,
  `frequency` varchar(255),
  `winnings` float4 DEFAULT 0,
  `status` varchar(255) DEFAULT "active",
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `event_user` (
  `event_id` int,
  `user_id` int
);

CREATE TABLE `payments` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `event_id` int,
  `from_id` int,
  `to_id` int,
  `transaction_id` varchar(255),
  `amount` float4,
  `created_at` timestamp,
  `updated_at` timestamp
);

ALTER TABLE `devices` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `notifications` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `cards` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `bank_details` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `invitations` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `invitations` ADD FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

ALTER TABLE `friends` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `friends` ADD FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`);

ALTER TABLE `posts` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `post_media` ADD FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `post_user` ADD FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

ALTER TABLE `post_user` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `report_post` ADD FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

ALTER TABLE `report_post` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `activity_user` ADD FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`);

ALTER TABLE `activity_user` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `events` ADD FOREIGN KEY (`activity_category_id`) REFERENCES `activity_categories` (`id`);

ALTER TABLE `events` ADD FOREIGN KEY (`activity_type_id`) REFERENCES `activity_types` (`id`);

ALTER TABLE `event_user` ADD FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

ALTER TABLE `event_user` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `payments` ADD FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

ALTER TABLE `payments` ADD FOREIGN KEY (`from_id`) REFERENCES `users` (`id`);

ALTER TABLE `payments` ADD FOREIGN KEY (`to_id`) REFERENCES `users` (`id`);
