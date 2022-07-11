<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Model;

use think\db\Query;
use Xin\Support\SQL;

/**
 * @mixin \think\Model
 * @method self simple()
 * @method self search(array $data, array $withoutFields = [])
 */
trait Modelable
{
	/**
	 * Append attributes to query when building a query.
	 *
	 * @param array $append
	 * @return $this
	 */
	public function append(array $append = [])
	{
		$this->append = array_unique(
			array_merge($this->append, $append)
		);

		return $this;
	}

	/**
	 * Set the accessors to append to model arrays.
	 *
	 * @param array $appends
	 * @return $this
	 */
	public function setAppends(array $appends)
	{
		$this->append = $appends;

		return $this;
	}

	/**
	 * Remove the accessors to append to model arrays.
	 *
	 * @param array $appends
	 * @return $this
	 */
	public function removeAppends(array $appends)
	{
		foreach ($appends as $append) {
			if (($index = array_search($append, $this->append, true)) !== false) {
				unset($this->append[$index]);
			}
		}

		return $this;
	}

	/**
	 * Return whether the accessor attribute has been appended.
	 *
	 * @param string $attribute
	 * @return bool
	 */
	public function hasAppended($attribute)
	{
		return in_array($attribute, $this->append, true);
	}

	/**
	 * Get the hidden attributes for the model.
	 *
	 * @return array
	 */
	public function getHidden()
	{
		return $this->hidden;
	}

	/**
	 * Set the hidden attributes for the model.
	 *
	 * @param array $hidden
	 * @return $this
	 */
	public function setHidden(array $hidden)
	{
		$this->hidden = $hidden;

		return $this;
	}

	/**
	 * Make the given, typically visible, attributes hidden.
	 *
	 * @param array $attributes
	 * @return $this
	 */
	public function makeHidden(array $attributes)
	{
		$this->hidden = array_merge(
			$this->hidden, $attributes
		);

		return $this;
	}

	/**
	 * Make the given, typically visible, attributes hidden if the given truth test passes.
	 *
	 * @param bool|Closure $condition
	 * @param array|string|null $attributes
	 * @return $this
	 */
	public function makeHiddenIf($condition, $attributes)
	{
		return value($condition, $this) ? $this->makeHidden($attributes) : $this;
	}

	/**
	 * Get the visible attributes for the model.
	 *
	 * @return array
	 */
	public function getVisible()
	{
		return $this->visible;
	}

	/**
	 * Set the visible attributes for the model.
	 *
	 * @param array $visible
	 * @return $this
	 */
	public function setVisible(array $visible)
	{
		$this->visible = $visible;

		return $this;
	}

	/**
	 * Make the given, typically hidden, attributes visible.
	 *
	 * @param array $attributes
	 * @return $this
	 */
	public function makeVisible(array $attributes)
	{
		$this->hidden = array_diff($this->hidden, $attributes);

		if (!empty($this->visible)) {
			$this->visible = array_merge($this->visible, $attributes);
		}

		return $this;
	}

	/**
	 * Make the given, typically hidden, attributes visible if the given truth test passes.
	 *
	 * @param bool|Closure $condition
	 * @param array $attributes
	 * @return $this
	 */
	public function makeVisibleIf($condition, array $attributes)
	{
		return value($condition, $this) ? $this->makeVisible($attributes) : $this;
	}

	/**
	 * 获取数据列表
	 *
	 * @param mixed $query
	 * @param array $options
	 * @return \think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @deprecated
	 */
	public static function getList($query = [], $options = [])
	{
		return static::plainQuery($query, $options)->select();
	}

