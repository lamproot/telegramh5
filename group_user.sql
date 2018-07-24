/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50621
 Source Host           : localhost
 Source Database       : telegram

 Target Server Type    : MySQL
 Target Server Version : 50621
 File Encoding         : utf-8

 Date: 07/14/2018 22:08:50 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `group_user`
-- ----------------------------
DROP TABLE IF EXISTS `group_user`;
CREATE TABLE `group_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `chat_bot_id` int(10) NOT NULL,
  `chat_id` varchar(14) NOT NULL DEFAULT '',
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1 入群 2 退群',
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `from_id` varchar(255) DEFAULT '' COMMENT '用户ID',
  `first_name` varchar(255) DEFAULT '',
  `last_name` varchar(255) DEFAULT '',
  `from_username` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `chat_bot_id` (`chat_bot_id`),
  KEY `chat_id` (`chat_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `group_user`
-- ----------------------------
BEGIN;
INSERT INTO `group_user` VALUES ('1', '2', '-1001249040089', '1', null, null, '520439801', 'dasdasd', '', '');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
