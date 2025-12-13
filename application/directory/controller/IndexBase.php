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
// | 前台会员基类
// +----------------------------------------------------------------------
namespace app\directory\controller;

use app\common\controller\Homebase;


class IndexBase extends HomeBase
{
    //会员模型相关配置
    protected $memberConfig = [];
    //会员组缓存
    protected $memberGroup = [];
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];
    /**
     * 权限Auth
     * @var Auth
     */
    protected $auth = null;

    //初始化
    protected function initialize()
    {

    }
}
