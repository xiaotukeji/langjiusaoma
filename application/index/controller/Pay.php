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
// | 会员支付前台
// +----------------------------------------------------------------------
namespace app\index\controller;

use addons\pay\model\MemberAmountLog;
use addons\pay\model\MemberPointLog;
use addons\pay\model\PayRechargeOrder;
use app\member\controller\MemberBase;
use app\member\model\Member as MemberModel;


use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;


use think\Db;
use think\Exception;

class Pay extends MemberBase
{


    protected $noNeedLogin = ['epay','ExecutePayment'];
    private $clientId = 'ATjkEfxfTDSe_kWRvirh24_pveBq6mbi50bFcmVLotteiVK4kg237dFolfQxdQ3wAvYsIuGkjRsgjPbq';
    private $clientSecret = 'EDPO00ssFi6x-NwHgPCSCO7hKLaJqxc6BpIl3GEwwQDutV_X22vdp7gZoXf2W8UBk8daEaa2RO_XJWDv';


    /*    private $clientId = 'AdaNca4SiKs8CVTc_7_yLro44smIqsyht06pIAqBusP8X3cpcNyEfhndFmaMJ8g1a2Iu1xV6GunYxvCj';
        private $clientSecret = 'EAoy_dSMBSsXbXKYrgGUSqH8Upl20hPQvMxkyTsivxOUEm2twpUmHVnjTbqdkrG_0Tv1oWUvSOVL8wCo';*/
    //初始化
    protected function initialize()
    {
        parent::initialize();
    }

    //充值
    public function index()
    {


        if ($this->request->isPost()) {
            $money    = $this->request->request('money/f');
            $pay_type = $this->request->request('paytype');
            $buy_years    = $this->request->request('buy_years/f',0);
            if (!$money || $money < 0) {
                $this->error("支付金额必须大于0");
            }
            if (!$pay_type || !in_array($pay_type, ['alipay', 'wechat','paypal'])) {
                $this->error("支付类型不能为空");
            }
            $config = get_addon_config('pay');
            if (isset($config['minmoney']) && $money < $config['minmoney']) {
                $this->error('充值金额不能低于' . $config['minmoney'] . '元');
            }
            //验证码
/*            if (!captcha_check($this->request->post('verify/s', ''))) {
                $this->error('验证码输入错误！');
                return false;
            }*/

            //获取兑换比例
            $cny_us_info = DB::name('config')->where('name','cny_us')->field('name,value')->find();
            $cny_us = $cny_us_info['value'];
            try {

                if ($pay_type == 'wechat' || $pay_type == 'alipay'){
                    $money = $money * $cny_us;
                }

                PayRechargeOrder::submitOrder($money, $pay_type ? $pay_type : 'wechat','','','','',$buy_years);
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }

        } else {

            $money    = $this->request->request('money/f',0);
            $buy_years    = $this->request->request('buy_years/f',0);

            $config    = get_addon_config('pay');
            $moneyList = [];
            if ($money > 0 && $buy_years >0){

                $moneyList[] = ['value' => $money*$buy_years, 'text' => $money*$buy_years, 'default' => true];
                $config['defaultmoney'] = $money*$buy_years;
            }else{
                foreach ($config['moneylist'] as $index => $item) {
                    $moneyList[] = ['value' => $item, 'text' => $index, 'default' => $item === $config['defaultmoney']];
                }

            }

            //获取兑换比例
            $cny_us_info = DB::name('config')->where('name','cny_us')->field('name,value')->find();
            $cny_us = $cny_us_info['value'];


            $paytypeList = [];
            foreach (explode(',', $config['paytypelist']) as $index => $item) {
                $paytypeList[] = ['value' => $item, 'image' => '/pay/images/' . $item . '-pay.png', 'default' => $item === $config['defaultpaytype']];
            }
            $this->assign('moneyList', $moneyList);
            $this->assign('paytypeList', $paytypeList);
            $this->assign('config', $config);
            $this->assign('buy_years', $buy_years);
            $this->assign('cny_us', $cny_us);
            $this->assign('money', $money);
            $cny = $money * $cny_us;
            $this->assign('cny', $cny);




            return $this->fetch('index');
        }
    }

    //余额日志
    public function amountlog()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page  = $this->request->param('page/d', 1);

            $list  = MemberAmountLog::where('user_id', $this->auth->id)->page($page, $limit)->order('id DESC')->select();
            $total = MemberAmountLog::where('user_id', $this->auth->id)->count();

