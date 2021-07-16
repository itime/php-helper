<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Foundation;

interface Application{

	/**
	 * 是否是开发环境
	 *
	 * @return bool
	 */
	public function isDevelop();

	/**
	 * 是否是本地环境
	 *
	 * @return bool
	 */
	public function isLocal();

	/**
	 * 是否是生产环境
	 *
	 * @return bool
	 */
	public function isProduction();

	/**
	 * 是否是所属环境
	 *
	 * @param string ...$env
	 * @return bool
	 */
	public function isEnv(...$env);

	/**
	 * @return string
	 */
	public function version();

	/**
	 * 网站根目录
	 *
	 * @param string $path
	 * @return string
	 */
	public function rootPath($path = null);

	/**
	 * Web根目录
	 *
	 * @param string $path
	 * @return string
	 */
	public function webRootPath($path = null);

	/**
	 * 存储目录
	 *
	 * @param string $path
	 * @return string
	 */
	public function storagePath($path = null);

	/**
	 * 插件目录
	 *
	 * @param string $path
	 * @return string
	 */
	public function pluginPath($path = null);

	/**
	 * @param string $path
	 * @return string
	 */
	public function runtimePath($path = null);

	/**
	 * 第三方资源目录
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function assetVendorPath($path = null);

	/**
	 * 模块域目录
	 *
	 * @param string $path
	 * @return string
	 */
	public function assetScopePath($path = null);

	/**
	 * 图片路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function assetImagesPath($path = null);

	/**
	 * 脚本路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function assetScriptsPath($path = null);

	/**
	 * 样式路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function assetStylesPath($path = null);

	/**
	 * 字体路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function assetFontsPath($path = null);

}
