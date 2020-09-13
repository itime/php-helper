SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for visit_ip
-- ----------------------------
DROP TABLE IF EXISTS `visit_ip`;
CREATE TABLE `visit_ip`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `time` int(11) UNSIGNED NOT NULL,
  `referer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `user_agent` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `create_time` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `app_id`(`app_id`, `ip`, `time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 559 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
