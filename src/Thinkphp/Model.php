<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp;

class Model extends \think\Model{
	
	/**
	 * 获取数据列表
	 *
	 * @param mixed $query
	 * @param array $options
	 * @return \think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function getList($query, $options = []){
		return static::newPlainQuery($query, $options)->select();
	}
	
	/**
	 * 获取数据分页
	 *
	 * @param mixed $query
	 * @param int   $listRows
	 * @param array $options
	 * @return \think\Paginator
	 * @throws \think\db\exception\DbException
	 */
	public static function getPaginate($query, $listRows = 15, $options = []){
		return static::newPlainQuery($query, $options)->paginate($listRows);
	}
	
	/**
	 * 获取简单的信息数据
	 *
	 * @param mixed $query
	 * @param array $options
	 * @return self
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function getPlainInfo($query, $options = []){
		$info = static::newPlainQuery($query, $options)->find();
		
		return static::resolvePlainInfo($info, $options);
	}
	
	/**
	 * 获取简单的信息数据
	 *
	 * @param int   $id
	 * @param array $options
	 * @return self
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function getPlainInfoById($id, $options = []){
		$info = static::newPlainQuery(null, $options)->find($id);
		
		return static::resolvePlainInfo($info, $options);
	}
	
	/**
	 * 简单数据额外处理
	 *
	 * @param self  $info
	 * @param array $options
	 * @return self
	 */
	protected static function resolvePlainInfo($info, $options = []){
		return $info;
	}
	
	/**
	 * 获取数据详细信息
	 *
	 * @param mixed $query
	 * @param array $with
	 * @param array $options
	 * @return self
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function getDetail($query, $with = [], $options = []){
		$query = static::with($with)->where($query);
		
		$info = static::applyOptions($query, $options)->find();
		
		return static::resolveDetail($info, $options);
	}
	
	/**
	 * 根据主键ID获取数据详细信息
	 *
	 * @param int   $id
	 * @param array $with
	 * @param array $options
	 * @return self
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function getDetailById($id, $with = [], $options = []){
		$info = static::applyOptions(static::with($with), $options)->find($id);
		
		return static::resolveDetail($info, $options);
	}
	
	/**
	 * 详细数据额外处理
	 *
	 * @param self  $info
	 * @param array $options
	 * @return self
	 */
	protected static function resolveDetail($info, $options = []){
		return $info;
	}
	
	/**
	 * 解析基础查询对象
	 *
	 * @param mixed $query
	 * @param array $options
	 * @return \think\db\Query|\think\Model
	 */
	public static function newPlainQuery($query, $options = []){
		$fields = static::getPlainFields();
		
		$newQuery = static::field($fields);
		
		if($query){
			$newQuery->where($query);
		}
		
		return static::applyOptions($newQuery, $options);
	}
	
	/**
	 * 获取要查找的简单数据字段列表
	 *
	 * @return array
	 */
	public static function getPlainFields(){
		return [];
	}
	
	/**
	 * 应用 options
	 *
	 * @param \think\Model|\think\db\Query $baseQuery
	 * @param array                        $options
	 * @return \think\Model|\think\db\Query
	 */
	public static function applyOptions($baseQuery, $options = null){
		if($options === null){
			return $baseQuery;
		}
		
		if(is_callable($options)){
			return $options($baseQuery);
		}else{
			foreach($options as $method => $option){
				if(method_exists($baseQuery, $method)){
					$baseQuery->$method($option);
				}
			}
		}
		
		return $baseQuery;
	}
}
