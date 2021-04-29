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

 Date: 23/08/2020 16:28:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for address
-- ----------------------------
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address`
(
    `id`          int(11) UNSIGNED                                        NOT NULL AUTO_INCREMENT COMMENT '主键id',
    `user_id`     int(11) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT '用户id',
    `name`        varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '' COMMENT '收货人姓名',
    `phone`       varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '' COMMENT '联系电话',
    `province`    varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '所在省份',
    `city`        varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '所在城市',
    `region`      varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '所在区',
    `district`    varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '新市辖区(该字段用于记录region表中没有的市辖区)',
    `detail`      varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
    `is_default`  tinyint(4) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT '是否是默认地址',
    `create_time` int(11) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT '创建时间',
    `update_time` int(11) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci COMMENT = '用户收货地址表'
  ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