            $result = ["code" => 0, "count" => $total, "data" => $list];
            return json($result);

        } else {
            return $this->fetch('amountlog');
        }
    }

    //积分日志
    public function pointlog()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page  = $this->request->param('page/d', 1);

            $list  = MemberPointLog::where('user_id', $this->auth->id)->page($page, $limit)->order('id DESC')->select();
            $total = MemberPointLog::where('user_id', $this->auth->id)->count();

            $result = ["code" => 0, "count" => $total, "data" => $list];
            return json($result);

        } else {
            return $this->fetch('pointlog');
        }
    }

    //积分兑换
    public function change_credit()
    {
        if ($this->request->isPost()) {
            $money = $this->request->param('money/d', 0);
            if (!$money || $money < 0) {
                $this->error("兑换金额必须大于0");
            }
            $point = $money * $this->memberConfig['rmb_point_rate'];

            try {
                if ($this->auth->amount < $money) {
                    throw new Exception('余额不足，无法进行兑换');
                }
                //扣除金钱
                MemberModel::amount(-$money, $this->auth->id, '积分兑换');
                //增加积分
                MemberModel::point($point, $this->auth->id, '积分兑换');
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
            }
            $this->success("兑换成功！");
        } else {
            return $this->fetch('change_credit');
        }
    }

    //企业支付通知和回调
    public function epay()
    {
        $type     = $this->request->param('type');
        $pay_type = $this->request->param('pay_type');
        if ($type == 'notify') {
            $pay = \addons\pay\library\Service::checkNotify($pay_type);
            if (!$pay) {
                echo '签名错误';
                return;
            }
            try {
                $data      = $pay->verify();
                $payamount = $pay_type == 'alipay' ? $data['total_amount'] : $data['total_fee'] / 100;
                PayRechargeOrder::settle($data['out_trade_no'], $payamount);
            } catch (Exception $e) {
                //写入日志
                // $e->getMessage();
            }
            return $pay->success()->send();
        } else {
            $pay = \addons\pay\library\Service::checkReturn($pay_type);
            if (!$pay) {
                $this->error('签名错误');
            }




            //你可以在这里定义你的提示信息,但切记不可在此编写逻辑
            $this->success("恭喜你！充值成功!", url("member/index/index"));
        }
        return;
    }


    public function ExecutePayment(){

        if (isset($_GET['success']) && $_GET['success'] == 'true') {

            $paypal_id = $_GET['paymentId'];
            $order = DB::name('pay_recharge_order')->where('paypal_id',$paypal_id)->find();
            $order_id = $order['orderid'];
            $money = $order['amount'];
            $buy_years = $order['buy_years'];
            $uid = $order['user_id'];



            $apiContext = $this->getApiContext($this->clientId, $this->clientSecret);

            $paymentId = $_GET['paymentId'];
            $payment = Payment::get($paymentId, $apiContext);


            $execution = new PaymentExecution();
            $execution->setPayerId($_GET['PayerID']);


            $transaction = new Transaction();
            $amount = new Amount();
            $details = new Details();

            $details->setShipping(0)
                ->setTax(0)
                ->setSubtotal($money);

            $amount->setCurrency('USD');
            $amount->setTotal($money);
            $amount->setDetails($details);
            $transaction->setAmount($amount);

            // Add the above transaction object inside our Execution object.
            $execution->addTransaction($transaction);

            try {

                $result = $payment->execute($execution, $apiContext);



                try {
                    $payment = Payment::get($paymentId, $apiContext);

                    $temp = json_decode($payment,true);
                    $payer = $temp['payer'];
                    $transactions = $temp['transactions'];
                    /*$payer = json_decode($payer,true);*/

                    if ($payer['status'] == 'VERIFIED'){ //处理各种记录

                        $amount = $transactions[0]['amount']['total'];

                        PayRechargeOrder::settle($order_id, $amount);

                        //升级IVP
/*                        $member_info = DB::name("member")->where('id',$uid)->find();
                        if ($member_info){

                            $member_overduedate =  $member_info['overduedate'];
                            $buydate = 365*$buy_years*86400;
                            $overduedate = $member_overduedate > time() ? ($member_overduedate + $buydate) : (time() + $buydate);
                            MemberModel::where('id', $uid)->update(['groupid' => 4, 'overduedate' => $overduedate, 'vip' => 1]);

                            MemberModel::amount(-$money, $uid, 'VIP-Use');
                        }*/


                        $this->success("Congratulations！Vip Success!", url("member/index/index"));
                        /*var_dump($order);*/
                    }




                } catch (Exception $e) {
                    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                    $this->error($e->getMessage());
                    exit(1);
                }
            } catch (Exception $e) {
                // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                $this->error($e->getMessage());
                exit(1);
            }


            return $payment;
        } else {

            exit;
        }

    }

    function getApiContext($clientId, $clientSecret)
    {



        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration

        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                /* 'mode' => 'live',*/
                'log.LogEnabled' => true,
                'log.FileName' => '../runtime/log/PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                //'cache.FileName' => '/PaypalCache' // for determining paypal cache directory
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

        return $apiContext;
    }


}