	/**
	 * 获取数据分页
	 *
	 * @param mixed $query
	 * @param array $options
	 * @param mixed $listRows
	 * @param bool $simple
	 * @return \think\Paginator
	 * @throws \think\db\exception\DbException
	 * @deprecated
	 */
	public static function getPaginate($query, $options = [], $listRows = 15, $simple = false)
	{
		return static::plainQuery($query, $options)->paginate($listRows, $simple);
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
	 * @deprecated
	 */
	public static function getSimpleInfo($query, $options = [])
	{
		$info = static::plainQuery($query, $options)->find();

		return static::resolvePlain($info, $options);
	}

	/**
	 * 获取简单的信息数据
	 *
	 * @param int $id
	 * @param array $options
	 * @return self
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @deprecated
	 */
	public static function getSimpleInfoById($id, $options = [])
	{
		$info = static::plainQuery(null, $options)->find($id);

		return static::resolvePlain($info, $options);
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
	 * @deprecated
	 */
	public static function getPlain($query, $options = [])
	{
		return self::getSimpleInfo($query, $options);
	}

	/**
	 * 获取简单的信息数据
	 *
	 * @param int $id
	 * @param array $options
	 * @return self
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @deprecated
	 */
	public static function getPlainById($id, $options = [])
	{
		return self::getSimpleInfoById($id, $options);
	}

	/**
	 * 简单数据额外处理
	 *
	 * @param self $info
	 * @param array $options
	 * @return self
	 * @deprecated
	 */
	protected static function resolvePlain($info, $options = [])
	{
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
	 * @deprecated
	 */
	public static function getDetail($query, $with = [], $options = [])
	{
		$query = static::with($with)->where($query);

		$info = static::applyOptions($query, $options)->find();

		return static::resolveDetail($info, $options);
	}

	/**
	 * 根据主键ID获取数据详细信息
	 *
	 * @param int $id
	 * @param array $with
	 * @param array $options
	 * @return self
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @deprecated
	 */
	public static function getDetailById($id, $with = [], $options = [])
	{
		$info = static::applyOptions(static::with($with), $options)->find($id);

		return static::resolveDetail($info, $options);
	}

	/**
	 * 详细数据额外处理
	 *
	 * @param self $info
	 * @param array $options
	 * @return self
	 * @deprecated
	 */
	protected static function resolveDetail($info, $options = [])
	{
		return $info;
	}

	/**
	 * 获取列表要查询的字段列表，一般用于接口列表查询
	 *
	 * @return array
	 */
	public static function getSimpleFields()
	{
		return static::getPlainFields();
	}

	/**
	 * 获取列表要查询的字段列表，一般用于接口列表查询
	 *
	 * @return array
	 * @deprecated
	 */
	public static function getPlainFields()
	{
		return [];
	}

	/**
	 * 获取要公开的字段列表，一般用于管理查询数据
	 * @return string[]
	 */
	public static function getPublicFields()
	{
		return static::getSimpleFields();
	}

	/**
	 * 获取要搜索的字段列表
	 * @return array
	 */
	public static function getSearchFields()
	{
		$allowSearchFields = static::getSimpleFields();

		$keywordField = static::getSearchKeywordParameter();
		if ($keywordField) {
			return array_merge($allowSearchFields, is_array($keywordField) ? [
				$keywordField[0] => $keywordField[1]
			] : [$keywordField]);
		}

		return $allowSearchFields;
	}

	/**
	 * 获取关键字搜索参数
	 * @return string
	 */
	public static function getSearchKeywordParameter()
	{
		return "keywords";
	}

	/**
	 * 简单数据查询作用域
	 * @param \think\db\Query $query
	 * @deprecated
	 */
	public function scopePlainList(Query $query)
	{
		$query->field(static::getSimpleFields());
	}

	/**
	 * 简单数据查询作用域
	 * @param \think\db\Query $query
	 */
	public function scopeSimple(Query $query)
	{
		$query->field(static::getSimpleFields() ?: static::getPlainFields());
	}

	/**
	 * 搜索数据作用域
	 * @param Query $query
	 * @param array $data
	 * @param array $withoutFields
	 * @return void
	 */
	public function scopeSearch(Query $query, array $data, array $withoutFields = [])
	{
		$data = array_filter($data, 'filled');

		$fields = array_diff(static::getSearchFields(), $withoutFields);
		$fields = array_intersect($fields, array_keys($data));

		$query->withSearch($fields, $data);
	}

	/**
	 * 标题搜索器
	 * @param Query $query
	 * @param string $value
	 * @return void
	 */
	public function searchKeywordsAttr(Query $query, $value)
	{
		$values = SQL::keywords($value);
		if (empty($values)) {
			return;
		}

		$query->where(implode('|', static::getSearchKeywordFields()), 'like', $values);
	}

	/**
	 * 获取关键字搜索字段
	 * @return string[]
	 */
	public static function getSearchKeywordFields()
	{
		return ["title"];
	}

	/**
	 * 解析基础查询对象
	 *
	 * @param mixed $query
	 * @param array $options
	 * @return \think\db\Query|\think\Model
	 * @deprecated
	 */
	public static function newPlainQuery($query = null, $options = [])
	{
		return static::plainQuery($query, $options);
	}

	/**
	 * 获取基础查询对象
	 *
	 * @param mixed $query
	 * @param array $options
	 * @return \think\db\Query|\think\Model
	 */
	public static function simpleQuery($query = null, $options = [])
	{
		$fields = static::getSimpleFields();
		if (isset($options['field'])) {
			if (is_callable($options['field'])) {
				$fields = $options['field']($fields);
			} else {
				$fields = $options['field'];
			}
			unset($options['field']);
		}

		$model = new static;
		$newQuery = $model->field($fields);

		if ($query) {
			$newQuery->where($query);
		}

		return static::applyOptions($newQuery, $options);
	}

	/**
	 * 获取基础查询对象
	 *
	 * @param mixed $query
	 * @param array $options
	 * @return \think\db\Query|\think\Model
	 * @deprecated
	 */
	public static function plainQuery($query = null, $options = [])
	{
		return static::simpleQuery($query, $options);
	}

	/**
	 * 应用 options
	 *
	 * @param mixed $baseQuery
	 * @param array $options
	 * @return \think\Model|\think\db\Query
	 */
	public static function applyOptions($baseQuery, $options = null)
	{
		if ($options === null) {
			return $baseQuery;
		}

		if (is_callable($options)) {
			return $options($baseQuery);
		}

		foreach ($options as $method => $option) {
			if (method_exists($baseQuery, $method)) {
				if (is_array($option) && in_array($method, ['limit', 'page'])) {
					$baseQuery->$method(...$option);
				} else {
					$baseQuery->$method($option);
				}
			}
		}

		return $baseQuery;
	}

}
