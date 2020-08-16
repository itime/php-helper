<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Foundation;

/**
 * Interface Application
 */
interface Application{
	
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
