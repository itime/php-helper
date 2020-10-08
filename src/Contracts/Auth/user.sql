SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `gender` tinyint(1) NOT NULL DEFAULT 1 COMMENT '性别',
  `app_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用ID',
  `third_appid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '三方应用ID',
  `openid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户唯一标识',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户头像地址',
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT '手机号',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT 'Email',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `language` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '语言',
  `country` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '国家',
  `province` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '城市',
  `source` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源信息',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推荐用户id',
  `integral` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分',
  `balance` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '余额信息',
  `login_count` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录次数',
  `login_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `login_ip` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录IP',
  `sys_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '同步用户信息时间',
  `create_ip` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册IP',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `openid`(`openid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 241 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '终端用户表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
