<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Hint;

/**
 * Interface HintInterface
 */
interface Hint{
	
	/**
	 * 提示成功消息
	 *
	 * @param string $msg
	 * @param string $url
	 * @param string $data
	 * @param array  $extend
	 * @return \think\Response|\Symfony\Component\HttpFoundation\Response
	 */
	public function success($msg, $url = null, $data = null, array $extend = []);
	
	/**
	 * 提示失败消息
	 *
	 * @param string $msg
	 * @param int    $code
	 * @param string $url
	 * @param array  $extend
	 * @return \think\Response|\Symfony\Component\HttpFoundation\Response
	 */
	public function error($msg, $code = 0, $url = null, array $extend = []);
	
	/**
	 * 返回成功的数据
	 *
	 * @param mixed $data
	 * @param array $extend
	 * @return \think\Response|\Symfony\Component\HttpFoundation\Response
	 */
	public function result($data = [], array $extend = []);
	
	/**
	 * 强错误提示
	 *
	 * @param string $msg
	 * @param int    $code
	 * @param string $url
	 * @param array  $extend
	 * @return \think\Response|\Symfony\Component\HttpFoundation\Response
	 */
	public function alert($msg, $code = 0, $url = null, array $extend = []);
	
	/**
	 * 直接提示成功消息
	 *
	 * @param string   $msg
	 * @param string   $url
	 * @param string   $data
	 * @param array    $extend
	 * @param callable $callback
	 */
	public function outputSuccess($msg, $url = null, $data = null, array $extend = [], callable $callback = null);
	
	/**
	 * 直接提示失败消息
	 *
	 * @param string   $msg
	 * @param int      $code
	 * @param string   $url
	 * @param array    $extend
	 * @param callable $callback
	 */
	public function outputError($msg, $code = 0, $url = null, array $extend = [], callable $callback = null);
	
	/**
	 * 直接输出强错误提示
	 *
	 * @param string   $msg
	 * @param int      $code
	 * @param string   $url
	 * @param array    $extend
	 * @param callable $callback
	 * @return \think\Response|\Symfony\Component\HttpFoundation\Response
	 */
	public function outputAlert($msg, $code = 0, $url = null, array $extend = [], callable $callback = null);
	
}
