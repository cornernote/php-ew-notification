/**
 * PHP Notifications
 *
 * @author Brett O'Donnell - cornernote@gmail.com
 * @copyright 2013, All Rights Reserved
 */

CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  `target_uid` int(11) DEFAULT NULL,
  `target_rid` int(11) DEFAULT NULL,
  `target_gid` int(11) DEFAULT NULL,
  `target_forum_uid` int(11) DEFAULT NULL,
  `target_phone` varchar(255) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `delivery_type_id` int(11) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `sent` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `notification_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `rid` int(11) DEFAULT NULL,
  `gid` int(11) DEFAULT NULL,
  `forum_uid` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `delivery_type_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `notification_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `notification_type` VALUES ('1', 'Tickets');
INSERT INTO `notification_type` VALUES ('2', 'Manual Transactions');
INSERT INTO `notification_type` VALUES ('3', 'Account Recovery');
INSERT INTO `notification_type` VALUES ('4', 'Character Migrations');
INSERT INTO `notification_type` VALUES ('5', 'Custom VIP');
INSERT INTO `notification_type` VALUES ('6', 'Identities');
INSERT INTO `notification_type` VALUES ('7', 'Billing Records');
INSERT INTO `notification_type` VALUES ('8', 'Redmine Tasks');
INSERT INTO `notification_type` VALUES ('9', 'Action Logging (high priority alerts only)');

CREATE TABLE `notification_delivery_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7;

INSERT INTO `notification_delivery_type` VALUES ('1', 'Mail');
INSERT INTO `notification_delivery_type` VALUES ('2', 'Email');
INSERT INTO `notification_delivery_type` VALUES ('3', 'PM');
INSERT INTO `notification_delivery_type` VALUES ('4', 'Announcement');
INSERT INTO `notification_delivery_type` VALUES ('5', 'GMAnnouncement');
INSERT INTO `notification_delivery_type` VALUES ('6', 'SMS');
