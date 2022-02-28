<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Upload;

use think\exception\HttpException;
use think\exception\ValidateException;
use think\helper\Str;
use think\Request;
use Xin\Contracts\Auth\AuthVerifyType;
use Xin\Thinkphp\Facade\Filesystem;
use Xin\Thinkphp\Facade\Hint;
use function Qiniu\base64_urlSafeDecode;

/**
 * Trait UploadToken
 *
 * @property string callbackAction
 */
trait UploadToken
{

	/**
	 * 获取上传token
	 *
	 * @param \think\Request $request
	 * @return \think\Response
	 */
	public function token(Request $request)
	{
		Hint::shouldUseApi();

		if (!$request->isPost()) {
			throw new HttpException(404);
		}

		// 上传的文件类型
		$type = $this->uploadType($request);

		$file = $request->post('file/a');
		if (empty($file) || !is_array($file)) {
			return Hint::error("没有上传文件");
		}

		$userId = $this->request->userId(AuthVerifyType::NOT);
		$ext = $this->resolveExt($type, $file);
		$filename = Str::random(6) . '.' . $ext;
		$key = $this->savePath($type) . '/' . $userId . '/' . date('YmdHis') . '/' . $filename;

		$policy = $this->policy($request, $type);
		$token = Filesystem::disk($this->disk())->getUploadToken(
			$key, 300, $policy, true
		);

		return Hint::result([
			'key' => $key,
			'token' => $token,
			'policy' => $policy,
		]);
	}

	/**
	 * 上传策略
	 *
	 * @param \think\Request $request
	 * @param string $type
	 * @return array
	 */
	protected function policy(Request $request, $type)
	{
		// $returnBody = '{"url":"'.$domain.'/$(key)","key":"$(key)","hash":"$(etag)","fsize":$(fsize)}';
		// '{"app_id":'.$appId.',"url":"'.$domain.'/$(key)","key":"$(key)","hash":"$(etag)","fsize":$(fsize)}';

		$uploadType = config('filesystem.disks.' . $this->disk() . '.type', 'qiniu');
		if ($uploadType === 'qiniu') {
			$policy = [
				'callbackUrl' => $this->callbackUrl($request),
				'callbackBody' => $this->callbackBody($type),
				'callbackBodyType' => 'application/json',
			];

			if ($type == 'image') {
				$policy['fsizeLimit'] = 1024 * 1024 * 2;
				$policy['mimeLimit'] = 'image/*';
			} elseif ($type == 'video') {
				$policy['fsizeLimit'] = 1024 * 1024 * 10;
				$policy['mimeLimit'] = 'video/*';
			} elseif ($type == 'audio') {
				$policy['fsizeLimit'] = 1024 * 1024 * 4;
				$policy['mimeLimit'] = 'audio/*';
			}

			return $policy;
		}

		throw new HttpException(500, "暂未支持！");
	}

	/**
	 * @param string $type
	 * @param array $file
	 * @return string
	 */
	protected function resolveExt($type, array $file)
	{
		if (!isset($file['type'])) {
			throw new ValidateException("文件类型不支持上传。");
		}

		$fileType = $file['type'];
		$maps = $this->extMaps();
		if (!isset($maps[$type][$fileType])) {
			throw new ValidateException("文件类型不支持上传。");
		}

		return $maps[$type][$fileType];
	}

	/**
	 * @return \string[][]
	 */
	protected function extMaps()
	{
		return [
			'image' => [
				'image/png' => 'png',
				'image/jpeg' => 'jpeg',
				'image/gif' => 'gif',
				'image/bmp' => 'bmp',
			],
			'audio' => [
				'audio/mp3' => 'mp3',
				'audio/wma' => 'wma',
				'audio/ogg' => 'ogg',
			],
			'video' => [
				'video/mp4' => 'mp4',
			],
		];
	}

	/**
	 * @param \think\Request $request
	 * @return string
	 */
	protected function callbackUrl(Request $request)
	{
		return $request->domain() . url($this->callbackAction());
	}

	/**
	 * @param string $type
	 * @return string
	 */
	protected function callbackBody($type)
	{
		$url = config('filesystem.disks.' . $this->disk() . '.url');

		return json_encode([
			"user_id" => $this->auth->getUserId(AuthVerifyType::NOT),
			"user_type" => property_exists($this, 'userType') ? $this->userType : '',
			"type" => $type,
			"url" => "{$url}/$(key)",
			"key" => "$(key)",
			"hash" => "$(etag)",
			"size" => "$(fsize)",
			"sha1" => "$(bodySha1)",
			"mime" => "$(mimeType)",
		], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		// return '{"type":"'.$type.'","url":"'.$url.'/$(key)","key":"$(key)","hash":"$(etag)","size":$(fsize),"sha1":"$
		//(bodySha1)","mime":"$(mimeType)"}';
	}

	/**
	 * @param \think\Request $request
	 * @return \think\Response
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function saveByToken(Request $request)
	{
		if (!$request->isPost()) {
			throw new HttpException(404);
		}

		Hint::shouldUseApi();

		$data = $request->post();
		$type = $data['type'];
		$sha1 = $data['sha1'];
		$str = base64_urlSafeDecode($data['hash']);
		$str = substr($str, 1);
		$sha1 = $this->string2Hex($str);

		$info = $this->findBySHA1($type, $sha1);
		if (!empty($info)) {
			return Hint::result([
				'id' => $info['id'],
				'path' => $info['path'],
			]);
		}

		$saveData = [
			'path' => $data['url'],
			'md5' => '',
			'sha1' => $sha1,
			'size' => $data['size'],
			'type' => $data['type'],
			'mime_type' => $data['mime'],
		];
		if (method_exists($this, 'buildSaveData')) {
			$saveData = $this->buildSaveData($saveData);
		}

		$data = $this->saveDb($type, $saveData);

		return Hint::result($data);
	}

	/**
	 * 字符串转16进制
	 *
	 * @param string $string
	 * @return string
	 */
	private function string2Hex($string)
	{
		$hex = '';
		$strLength = strlen($string);
		for ($i = 0; $i < $strLength; $i++) {
			$hex .= dechex(ord($string[$i]));
		}

		return $hex;
	}

	/**
	 * @return string
	 */
	protected function callbackAction()
	{
		if (property_exists($this, 'callbackAction')) {
			return $this->callbackAction;
		}

		return "callback";
	}

	/**
	 * @param string $method
	 * @param array $args
	 * @return \think\Response
	 */
	public function __call($method, $args)
	{
		if ($method === $this->callbackAction()) {
			return $this->saveByToken(app()->request);
		}

		return Hint::result();
	}

}
