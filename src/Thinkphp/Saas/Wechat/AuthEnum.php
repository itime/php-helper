<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Wechat;

class AuthEnum{

	// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// + 公众号权限集														+
	// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// 消息管理权限
	const OFFICIAL_MESSAGE = 1;

	// 用户管理权限
	const OFFICIAL_USER = 2;

	// 帐号服务权限
	const OFFICIAL_ACCOUNT = 3;

	// 网页服务权限
	const OFFICIAL_WEB = 4;

	// 微信小店权限
	const OFFICIAL_MICRO_SHOP = 5;

	// 微信多客服权限
	const OFFICIAL_CUSTOMER = 6;

	// 群发与通知权限
	const OFFICIAL_TEMPLATE_MESSAGE = 7;

	// 微信卡券权限
	const OFFICIAL_CARD = 8;

	// 微信扫一扫权限
	const OFFICIAL_SCAN = 9;

	// 微信连WIFI权限
	const OFFICIAL_WIFI = 10;

	// 素材管理权限
	const OFFICIAL_MEDIA = 11;

	// 微信摇周边权限
	const OFFICIAL_NEARBY = 12;

	// 微信门店权限
	const OFFICIAL_STORE = 13;

	// 自定义菜单权限
	const OFFICIAL_MENU = 15;

	// 城市服务接口权限
	const OFFICIAL_CITY = 22;

	// 广告管理权限
	const OFFICIAL_AD = 23;

	// 开放平台帐号管理权限
	const OFFICIAL_OPEN_PLATFORM = 24;

	// 微信电子发票权限
	const OFFICIAL_INVOICE = 26;

	// 快速注册小程序权限
	const OFFICIAL_FAST_REGISTER_MINIPROGRAM = 27;

	// 小程序管理权限
	const OFFICIAL_MINIPROGRAM = 33;

	// 微信商品库权限
	const OFFICIAL_GOODS = 34;

	// 微信卡路里权限
	const OFFICIAL_MOTION = 35;

	// 微信好物圈
	const OFFICIAL_GOOD_GOODS = 44;

	// 微信一物一码权限
	const OFFICIAL_UNIQUE_CODE = 46;

	// 微信财政电子票据权限
	const OFFICIAL_FINANCE_INVOICE = 47;

	// 服务号对话权限
	const OFFICIAL_GUIDE = 54;

	// 服务平台管理权限
	const OFFICIAL_SERVICE_PLATFORM = 66;

	// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// + 小程序权限集														+
	// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// 用户管理权限
	const MINIPROGRAM_USER = 17;

	// 开发管理与数据分析权限
	const MINIPROGRAM_DEVELOP = 18;

	// 客服消息管理权限
	const MINIPROGRAM_CUSTOMER = 19;

	// 开放平台帐号管理权限
	const MINIPROGRAM_OPEN_PLATFORM = 25;

	// 开放平台帐号管理权限
	const MINIPROGRAM_BASE_INFO = 30;

	// 小程序认证权限
	const MINIPROGRAM_VERIFY = 31;

	// 微信卡路里权限
	const MINIPROGRAM_MOTION = 36;

	// 附近地点权限
	const MINIPROGRAM_NEARBY = 37;

	// 插件管理权限
	const MINIPROGRAM_PLUGIN = 40;

	// 微信好物圈
	const MINIPROGRAM_GOOD_GOODS = 41;

	// 快递配送权限
	const MINIPROGRAM_EXPRESS = 45;

	// 微信财政电子票据权限
	const MINIPROGRAM_FINANCE_INVOICE = 48;

	// 云开发管理权限
	const MINIPROGRAM_CLOUD = 49;

	// 即时配送权限
	const MINIPROGRAM_DELIVERY = 51;

	// 小程序直播权限
	const MINIPROGRAM_LIVE_PLAY = 52;

	// 页面推送权限
	const MINIPROGRAM_PAGE_PUSH = 57;

	// 广告管理权限
	const MINIPROGRAM_AD = 65;

	// 服务平台管理权限
	const MINIPROGRAM_SERVICE_PLATFORM = 67;

	// 商品管理权限
	const MINIPROGRAM_MICRO_GOODS = 70;

	// 订单与物流管理权限
	const MINIPROGRAM_MICRO_ORDER = 71;

