/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : mall

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 12/11/2020 22:29:17
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`
(
    `id`          int(11) unsigned                        NOT NULL AUTO_INCREMENT,
    `key`         varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '键名',
    `type`        varchar(24) COLLATE utf8mb4_unicode_ci  NOT NULL COMMENT '类型',
    `value`       text COLLATE utf8mb4_unicode_ci         NOT NULL COMMENT '持久化缓存数据',
    `tag`         varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签名',
    `expire_time` int(11) unsigned                        NOT NULL COMMENT '有效期',
    PRIMARY KEY (`id`),
    UNIQUE KEY `caches_key_unique` (`key`) USING BTREE,
    KEY `tag` (`tag`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='持久化缓存表'
  ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
