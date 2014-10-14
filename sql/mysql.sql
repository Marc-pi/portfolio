CREATE TABLE `{project}` ( 
    `id` int(10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `service` varchar(255) NOT NULL,
    `technology` varchar(255) NOT NULL,
    `website` varchar(64) NOT NULL,
    `website_view` tinyint(1) unsigned NOT NULL,
    `information` text   NOT NULL,
    `seo_title` varchar(255) NOT NULL,
    `seo_keywords` varchar(255) NOT NULL,
    `seo_description` varchar(255) NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    `time_update` int(10) unsigned NOT NULL,
    `uid` int(10) unsigned NOT NULL,
    `hits` int(10) unsigned NOT NULL,
    `image` varchar(255) NOT NULL,
    `path` varchar(16) NOT NULL,
    `status` tinyint(1) unsigned NOT NULL,
    `point` int(10) NOT NULL,
    `count` int(10) unsigned NOT NULL,
    `favourite` int(10) unsigned NOT NULL,
    `commentby` varchar(255) NOT NULL,
    `comment` text NOT NULL,
    `customer` varchar(255) NOT NULL,
    `version` varchar(64) NOT NULL,
    `size` varchar(64) NOT NULL,
    `link_1` varchar(64) NOT NULL,
    `link_2` varchar(64) NOT NULL,
    `link_3` varchar(64) NOT NULL,
    `link_4` varchar(64) NOT NULL,
    `link_5` varchar(64) NOT NULL,
    `image_1` varchar(255) NOT NULL,
    `image_2` varchar(255) NOT NULL,
    `image_3` varchar(255) NOT NULL,
    `image_4` varchar(255) NOT NULL,
    `image_5` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `title` (`title`),
    KEY `time_create` (`time_create`),
    KEY `status` (`status`),
    KEY `project_order` (`id`, `time_create`)
);