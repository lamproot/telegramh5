/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50627
 Source Host           : localhost
 Source Database       : telegram

 Target Server Type    : MySQL
 Target Server Version : 50627
 File Encoding         : utf-8

 Date: 05/16/2018 20:45:55 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `user_tokenman_log`
-- ----------------------------
DROP TABLE IF EXISTS `user_tokenman_log`;
CREATE TABLE `user_tokenman_log` (
  `chat_bot_id` int(10) NOT NULL COMMENT '机器人ID',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `from_id` int(11) NOT NULL COMMENT '发送用户ID',
  `first_name` varchar(255) NOT NULL,
  `from_username` varchar(255) NOT NULL COMMENT '发送用户名字',
  `last_name` varchar(255) NOT NULL,
  `ip`  varchar(255) NOT NULL,
  `agent` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



ALTER TABLE group_activity ADD activate_type TINYINT(2) NOT NULL DEFAULT 0 COMMENT '激活类型';
ALTER TABLE group_activity ADD activity_tinyurl varchar(255) NOT NULL DEFAULT '' COMMENT '邀请地址配置';

