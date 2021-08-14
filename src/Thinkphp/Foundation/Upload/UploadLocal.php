<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Upload;

use think\exception\ValidateException;
use think\facade\Filesystem;
use think\file\UploadedFile;
use think\Request;
use Xin\Thinkphp\Facade\Hint;

/**
 * @method array buildSaveData(array $data, UploadedFile $file)
 */
trait UploadLocal{

	/**
	 * 上传文件
	 *
	 * @param \think\Request $request
	 * @return \think\Response
	 */
	public function upload(Request $request){
		Hint::shouldUseApi();

		// 获取表单上传文件
		$file = $request->file($this->uploadName());
		if(empty($file)){
			return Hint::error("没有上传文件");
		}

		// 上传的文件类型
		$type = $this->uploadType($request);

		// 验证文件合法性
		$this->validateFile($type, $file);

		// 文件是否已存在
		$info = $this->findByFile($type, $file);
		if(empty($info)){
			$info = $this->putFile($type, $file);
		}

		return Hint::success("已上传！", null, $info);
	}

	/**
	 * 保存文件
	 *
	 * @param string                   $type
	 * @param \think\file\UploadedFile $file
	 * @return array
	 */
	protected function putFile($type, UploadedFile $file){
		$savePath = Filesystem::disk($this->disk())->putFile(
			$this->savePath($type), $file, $this->saveRule()
		);

		$publicPath = config('filesystem.disks.'.$this->disk().'.url').'/'.str_replace("\\", "/", $savePath);

		$saveData = [
			'path' => $publicPath,
			'md5'  => $file->md5(),
			'sha1' => $file->sha1(),
			'size' => $file->getSize(),
			'type' => $file->getMime(),
		];

		if(method_exists($this, 'buildSaveData')){
			$saveData = $this->buildSaveData($saveData, $file);
		}

		return $this->saveDb($type, $saveData);
	}

	/**
	 * 验证文件合法性
	 *
	 * @param string                   $type
	 * @param \think\file\UploadedFile $file
	 * @param bool                     $failException
	 * @return bool
	 */
	protected function validateFile($type, UploadedFile $file, $failException = true){
		return validate([
			$this->uploadName() => $this->validateFileRule($type),
		], [], false, $failException)->rule([], [
			$this->uploadName() => '文件',
		])->check([
			$this->uploadName() => $file,
		]);
	}

	/**
	 * 文件保存验证规则
	 *
	 * @param string|null $type
	 * @return string
	 */
	protected function validateFileRule($type){
		if($type == 'image'){
			$w = \request()->param('w/d');
			$h = \request()->param('h/d');
			$imageArea = '';
			if($w > 0 && $h > 0){
				$imageArea = ":{$w},{$h}";
			}
			// size：2m
			return 'fileSize:2097152|fileExt:jpg,png|fileMime:image/png,image/jpeg|image'.$imageArea;
		}elseif($type === 'video'){ // size：10m
			return "fileSize:104488960|fileExt:mp4|fileMime:video/mp4";
		}elseif($type === 'audio'){ // size：4m
			return "fileSize:4194304|fileExt:mp3,wma,ogg|fileMime:audio/mp3,audio/wma,audio/ogg";
		}

		throw new ValidateException("不支持的文件类型");
	}
}
