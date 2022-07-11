<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use app\admin\Controller;
use think\db\Query;
use Xin\Contracts\Plugin\Factory as PluginManager;
use Xin\Contracts\Plugin\PluginInfo;
use Xin\Contracts\Plugin\PluginInfo as PluginInfoContract;
use Xin\Contracts\Plugin\PluginNotFoundException;
use Xin\Support\Arr;
use Xin\Support\File;
use Xin\Support\Str;
use Xin\Thinkphp\Facade\Hint;
use Xin\Thinkphp\Facade\Menu;
use Xin\Thinkphp\Foundation\Controller\PageCURD;

class PluginController extends Controller
{
	use PageCURD;

	/**
	 * @var string
	 */
	protected $keywordField = 'title';

	/**
	 * 列表查询
	 *
	 * @param \think\db\Query $query
	 */
	protected function querySelect(Query $query)
	{
		$install = $this->request->param('install/d', 0);
		$query->when($install, ['install' => $install === 1 ? 1 : 0]);

		$this->assign('install', $install);
	}

	/**
	 * 安装插件
	 *
	 * @param PluginManager $pluginManager
	 * @return \think\Response
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function install(PluginManager $pluginManager)
	{
		/** @var DatabasePlugin $info */
		$info = $this->findIsEmptyAssert();
		if ($info->install) {
			return Hint::success("应用已安装！");
		}

		if (!$info->local_version) {
			return Hint::error("应用已删除！");
		}

		try {
			$pluginInfo = $pluginManager->installPlugin($info->name);

			$this->updateInfo($pluginInfo);

			// 更新配置
			$info->save([
				'install' => 1,
				'version' => $pluginInfo->getVersion(),
				'config' => $pluginInfo->getConfigTemplate((array)$info->config),
			]);
		} catch (\Exception $e) {
			return Hint::error($e->getMessage());
		}

