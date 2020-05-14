SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `login` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `email` TEXT,
  `phone` TEXT,
  `first_name` TEXT,
  `last_name` TEXT,
  `avatar` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2
;

INSERT INTO `user` (`id`, `login`, `password`, `email`, `phone`, `first_name`, `last_name`, `avatar`) VALUES
(1, 'testadmin', '9283a03246ef2dacdc21a9b137817ec1', NULL, NULL, NULL, NULL, NULL)
;

CREATE TABLE `news` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`category` TINYINT(1) NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`message` TEXT NOT NULL,
	`image` TEXT NULL,
	`created_at` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `category` (`category`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
