<?php

namespace Xin\Wechat;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\ServiceContainer;
use Xin\Contracts\Wechat\Factory as WechatFactory;
use Xin\Support\Arr;
use Xin\Wechat\EasyWechat\Work\ExternalContact\ContactWayClient;
use Xin\Wechat\Exceptions\WechatInvalidConfigException;
use Xin\Wechat\Exceptions\WechatNotConfigureException;

class WechatManager implements WechatFactory {

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * Wechat constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function openPlatform($name = null, array $options = []) {
		$config = $this->getConfig("open_platform.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'open_platform.{$name}' not defined.");
		}

		$config = $this->checkApplicationConfig($config);

		return $this->initApplication(
			Factory::openPlatform($config),
			$options
		);
	}

	/**
	 * @inheritDoc
	 */
	public function hasOpenPlatform($name = null) {
		return $this->hasConfig('open_platform' . ($name ?: $this->getDefault('open_platform')));
	}

	/**
	 * @inheritDoc
	 */
	public function officialAccount($name = null, array $options = []) {
		$config = $this->getConfig("official_account.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'official_account.{$name}' not defined.");
		}

		$config = $this->checkApplicationConfig($config);

		return $this->initApplication(
			Factory::officialAccount($config),
			$options
		);
	}

	/**
	 * @inheritDoc
	 */
	public function hasOfficialAccount($name = null) {
		return $this->hasConfig('official_account' . ($name ?: $this->getDefault('official')));
	}

	/**
	 * @inheritDoc
	 */
	public function miniProgram($name = null, array $options = []) {
		$name = $name ?: $this->getDefault('mini_program');

		$config = $this->getConfig("mini_program.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'mini_program.{$name}' not defined.");
		}

		$config = $this->checkApplicationConfig($config);

		return $this->initApplication(
			Factory::miniProgram($config),
			$options
		);
	}

	/**
	 * @inheritDoc
	 */
	public function hasMiniProgram($name = null) {
		return $this->hasConfig('mini_program' . ($name ?: $this->getDefault('mini_program')));
	}

	/**
	 * 检查配置是否正确
	 *
	 * @param array $config
	 * @return array
	 */
	protected function checkApplicationConfig(array $config) {
		if (!isset($config['app_id']) || empty($config['app_id'])) {
			throw new WechatInvalidConfigException("wechat config 'app_id' not defined.");
		}

		if (!isset($config['mode']) || $config['mode'] == 0) {
			if (!isset($config['secret']) || empty($config['secret'])) {
				throw new WechatInvalidConfigException("wechat config 'secret' not defined.");
			}
		} else {
			if (!isset($config['authorizer_refresh_token']) || empty($config['authorizer_refresh_token'])) {
				throw new WechatInvalidConfigException("wechat config 'authorizer_refresh_token' not defined.");
			}
		}

		return array_merge($this->getConfig('defaults', []), $config);
	}

	/**
	 * @inheritDoc
	 */
	public function work($name = null, array $options = []) {
		$name = $name ?: $this->getDefault('work');

		$config = $this->getConfig("work.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'work.{$name}' not defined.");
		}

		$config = array_merge($this->getConfig('defaults', []), $config);

		$app = Factory::work($config);
		$app['contact_way'] = function ($app) {
			return new ContactWayClient($app);
		};

		return $this->initApplication($app, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function hasWork($name = null) {
		return $this->hasConfig('work' . ($name ?: $this->getDefault('work')));
	}

	/**
	 * @inheritDoc
	 */
	public function openWork($name = null, array $options = []) {
		$name = $name ?: $this->getDefault('openwork');

		$config = $this->getConfig("openworks.{$name}");
		if (empty($config)) {
			throw new WechatInvalidConfigException("wechat config is invalid.");
		}

		$config = array_merge($this->getConfig('defaults', []), $config);
		$app = Factory::openWork($config);

		return $this->initApplication($app, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function hasOpenWork($name = null) {
		return $this->hasConfig('openworks' . ($name ?: $this->getDefault('openwork')));
	}

	/**
	 * @param ServiceContainer $app
	 * @param array            $options
	 * @return mixed
	 */
	protected function initApplication(ServiceContainer $app, array $options) {
		//// 监听微信响应数据
		//$app->events->addListener(HttpResponseCreated::class, function(HttpResponseCreated $event){
		//	$response = $event->response;
		//
		//	if(false !== stripos($response->getHeaderLine('Content-disposition'), 'attachment')){
		//		$response = StreamResponse::buildFromPsrResponse($response);
		//	}else{
		//		$response = Response::buildFromPsrResponse($response);
		//	}
		//});

		// $app->logger->extend('api_log', function ($app, $config) {
		// 	return new Monolog('WechatApiLog', [
		// 		new ApiLogHandler($app, $config),
		// 	]);
		// });

		return $app;
	}

	/**
	 * @return string
	 */
	protected function getDefault($type) {
		return Arr::get($this->config, "defaults.{$type}", 'default');
	}

	/**
	 * 获取配置
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function getConfig($key = null, $default = null) {
		if (null === $key) {
			return $this->config;
		}

		return Arr::get($this->config, $key, $default);
	}

	/**
	 * 指定类型的配置是否存在
	 *
	 * @param string $key
	 * @return bool
	 */
	public function hasConfig($key = null) {
		if ($key == null) {
			return !empty($this->config);
		}

		return Arr::has($this->config, $key);
	}

}
