<?php
// +----------------------------------------------------------------------
// | 菜单设置
// +----------------------------------------------------------------------

return [
	'defaults' => [
		'menu' => 'admin',
	],

	'menus' => [
		'admin' => [
			'type' => 'phpfile',
			//			'type'        => 'model',
			'model' => \app\admin\model\AdminMenu::class,
			'base_path' => base_path('admin') . 'menus.php',
			'target_path' => runtime_path('admin') . 'menus.php',
		],
		'store' => [
			'type' => 'phpfile',
			'base_path' => base_path('store') . 'menus.php',
			'target_path' => runtime_path('store') . 'menus.php',
		],
	],
];
