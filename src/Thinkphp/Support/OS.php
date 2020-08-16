<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Support;

use think\facade\App;
use think\facade\Env;
use think\facade\Request;

final class OS{

	/**
	 * @var array
	 */
	public static $DEV_DOMAIN_LIST = [];

	/**
	 * 是否是开发环境
	 *
	 * @return bool
	 */
	public static function isDev(){
		return in_array(Request::host(true), self::$DEV_DOMAIN_LIST);
	}

	/**
	 * 是否是本地开发环境
	 *
	 * @return bool
	 */
	public static function isLocal(){
		return Env::get('app_env') == 'local';
	}

	/**
	 * 是否是线上环境
	 *
	 * @return bool
	 */
	public static function isProduction(){
		return Env::get('app_env') == 'production';
	}

	/**
	 * 当前所属环境
	 *
	 * @param array $envs
	 * @return bool
	 */
	public static function is(array $envs){
		$env = Env::get('app_env');
		return in_array($env, $envs);
	}

	/**
	 * 获取系统插件目录
	 *
	 * @return string
	 */
	public static function getAddonsPath(){
		return App::getRootPath()."addons";
	}

	/**
	 * 获取插件目录
	 *
	 * @return string
	 */
	public static function getPluginsPath(){
		return App::getRootPath()."plugins";
	}

	/**
	 * 获取开放目录
	 *
	 * @return string
	 */
	public static function getPublicPath(){
		return App::getRootPath()."public";
	}

	/**
	 * 获取上传目录
	 *
	 * @return string
	 */
	public static function getUploadsPath(){
		return self::getPublicPath().DIRECTORY_SEPARATOR.'uploads';
	}

	/**
	 * 获取Web公共目录
	 *
	 * @return string
	 */
	public static function getWebRootPath(){
		return Env::get('web_public_path') ?: '';
	}

	/**
	 * 获取Web公共静态资源目录
	 *
	 * @return string
	 */
	public static function getWebStaticPath(){
		return self::getWebRootPath()."/static";
	}

	/**
	 * 获取Web上传目录
	 *
	 * @return string
	 */
	public static function getWebUploadsPath(){
		return self::getWebRootPath()."/uploads";
	}

	/**
	 * 获取Web第三方插件目录
	 *
	 * @return string
	 */
	public static function getWebVendorPath(){
		return self::getWebRootPath()."/vendor";
	}

	/**
	 * 获取作用域目录
	 *
	 * @param string $resDir
	 * @return string
	 */
	public static function getWebScopePath($resDir){
		return self::getWebRootPath().$resDir.'/'.Request::module();
	}

	/**
	 * 获取Web模块下图片路径
	 *
	 * @return string
	 */
	public static function getWebImagesPath(){
		return self::getWebScopePath('images');
	}

	/**
	 * 获取Web模块下js路径
	 *
	 * @return string
	 */
	public static function getWebJsPath(){
		return self::getWebScopePath('js');
	}

	/**
	 * 获取Web模块下css路径
	 *
	 * @return string
	 */
	public static function getWebCssPath(){
		return self::getWebScopePath('css');
	}

	/**
	 * 获取Web模块下字体路径
	 *
	 * @return string
	 */
	public static function getWebFontsPath(){
		return self::getWebRootPath()."/fonts";
	}
}
