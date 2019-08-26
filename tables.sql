CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(16) NOT NULL,
    `email` VARCHAR(40) NOT NULL,
    `password` CHAR(128) NOT NULL,
    `salt` CHAR(64),
    `ip` VARCHAR(39) NOT NULL,
    `ipLastLogin` VARCHAR(39) NOT NULL,
    `dateRegister` BIGINT UNSIGNED NOT NULL,
    `dateLastLogin` BIGINT UNSIGNED NOT NULL,
    `dateLastLink` BIGINT UNSIGNED NOT NULL,
    `linksShortened` BIGINT UNSIGNED NOT NULL,
    `linksShortenedToday` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY (`name`),
    KEY (`ip`),
    KEY (`email`)
) ENGINE=MyISAM, charset=utf8, AUTO_INCREMENT=1;

CREATE TABLE `links` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user` BIGINT UNSIGNED NOT NULL, -- who created this link
    `accessed` BIGINT UNSIGNED NOT NULL,
    `link` TEXT NOT NULL,
    `date` BIGINT UNSIGNED NOT NULL, -- creation date
    `edited` tinyint(1) NOT NULL,
    `ip` VARCHAR(39) NOT NULL, -- ip with which the link was created
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user`) REFERENCES `users`(`id`)
) ENGINE=MyISAM, charset=utf8, AUTO_INCREMENT=1;

CREATE TABLE `browsers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `hash` CHAR(32) NOT NULL,
    `agent` TEXT NOT NULL,
    `count` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY (`hash`)
) ENGINE=MyISAM, charset=utf8, AUTO_INCREMENT=1;

CREATE TABLE `ipLog` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `link` BIGINT UNSIGNED NOT NULL,
    `date` BIGINT UNSIGNED NOT NULL,
    `browser` BIGINT UNSIGNED NOT NULL,
    `ip` VARCHAR(39) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`link`) REFERENCES `links`(`id`),
    FOREIGN KEY (`browser`) REFERENCES `browsers`(`id`)
) ENGINE=MyISAM, charset=utf8, AUTO_INCREMENT=1;