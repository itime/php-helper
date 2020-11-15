<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

/**
 * Trait InteractsRepository
 *
 * @mixin \think\Model
 */
trait InteractsRepository{
	
	/**
	 * 根据主键获取一条数据
	 *
	 * @param mixed $identifier
	 * @return array|\think\Model|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveById($identifier){
		return $this->find($identifier);
	}
	
	/**
	 * 根据凭证获取一条数据
	 *
	 * @param mixed $credentials
	 * @param array $options
	 * @return array|\think\Model|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveByCredentials($credentials, array $options = []){
		return $this->resolveOptions($this, $options)->where($credentials)->find();
	}
	
	/**
	 * 解析 Options
	 *
	 * @param \think\db\Query|\think\Model $query
	 * @param array                        $options
	 * @return \think\db\Query
	 */
	protected function resolveOptions($query, array $options){
		foreach($options as $key => $value){
			$query->setOption($key, $value);
		}
		
		return $query;
	}
}
