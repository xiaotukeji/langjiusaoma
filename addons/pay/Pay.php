<?php
// +----------------------------------------------------------------------
// | Yzncms [ 御宅男工作室 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://yzncms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 御宅男 <530765310@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 支付插件
// +----------------------------------------------------------------------
namespace addons\pay;

use app\common\library\Menu;
use think\Addons;

class Pay extends Addons
{
    //后台菜单
    protected $menu = [
        [
            'name'    => 'member.amountlog',
            'title'   => '会员余额日志',
            'sublist' => [
                ['name' => 'member.amountlog/index', 'title' => '查看'],
                ['name' => 'member.amountlog/add', 'title' => '添加'],
                ['name' => 'member.amountlog/edit', 'title' => '修改'],
                ['name' => 'member.amountlog/del', 'title' => '删除'],
                ['name' => 'member.amountlog/multi', 'title' => '批量更新'],
            ],
        ],
        [
            'name'    => 'member.pointlog',
            'title'   => '会员积分日志',
            'sublist' => [
                ['name' => 'member.pointlog/index', 'title' => '查看'],
                ['name' => 'member.pointlog/add', 'title' => '添加'],
                ['name' => 'member.pointlog/edit', 'title' => '修改'],
                ['name' => 'member.pointlog/del', 'title' => '删除'],
                ['name' => 'member.pointlog/multi', 'title' => '批量更新'],
            ],
        ],
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $info = get_addon_info('member');
        if (!$info || $info['status'] != 1) {
            throw new \think\Exception("请在后台插件管理中安装《会员插件》并启用后再尝试");
        }
        Menu::create($this->menu, 'member');
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('member.amountlog');
        Menu::delete('member.pointlog');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('member.amountlog');
        Menu::enable('member.pointlog');
        return true;
    }

    /**
     * 插件更新方法
     * @return bool
     */
    public function upgrade()
    {
        if (!\think\Db::name("auth_rule")->where('name', 'member.amountlog')->find()) {
            Menu::create($this->menu, 'member');
        }
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('member.amountlog');
        Menu::disable('member.pointlog');
        return true;
    }

    //或者run方法
    public function userSidenavAfter($content)
    {
        return $this->fetch('userSidenavAfter');
    }

}
