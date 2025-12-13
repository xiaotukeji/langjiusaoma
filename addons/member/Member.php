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
// | 会员插件
// +----------------------------------------------------------------------
namespace addons\member;

use app\common\library\Menu;
use think\Addons;

class Member extends Addons
{
    public $cache_list = [
        'Member_Group' => [
            'name'   => '会员组',
            'model'  => 'MemberGroup',
            'action' => 'membergroup_cache',
        ],
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                "name"    => "member",
                "title"   => "会员管理",
                "icon"    => "iconfont icon-user-line",
                "sublist" => [
                    [
                        "name"      => "member.member",
                        "title"     => "会员管理",
                        "icon"      => "iconfont icon-user-line",
                        "listorder" => 99,
                        "sublist"   => [
                            ["name" => "member.member/index", "title" => "查看"],
                            ["name" => "member.member/add", "title" => "新增"],
                            ["name" => "member.member/edit", "title" => "编辑"],
                            ["name" => "member.member/del", "title" => "删除"],
                            ["name" => "member.member/pass", "title" => "审核"],
                        ],
                    ],
                    [
                        "name"      => "member.member/userverify",
                        "title"     => "审核会员",
                        "icon"      => "iconfont icon-user-star-line",
                        "listorder" => 98,
                        "ismenu"    => 1,
                    ],
                    [
                        "name"    => "member.group",
                        "title"   => "会员组管理",
                        "icon"    => "iconfont icon-user-settings-line",
                        "sublist" => [
                            ["name" => "member.group/index", "title" => "查看"],
                            ["name" => "member.group/add", "title" => "新增"],
                            ["name" => "member.group/edit", "title" => "编辑"],
                            ["name" => "member.group/del", "title" => "删除"],
                        ],
                    ],
                ],
            ],
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("member");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("member");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("member");
        return true;
    }
}