	/**
	 * @var \string[][]
	 */
	protected static $DESC_LIST = [
		// 公众号权限集
		1  => [
			'title' => '消息管理权限',
			'desc'  => '帮助公众号接收用户消息，进行人工客服回复或自动回复',
		],
		2  => [
			'title' => '用户管理权限',
			'desc'  => '帮助公众号获取用户信息，进行用户管理',
		],
		3  => [
			'title' => '帐号服务权限',
			'desc'  => '帮助认证、设置公众号，进行帐号管理',
		],
		4  => [
			'title' => '网页服务权限',
			'desc'  => '帮助公众号实现第三方网页服务和活动',
		],
		5  => [
			'title' => '微信小店权限',
			'desc'  => '帮助公众号使用微信小店',
		],
		6  => [
			'title' => '微信多客服权限',
			'desc'  => '帮助公众号使用微信多客服',
		],
		7  => [
			'title' => '群发与通知权限',
			'desc'  => '帮助公众号进行群发和模板消息业务通知',
		],
		8  => [
			'title' => '微信卡券权限',
			'desc'  => '帮助公众号使用微信卡券',
		],
		9  => [
			'title' => '微信扫一扫权限',
			'desc'  => '帮助公众号使用微信扫一扫',
		],
		10 => [
			'title' => '微信连WIFI权限',
			'desc'  => '帮助公众号使用微信连WIFI',
		],
		11 => [
			'title' => '素材管理权限',
			'desc'  => '帮助公众号管理多媒体素材，用于客服等业务',
		],
		12 => [
			'title' => '微信摇周边权限',
			'desc'  => '帮助公众号使用微信摇周边',
		],
		13 => [
			'title' => '微信门店权限',
			'desc'  => '帮助公众号使用微信门店',
		],
		15 => [
			'title' => '自定义菜单权限',
			'desc'  => '帮助公众号使用自定义菜单',
		],
		22 => [
			'title' => '城市服务接口权限',
			'desc'  => '帮助城市服务内的服务向用户发送消息，沉淀办事记录，展示页卡及办事结果页',
		],
		23 => [
			'title' => '广告管理权限',
			'desc'  => '帮助广告主进行微信广告的投放和管理',
		],
		24 => [
			'title' => '开放平台帐号管理权限',
			'desc'  => '帮助公众号绑定开放平台帐号，实现用户身份打通',
		],
		26 => [
			'title' => '微信电子发票权限',
			'desc'  => '帮助公众号使用微信电子发票',
		],
		27 => [
			'title' => '快速注册小程序权限',
			'desc'  => '帮助公众号快速注册小程序',
		],
		33 => [
			'title' => '小程序管理权限',
			'desc'  => '可新增关联小程序，并对公众号已关联的小程序进行管理',
		],
		34 => [
			'title' => '微信商品库权限',
			'desc'  => '帮助公众号商家导入、更新、查询商品信息，从而在返佣商品推广等场景使用',
		],
		35 => [
			'title' => '微信卡路里权限',
			'desc'  => '为公众号提供用户卡路里同步、授权查询、兑换功能',
		],
		44 => [
			'title' => '好物圈权限',
			'desc'  => '帮助公众号将物品、订单、收藏等信息同步至好物圈，方便用户进行推荐',
		],
		46 => [
			'title' => '微信一物一码权限',
			'desc'  => '帮助公众号使用微信一物一码功能',
		],
		47 => [
			'title' => '微信财政电子票据权限',
			'desc'  => '帮助公众号完成授权、插卡及报销',
		],
		54 => [
			'title' => '服务号对话权限',
			'desc'  => '帮助公众号配置对话能力，管理顾问、客户、标签和素材等',
		],
		66 => [
			'title' => '服务平台管理权限',
			'desc'  => '帮助公众号管理服务平台上购买的资源',
		],

		// 小程序权限集
		17 => [
			'title' => '帐号管理权限',
			'desc'  => '帮助小程序获取二维码，进行帐号管理',
		],
		18 => [
			'title' => '开发管理与数据分析权限',
			'desc'  => '帮助小程序进行功能开发与数据分析',
		],
		19 => [
			'title' => '客服消息管理权限',
			'desc'  => '帮助小程序接收和发送客服消息',
		],
		25 => [
			'title' => '开放平台帐号管理权限',
			'desc'  => '帮助小程序绑定开放平台帐号，实现用户身份打通',
		],
		30 => [
			'title' => '小程序基本信息设置权限',
			'desc'  => '帮助小程序设置名称、头像、简介、类目等基本信息',
		],
		31 => [
			'title' => '小程序认证权限',
			'desc'  => '帮助小程序申请认证',
		],
		36 => [
			'title' => '微信卡路里权限',
			'desc'  => '为小程序提供用户卡路里同步、授权查询、兑换功能',
		],
		37 => [
			'title' => '附近地点权限',
			'desc'  => '帮助小程序创建附近地点，可设置小程序展示在“附近的小程序”入口中',
		],
		40 => [
			'title' => '插件管理权限',
			'desc'  => '用于代小程序管理插件的添加和使用',
		],
		41 => [
			'title' => '好物圈权限',
			'desc'  => '帮助小程序将物品、订单、收藏等信息同步至好物圈，方便用户进行推荐',
		],
		45 => [
			'title' => '快递配送权限',
			'desc'  => '帮助有快递配送需求的开发者，快速高效对接多家快递公司。对接后用户可通过微信服务通知接收实时快递配送状态，提升用户体验',
		],
		48 => [
			'title' => '微信财政电子票据权限',
			'desc'  => '帮助小程序完成授权、插卡及报销',
		],
		49 => [
			'title' => '云开发管理权限',
			'desc'  => '帮助小程序管理小程序·云开发资源',
		],
		51 => [
			'title' => '即时配送权限',
			'desc'  => '旨在解决餐饮、生鲜、超市等小程序的外卖配送需求，接入后小程序商家可通过统一的接口获得多家配送公司的配送服务，提高经营效率',
		],
		52 => [
			'title' => '小程序直播权限',
			'desc'  => '帮助有直播需求的小程序实现在小程序上直播边看边买的能力',
		],
		57 => [
			'title' => '页面推送权限',
			'desc'  => '帮助小程序推送小程序页面给搜索引擎，增加小程序页面在搜索的收录与曝光机会',
		],
		65 => [
			'title' => '广告管理权限',
			'desc'  => '帮助广告主进行微信广告的投放和管理',
		],
		67 => [
			'title' => '服务平台管理权限',
			'desc'  => '帮助小程序管理服务平台上购买的资源',
		],
		70 => [
			'title' => '商品管理权限',
			'desc'  => '支持对小商店商品及库存信息进行管理',
		],
		71 => [
			'title' => '订单与物流管理权限',
			'desc'  => '支持对小商店订单及物流信息进行管理',
		],
	];

	/**
	 * 获取权限描述
	 *
	 * @param int $type
	 * @return array|string[]
	 */
	public static function getDesc($type){
		return isset(static::$DESC_LIST[$type]) ? static::$DESC_LIST[$type] : [];
	}

	/**
	 * 获取权限描述列表
	 *
	 * @return \string[][]
	 */
	public static function getDescList(){
		return static::$DESC_LIST;
	}
}
