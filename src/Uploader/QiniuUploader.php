<?php

namespace Xin\Uploader;

use Xin\Support\Arr;

class QiniuUploader extends AbstractUploader
{

	/**
	 * @inheritDoc
	 */
	public function file($scene, $targetPath, \SplFileInfo $file, array $options = [])
	{
		return $this->filesystem->put($targetPath, file_get_contents($file->getRealPath()));
	}

	/**
	 * @inheritDoc
	 */
	public function token($scene, $targetPath, array $options = [])
	{
		$policy = $this->makePolicy($scene, $targetPath, $options);

		$expires = $this->getExpire($options);

		$token = $this->filesystem->getUploadToken(
			$targetPath, $expires, $policy, true
		);

		return [
			'key' => $targetPath,
			'token' => $token,
		];
	}

	/**
	 * @param string $scene
	 * @param string $filename
	 * @param array $options
	 * @return array
	 */
	protected function makePolicy($scene, $filename, array $options)
	{
		$policy = [
			'callbackUrl' => $this->getCallbackUrl($scene, $options),
			'callbackBody' => $this->makeCallbackBody($scene, $options),
			'callbackBodyType' => 'application/json',
		];

		if ($size = Arr::get($options, 'size')) {
			$policy['fsizeLimit'] = $size;
		}

		if ($mimes = Arr::get($options, 'mimes')) {
			$policy['mimeLimit'] = is_array($mimes) ? implode(";", $mimes) : $mimes;
		}

		return $policy;
	}


	/**
	 * @param string $scene
	 * @param array $options
	 * @return string
	 */
	protected function getCallbackUrl($scene, array $options)
	{
		return Arr::get($options, 'callback_url', '');
	}

	/**
	 * @param string $scene
	 * @param array $options
	 * @return string
	 */
	protected function makeCallbackBody($scene, array $options)
	{
		$cdn = $this->filesystem->getAdapter()->url('');
		$cdn = rtrim($cdn, '/');
		$userData = $options['user_data'] ?? [];

		$data = array_merge([
			'type' => $scene,
			'scene' => $scene,
			'url' => "{$cdn}/$(key)",
			'key' => '$(key)',
			'hash' => '$(etag)',
			'size' => '$(fsize)',
			'sha1' => '$(bodySha1)',
			'mime' => '$(mimeType)',
		], $userData);

		return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	/**
	 * @param array $options
	 * @return int
	 */
	protected function getExpire(array $options)
	{
		return Arr::get($options, 'expire', 300);
	}
}