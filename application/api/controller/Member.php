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
// | 前台会员api管理
// +----------------------------------------------------------------------
namespace app\api\controller;

use app\common\library\Ems;
use app\common\library\Sms;
use app\member\controller\MemberApi;

class Member extends MemberApi
{
    protected $noNeedLogin = ['login', 'register'];
    protected $noNeedRight = [];

    //初始化
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     *
     * @ApiMethod (POST)
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $account  = $this->request->post('account');
        $password = $this->request->post('password');
        if (!$account || !$password) {
            $this->error('未知参数');
        }
        $ret = $this->auth->loginLocal($account, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success('登录成功', $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     */
    public function register()
    {
        if (empty($this->memberConfig['allowregister'])) {
            $this->error("系统不允许新会员注册！");
        }
        $extend = [];
        $data   = $this->request->post();
        $rule   = [
            'username|用户名' => 'unique:member|require|alphaDash|length:3,20',
            'nickname|昵称'  => 'unique:member|chsDash|length:3,20',
            'mobile|手机'    => 'require|unique:member|mobile',
            'password|密码'  => 'require|length:3,20',
            'email|邮箱'     => 'unique:member|require|email',
        ];
        if ($this->memberConfig['password_confirm']) {
            $rule['password|密码'] = "require|length:3,20|confirm";
        }
        if ($this->memberConfig['register_mobile_verify']) {
            $rule['captcha_mobile|手机验证码'] = "require";
        }
        if ($this->memberConfig['register_email_verify']) {
            $rule['captcha_email|邮箱验证码'] = "require";
        }
        $result = $this->validate($data, $rule);
        if (true !== $result) {
            $this->error($result);
        }
        if ($this->memberConfig['register_mobile_verify']) {
            $result = Sms::check($data['mobile'], $data['captcha_mobile'], 'register');
            if (!$result) {
                $this->error('手机验证码错误！');
            }
            $extend['ischeck_mobile'] = 1;
        }
        if ($this->memberConfig['register_email_verify']) {
            $result = Ems::check($data['email'], $data['captcha_email'], 'register');
            if (!$result) {
                $this->error('邮箱验证码错误！');
            }
            $extend['ischeck_email'] = 1;
        }
        $ret = $this->auth->userRegister($data['username'], $data['password'], $data['email'], $data['mobile'], $extend);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success('注册成功', $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error('未知参数');
        }
        $this->auth->logout();
        $this->success('注销成功');
    }
}