		return Hint::success("应用已安装！");
	}

	/**
	 * 卸载插件
	 *
	 * @param PluginManager $pluginManager
	 * @return \think\Response
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function uninstall(PluginManager $pluginManager)
	{
		/** @var DatabasePlugin $info */
		$info = $this->findIsEmptyAssert();
		if (!$info->install) {
			return Hint::success("应用已卸载！");
		}

		try {
			$pluginInfo = $pluginManager->uninstallPlugin($info->name);

			$pluginDetail = $pluginInfo->getInfo();

			// 卸载事件
			if (isset($pluginDetail['events'])) {
				DatabaseEvent::unmountAddon($info->name);
			}

			// 配置菜单
			if (isset($pluginDetail['menus'])) {
				$this->setupMenus(false, $pluginDetail['menus'], $pluginInfo);
			}

			$info->save(['install' => 0]);
		} catch (PluginNotFoundException $e) {
			DatabaseEvent::unmountAddon($info->name);
			$info->save(['install' => 0]);

			return Hint::success("应用目录已被删除！");
		} catch (\Exception $e) {
			return Hint::error($e->getMessage());
		}

		return Hint::success("应用已卸载！");
	}

	/**
	 * 升级插件
	 *
	 * @param \Xin\Contracts\Plugin\Factory $pluginManager
	 * @return \think\Response
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function upgrade(PluginManager $pluginManager)
	{
		/** @var DatabasePlugin $info */
		$info = $this->findIsEmptyAssert();
		if (!$info->install) {
			return Hint::success("应用已卸载！");
		}

		if (!$info->local_version) {
			return Hint::error("应用已删除！");
		}

		try {
			$pluginInfo = $pluginManager->plugin($info->name);
			$this->updateInfo($pluginInfo);
			$info->save([
				'version' => $pluginInfo->getVersion(),
			]);
		} catch (\Exception $e) {
			return Hint::error($e->getMessage());
		}

		return Hint::success("已更新信息");
	}

	/**
	 * 更新插件信息
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function updateInfo(PluginInfo $pluginInfo)
	{
		// 获取插件描述信息
		$pluginDetail = $pluginInfo->getInfo();

		// 生成静态资源软链接
		$this->createStaticSymlink($pluginInfo);

		// 安装事件
		if (isset($pluginDetail['events'])) {
			DatabaseEvent::mountAddon($pluginInfo->getName(), $pluginDetail['events']);
		}

		// 配置菜单
		$this->setupMenus(true, $pluginInfo);
	}

	/**
	 * 设置菜单
	 *
	 * @param bool $isAdd
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 */
	protected function setupMenus($isAdd, PluginInfoContract $pluginInfo)
	{
		$menuGuards = array_keys($this->app->config->get('menu.menus'));
		foreach ($menuGuards as $guard) {
			if ($isAdd) {
				$menusFilename = $pluginInfo->path($guard) . "menus.php";
				if (!file_exists($menusFilename)) {
					continue;
				}
				$menusData = require_once $menusFilename;
				Menu::menu($guard)->puts($menusData, $pluginInfo->getName());
			} else {
				Menu::menu($guard)->forget([
					'plugin' => $pluginInfo->getName(),
				]);
			}
		}
	}

	/**
	 * 插件配置
	 *
	 * @param \Xin\Contracts\Plugin\Factory $pluginManager
	 * @return \think\Response|string
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function config(PluginManager $pluginManager)
	{
		/** @var DatabasePlugin $info */
		$info = $this->findIsEmptyAssert();
		if (!$info->install) {
			return Hint::success("插件未安装！");
		}

		// 获取插件实例
		$pluginInfo = $pluginManager->plugin($info->name);

		if (!$this->request->isPost()) {
			// 获取插件配置
			$this->assign([
				'info' => $info,
				'config_tpl' => $pluginInfo->getConfigTemplate((array)$info->config),
			]);

			return $this->fetch();
		}

		$config = $this->request->param('config/a', []);
		foreach ($pluginInfo->getConfigTypeList() as $key => $type) {
			if (!isset($config[$key])) {
				continue;
			}

			if ('int' == $type) {
				$config[$key] = (int)$config[$key];
			} elseif ('float' == $type) {
				$config[$key] = (float)$config[$key];
			} elseif ('array' == $type) {
				$config[$key] = Arr::parse($config[$key]);
			}
		}

		$info->config = array_merge((array)$info->config, $config);

		$info->save();

		return Hint::success('配置已更新！');
	}

	/**
	 * 根据id获取数据，如果为空将中断执行
	 *
	 * @param int|null $id
	 * @return DatabasePlugin
	 */
	protected function findIsEmptyAssert($id = null)
	{
		if ($id) {
			return $this->repository()->detailById($id, [], ['fail' => true]);
		}

		if ($this->request->has('name')) {
			return $this->repository()->detail([
				'name' => $this->request->validString('name')
			], [], ['fail' => true]);
		}

		return $this->repository()->detailById($this->request->validId(), [], ['fail' => true]);
	}

	/**
	 * 重新刷新插件菜单
	 *
	 * @param \Xin\Contracts\Plugin\Factory $pluginManager
	 * @return \think\Response
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function refreshMenus(PluginManager $pluginManager)
	{
		$plugin = $this->request->param('plugin', '', 'trim');

		$menuGuards = array_keys($this->app->config->get('menu.menus'));
		foreach ($menuGuards as $guard) {
			Menu::menu($guard)->refresh($plugin);
		}

		DatabasePlugin::where([
			'install' => 1,
			'status' => 1,
		])->when($plugin, ['name' => $plugin])
			->select()
			->each(function (DatabasePlugin $info) use ($pluginManager) {
				if (!$pluginManager->has($info->name)) {
					return;
				}

				$pluginInfo = $pluginManager->plugin($info->name);

				// 配置菜单
				$this->setupMenus(true, $pluginInfo);
			});

		return Hint::success("已刷新！");
	}

	/**
	 * 创建插件资源目录软链接
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 */
	protected function createStaticSymlink(PluginInfoContract $pluginInfo)
	{
		$pluginName = $pluginInfo->getName();
		$pluginStaticPath = $pluginInfo->path('static');
		$linkPath = public_path('vendor') . $pluginName;

		// 检查原路径是否存在
		if (!is_dir($pluginStaticPath)) {
			return;
		}

		// 检查目标路径是否存在，如果存在则需要删除
		if (is_dir($linkPath)) {
			rmdir($linkPath);
		}

		// 创建软链接
		if (!@symlink($pluginStaticPath, $linkPath)) {
			throw new \LogicException('资源目录软链接创建失败，请检查 public 目录是否有可写权限！');
		}
	}

	/**
	 * 向页面赋值
	 *
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function assignEvents()
	{
		$events = DatabaseEvent::where('status', 1)->order('id desc')->select();
		$this->assign('events', $events);
	}

	/**
	 * 快速创建视图
	 *
	 * @return string
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function showCreateView()
	{
		$this->assignEvents();
		$this->assign('config_tpl', $this->buildConfigTplContent());

		return $this->showCreateView2();
	}

	/**
	 * 快速创建视图
	 *
	 * @param $model
	 * @return string
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function showUpdateView($model)
	{
		$this->assignEvents();

		return $this->showUpdateView2($model);
	}

	/**
	 * @inheritDoc
	 */
	protected function afterSetField($ids, $field, $value)
	{
		DatabaseEvent::refreshCache();
		DatabasePlugin::refreshPluginDisabledListCache();
	}

	/**
	 * 创建前数据处理
	 *
	 * @param mixed $model
	 * @param array $data
	 * @return array
	 */
	protected function beforeCreate($model, $data)
	{
		$data['config'] = new \stdClass();
		$data['events'] = isset($data['events']) ? $data['events'] : [];

		return $data;
	}

	/**
	 * 数据创建之后
	 *
	 * @param mixed $model
	 * @param array $data
	 */
	protected function afterCreate($model, $data)
	{
		$pluginName = $data['name'];

		/** @var PluginManager $pluginManager */
		$pluginManager = app(PluginManager::class);
		$pluginRootPath = $pluginManager->pluginPath($pluginName);

		// 插件已存在，则不在处理
		if (is_dir($pluginRootPath)) {
			Hint::outputSuccess("插件目录已存在，文件将不在生成！");
		}

		$createDirs = [$pluginRootPath];

		// 是否生成事件目录
		$eventDirs = [
			0 => 'weight',
			1 => 'listener',
		];
		$events = [];
		if (!empty($data['events'])) {
			$events = DatabaseEvent::where('name', 'in', $data['events'])->column('type', 'name');
			$eventTypes = array_unique(array_values($events));
			foreach ($eventDirs as $key => $dir) {
				if (in_array($key, $eventTypes)) {
					$createDirs[] = $pluginRootPath . $dir . DIRECTORY_SEPARATOR;
				}
			}
		}

		// 创建插件目录
		File::createDirOrFiles($createDirs);

		// 创建插件信息文件
		$manifestPath = $pluginRootPath . "manifest.php";
		file_put_contents($manifestPath, $this->buildManifestContent($data));

		$pluginPath = $pluginRootPath . "Plugin.php";
		file_put_contents($pluginPath, $this->buildPluginContent($data));

		// 创建配置文件
		if (isset($data['config_tpl']) && !empty($data['config_tpl'])) {
			$pluginConfigTplPath = $pluginRootPath . "config.php";
			file_put_contents($pluginConfigTplPath, $data['config_tpl']);
		}

		// 创建事件文件
		if (!empty($events)) {
			foreach ($events as $event => $type) {
				$subDir = $eventDirs[$type];
				$eventClass = Str::studly($event);
				$eventFilePath = $pluginRootPath . $subDir . DIRECTORY_SEPARATOR . $eventClass . ".php";
				file_put_contents($eventFilePath, $this->buildEventContent($type, $eventClass, $subDir, $pluginName));
			}
		}

		// 创建后台文件
		if (isset($data['has_admin']) && $data['has_admin']) {
			$adminRootPath = $pluginRootPath . 'admin' . DIRECTORY_SEPARATOR;
			File::createDirOrFiles([
				$adminRootPath . 'controller' . DIRECTORY_SEPARATOR,
				$adminRootPath . 'view' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR,
			]);
			file_put_contents(
				$adminRootPath . 'controller' . DIRECTORY_SEPARATOR . "IndexController.php",
				$this->buildAdminControllerContent($data)
			);
			file_put_contents(
				$adminRootPath . 'view' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . "index.html",
				$this->buildAdminViewContent($data)
			);
			file_put_contents(
				$adminRootPath . "menus.php",
				$this->buildAdminMenusConfigContent($data)
			);
		}
	}

	/**
	 * 生成插件描述文件内容
	 *
	 * @param array $data
	 * @return string
	 */
	protected function buildManifestContent($data)
	{
		$info = var_export([
			'name' => $data['name'],
			'title' => $data['title'],
			'description' => $data['description'],
			'author' => $data['author'],
			'version' => $data['version'],
			'events' => $data['events'],
		], true);

		return <<<EOT
<?php
return $info;
EOT;
	}

	/**
	 * 生成插件文件内容
	 *
	 * @param array $data
	 * @return string
	 */
	protected function buildPluginContent($data)
	{
		return $this->stub('plugin.stub', $data);
	}

	/**
	 * 生成事件文件内容
	 *
	 * @param string $eventClass
	 * @param string $subDir
	 * @param string $pluginName
	 * @return string
	 */
	protected function buildEventContent($type, $eventClass, $subDir, $pluginName)
	{
		$data = compact('type', 'eventClass', 'subDir', 'pluginName');

		if ($type == 0) {
			return $this->stub('weight.stub', $data);
		}

		return $this->stub('listener.stub', $data);
	}

	/**
	 * 生产配置文件
	 *
	 * @return string
	 */
	protected function buildConfigTplContent()
	{
		return $this->stub('config.stub', []);
	}

	/**
	 * 创建后台控制器
	 *
	 * @param array $data
	 * @return string
	 */
	protected function buildAdminControllerContent($data)
	{
		return $this->stub('admin_controller.stub', [
			'className' => $data['name']
		]);
	}

	/**
	 * 生产菜单配置文件
	 *
	 * @param array $data
	 * @return string
	 */
	protected function buildAdminMenusConfigContent($data)
	{
		return $this->stub('admin_menus.stub', $data);
	}

	/**
	 * 创建后台页面文件
	 *
	 * @param array $data
	 * @return string
	 */
	protected function buildAdminViewContent($data)
	{
		return $this->stub('admin_view.stub', $data);
	}

	/**
	 * @param string $tpl
	 * @param array $data
	 * @return array|string|string[]
	 */
	protected function stub($tpl, $data)
	{
		return Str::stub(file_get_contents($tpl), $data);
	}

	/**
	 * @inerhitDoc
	 */
	protected function repositoryTo()
	{
		return DatabasePlugin::class;
	}
}
