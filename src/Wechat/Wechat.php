<?php

namespace Xin\Wechat;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\ServiceContainer;
use Xin\Contracts\Wechat\Factory as WechatContract;
use Xin\Support\Arr;
use Xin\Wechat\EasyWechat\Work\ContactWayClient;

class Wechat implements WechatContract {

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
		$config = $this->getConfig("open_platforms.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'open_platforms.{$name}' not defined.");
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
		return $this->hasConfig('open_platforms' . ($name ?: $this->getDefault('open_platform')));
	}

	/**
	 * @inheritDoc
	 */
	public function official($name = null, array $options = []) {
		$config = $this->getConfig("officials.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'officials.{$name}' not defined.");
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
	public function hasOfficial($name = null) {
		return $this->hasConfig('officials' . ($name ?: $this->getDefault('official')));
	}

	/**
	 * @inheritDoc
	 */
	public function miniProgram($name = null, array $options = []) {
		$name = $name ?: $this->getDefault('miniprogram');

		$config = $this->getConfig("miniprograms.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'miniprograms.{$name}' not defined.");
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
		return $this->hasConfig('miniprograms' . ($name ?: $this->getDefault('miniprogram')));
	}

	/**
	 * @inheritDoc
	 */
	public function work($name = null, array $options = []) {
		$name = $name ?: $this->getDefault('work');

		$config = $this->getConfig("works.{$name}");
		if (empty($config)) {
			throw new WechatNotConfigureException("wechat config 'works.{$name}' not defined.");
		}

		$app = Factory::work($config);
		$app['contact_way'] = function ($app) {
			return new ContactWayClient($app);
		};

		return $app;
	}

	/**
	 * @inheritDoc
	 */
	public function hasWork($name = null) {
		return $this->hasConfig('works' . ($name ?: $this->getDefault('work')));
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

		return Factory::openWork($config);
	}

	/**
	 * @inheritDoc
	 */
	public function hasOpenWork($name = null) {
		return $this->hasConfig('openworks' . ($name ?: $this->getDefault('openwork')));
	}

	/**
	 * @param \EasyWeChat\Kernel\ServiceContainer $sc
	 * @param array                               $options
	 * @return mixed
	 */
	protected function initApplication(ServiceContainer $sc, array $options) {
		//// 监听微信响应数据
		//$sc->events->addListener(HttpResponseCreated::class, function(HttpResponseCreated $event){
		//	$response = $event->response;
		//
		//	if(false !== stripos($response->getHeaderLine('Content-disposition'), 'attachment')){
		//		$response = StreamResponse::buildFromPsrResponse($response);
		//	}else{
		//		$response = Response::buildFromPsrResponse($response);
		//	}
		//});

		return $sc;
	}

	/**
	 * @return string
	 */
	protected function getDefault($type) {
		return Arr::get($this->config, "defaults.{$type}", 'default');
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

		return $config;
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
	 * @param string $type
	 * @return bool
	 */
	public function hasConfig($type = null) {
		return !isset($this->config[$type]) || empty($this->config[$type]);
	}


}
