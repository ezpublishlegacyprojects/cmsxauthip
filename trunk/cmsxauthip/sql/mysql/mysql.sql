CREATE TABLE `cmsxauthip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `address` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `range_start` int(12) unsigned NOT NULL,
  `range_end` int(12) unsigned NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci