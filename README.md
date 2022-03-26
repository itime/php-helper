# Framework

## 介绍

**此仓库为内部使用，切勿引用到自身产品中使用**

PHP项目日常开发必备基础库，它包含了基础常用的工具库（字符串、集合、数值、函数、服务器、加密）。
在此基础上进一步封装了用户授权体系、常规业务支持、微信API、菜单管理器、插件管理器、小票打印机、ThinkPHP增强，
它将进一步演变成一个基础库，对产品业务提供强有力的支撑。

## 安装教程

composer require xin/helper

## 计划清单

- 菜单管理器优化
- 权限管理器优化
- 插件管理器优化
- ~~移除支付宝SDK支持~~，迁移到新仓库[php-alipay-adapter](https://gitee.com/liuxiaojinla/php-alipay-adapter)
- 用户鉴权优化
- 用户授权优化
- 支付器
- 提升EasyWechat版本
- 子包抽离