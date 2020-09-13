SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for stat
-- ----------------------------
DROP TABLE IF EXISTS `stat`;
CREATE TABLE `stat`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) UNSIGNED NOT NULL COMMENT '应用ID',
  `name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '唯一标识',
  `value` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数量',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`name`, `app_id`, `create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 899 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '统计表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
