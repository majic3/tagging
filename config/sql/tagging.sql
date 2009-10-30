DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `models_tags`;

CREATE TABLE `tags` (
    `id` CHAR(36) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `count` int(10) NOT NULL default '0',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE `models_tags` (
    `id` CHAR(36) NOT NULL,
    `tag_id` CHAR(36) NOT NULL,
    `model` varchar(100) default NULL,
    `model_id` CHAR(36) NOT NULL,
    PRIMARY KEY(`id`)
);

ALTER TABLE `tags`
    ADD UNIQUE KEY `name`(`name`),
    ADD UNIQUE KEY `slug`(`slug`);

ALTER TABLE `models_tags`
    ADD KEY `tag_id`(`tag_id`),
    ADD KEY `model__model_id`(`model`, `model_id`);