<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Upload;

use think\exception\HttpException;
use think\facade\Db;
use think\file\UploadedFile;

trait UploadFinder{
	
	/**
	 * 查找文件
	 *
	 * @param string                   $type
	 * @param \think\file\UploadedFile $file
	 * @return array|\think\Model|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function findByFile($type, UploadedFile $file){
		return $this->findBySHA1($type, $file->sha1());
	}
	
	/**
	 * 查找文件 - SHA1
	 *
	 * @param string $type
	 * @param string $sha1
	 * @return array|\think\Model|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function findBySHA1($type, $sha1){
		return $this->db($type)->where([
			'sha1' => $sha1,
		])->find();
	}
	
	/**
	 * 查找文件 - MD5
	 *
	 * @param string $type
	 * @param string $md5
	 * @return array|\think\Model|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function findByMD5($type, $md5){
		return $this->db($type)->where([
			'md5' => $md5,
		])->find();
	}
	
	/**
	 * 保存数据
	 *
	 * @param string $type
	 * @param array  $data
	 * @return array
	 */
	protected function saveDb($type, $data){
		$this->onSaveData($type, $data);
		
		$data['create_time'] = request()->time();
		
		$id = $this->db($type)->insertGetId($data);
		if($id < 1){
			throw new HttpException(500, "文件保存失败！");
		}
		
		return [
			'id'   => $id,
			'path' => $data['path'],
		];
	}
	
	/**
	 * 数据保存事件
	 *
	 * @param string $type
	 * @param array  $data
	 */
	protected function onSaveData($type, &$data){ }
	
	/**
	 * @param null $type
	 * @return \think\facade\Db|\think\db\Query
	 */
	protected function db($type = null){
		return Db::name($type !== 'image' ? 'file' : 'image');
	}
}
