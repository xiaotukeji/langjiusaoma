<?php
// +----------------------------------------------------------------------
// | Yzncms [ 御宅男工作室 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://yzncms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | fastadmin: https://www.fastadmin.net/
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 邮箱验证码接口
// +----------------------------------------------------------------------
namespace app\api\controller;


use addons\pay\model\PayRechargeOrder;
use think\Db;
use app\common\controller\Api;
use app\common\library\Ems as Emslib;
use app\member\model\Member;
use think\facade\Hook;
use think\facade\Validate;

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


/**
 * @title 邮箱验证码接口
 * @controller api\controller\Ems
 * @group base
 */



class Test extends Api
{


    public function get_address() {
        $latitude=request()->get('latitude','30.272550997945984');//经度
        $longitude=request()->get('longitude','120.1386774388');
        $key='B3RBZ-BVCC5-ENRIJ-Q4PUM-DMWYO-SRFV6';//腾讯key值
        $url = 'https://apis.map.qq.com/ws/geocoder/v1?key='.$key.'&location='.$latitude.','.$longitude;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');
        $content = curl_exec($ch);
        curl_close($ch);
        $result = [];
        if($content) {
            $result = json_decode($content, true);
            var_dump($result['result']['address_component']);
        }
    }





    private $clientId = 'ATjkEfxfTDSe_kWRvirh24_pveBq6mbi50bFcmVLotteiVK4kg237dFolfQxdQ3wAvYsIuGkjRsgjPbq';
    private $clientSecret = 'EDPO00ssFi6x-NwHgPCSCO7hKLaJqxc6BpIl3GEwwQDutV_X22vdp7gZoXf2W8UBk8daEaa2RO_XJWDv';


/*    private $clientId = 'AdaNca4SiKs8CVTc_7_yLro44smIqsyht06pIAqBusP8X3cpcNyEfhndFmaMJ8g1a2Iu1xV6GunYxvCj';
    private $clientSecret = 'EAoy_dSMBSsXbXKYrgGUSqH8Upl20hPQvMxkyTsivxOUEm2twpUmHVnjTbqdkrG_0Tv1oWUvSOVL8wCo';*/


    public function test_email(){

        $email = "example@example.com"; // 你要检查的电子邮件地址


        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "是有效的电子邮件地址";
        } else {
            echo "不是有效的电子邮件地址";
        }


    }



    public function test01(){



        define('SITE_URL', 'http://localhost/PayPal'); //网站url自行定义
        //创建支付对象实例
        $apiContext = $this->getApiContext($this->clientId, $this->clientSecret);
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
        $item1->setName('Ground Coffee 40 oz')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice(7.5);
        $item2 = new Item();
        $item2->setName('Granola bars')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setSku("321321") // Similar to `item_number` in Classic API
            ->setPrice(2);
        $itemList = new ItemList();
        $itemList->setItems(array($item1, $item2));
        $details = new Details();
        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal(20);

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal(20)
            ->setDetails($details);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            /*->setItemList($itemList)*/
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        // ### Redirect urls
        // Set the urls that the buyer must be redirected to after
        // payment approval/ cancellation.
        //$baseUrl = getBaseUrl();
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://jiankong.vy33.com/api/test/ExecutePayment?success=true")
            ->setCancelUrl("http://jiankong.vy33.com/api/test/ExecutePayment?success=false");

        // ### Payment
// A Payment Resource; create one using
// the above types and intent set to 'sale'
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));


        // For Sample Purposes Only.
        $request = clone $payment;


        // ### Create Payment
        // Create a payment by calling the 'create' method
        // passing it a valid apiContext.
        // (See bootstrap.php for more on `ApiContext`)
        // The return object contains the state and the
        // url to which the buyer must be redirected to
        // for payment approval
        try {
            $payment->create($apiContext);
        } catch (Exception $e) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            $this->error($e->getMessage());
            exit(1);
        }

        $approvalUrl = $payment->getApprovalLink();

        $temp = json_decode($payment,true);

        var_dump($temp['id']);
        var_dump('--<br>');
        foreach($temp as $k => $v){

            var_dump($k.'--'.$v);
            var_dump('--<br>');

        }


/*        var_dump($payment);*/
        var_dump($approvalUrl);


    }

    public function ExecutePayment(){

        if (isset($_GET['success']) && $_GET['success'] == 'true') {

            // Get the payment Object by passing paymentId
            // payment id was previously stored in session in
            // CreatePaymentUsingPayPal.php

            $apiContext = $this->getApiContext($this->clientId, $this->clientSecret);

            $paymentId = $_GET['paymentId'];
            $payment = Payment::get($paymentId, $apiContext);

            // ### Payment Execute
            // PaymentExecution object includes information necessary
            // to execute a PayPal account payment.
            // The payer_id is added to the request query parameters
            // when the user is redirected from paypal back to your site
            $execution = new PaymentExecution();
            $execution->setPayerId($_GET['PayerID']);

            // ### Optional Changes to Amount
            // If you wish to update the amount that you wish to charge the customer,
            // based on the shipping address or any other reason, you could
            // do that by passing the transaction object with just `amount` field in it.
            // Here is the example on how we changed the shipping to $1 more than before.
            $transaction = new Transaction();
            $amount = new Amount();
            $details = new Details();

            $details->setShipping(0)
                ->setTax(0)
                ->setSubtotal(20);

            $amount->setCurrency('USD');
            $amount->setTotal(20);
            $amount->setDetails($details);
            $transaction->setAmount($amount);

            // Add the above transaction object inside our Execution object.
            $execution->addTransaction($transaction);

            try {
                // Execute the payment
                // (See bootstrap.php for more on `ApiContext`)
                $result = $payment->execute($execution, $apiContext);

                // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                /*var_dump($result);*/

                try {
                    $payment = Payment::get($paymentId, $apiContext);

                    $temp = json_decode($payment,true);
                    $payer = $temp['payer'];
                    $transactions = $temp['transactions'];
                    /*$payer = json_decode($payer,true);*/

                    if ($payer['status'] == 'VERIFIED'){ //处理各种记录
                        $paypal_id = $_GET['paymentId'];
                        $order = DB::name('pay_recharge_order')->where('paypal_id',$paypal_id)->find();
                        $order_id = $order['orderid'];
                        $amount = $transactions[0]['amount']['total'];

                        PayRechargeOrder::settle($order_id, $amount);
                        $this->success("Congratulations！Recharge Success!", url("member/index/index"));
                        /*var_dump($order);*/
                    }
/*                    var_dump($transactions[0]['amount']['total']);
                    var_dump('---<br>');
                    foreach ($transactions[0] as $k =>$v){

                        var_dump($k.'--'.$v.'--<br>');
                    }

                    var_dump('-----');*/
                   /* var_dump($payer);*/
/*                    foreach($temp as $k => $v){

                        $payer =


                        var_dump($v);
                        var_dump('---');

                    }*/



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

            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            /*ResultPrinter::printResult("Get Payment", "Payment", $payment->getId(), null, $payment);*/

            return $payment;
        } else {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            /*ResultPrinter::printResult("User Cancelled the Approval", null);*/
            exit;
        }

    }


    function getApiContext($clientId, $clientSecret)
    {

        // #### SDK configuration
        // Register the sdk_config.ini file in current directory
        // as the configuration source.
        /*
        if(!defined("PP_CONFIG_PATH")) {
            define("PP_CONFIG_PATH", __DIR__);
        }
        */


        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com

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

    /**
     * @title 发送验证码
     * @desc 最基础的接口注释写法
     * @author 御宅男
     * @url /api/Ems/send
     * @method GET
     * @tag 邮箱 验证码
     * @param name:email type:string require:1 desc:邮箱
     * @param name:event type:string require:1 desc:事件名称
     * @return name:data type:array ref:definitions\dictionary
     */
    public function send()
    {

        $email = input('email','martin@airsea-scm.com');
        //$email = "martinxia518@gmail.com";//$this->request->request("email");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';

        if (!$email || !Validate::isEmail($email)) {
            $this->error('邮箱格式不正确！');
        }
        $last = Emslib::get($email, $event);
        /*        if ($last && time() - $last['create_time'] < 60) {
                    $this->error('发送频繁');
                }*/
        if ($event) {
            $userinfo = Member::getByEmail($email);
            if ($event == 'register' && $userinfo) {
                $this->error('已被注册');
            } elseif (in_array($event, ['changeemail']) && $userinfo) {
                $this->error('已被占用');
            } elseif (in_array($event, ['changepwd', 'resetpwd', 'actemail']) && !$userinfo) {
                $this->error('未注册');
            }
        }
        if (!Hook::get('ems_send')) {
            $this->error('请在后台插件管理安装邮箱验证插件');
        }
        $ret = Emslib::send($email, null, $event);
        if ($ret) {
            $this->success('发送成功');
        } else {
            $this->error('发送失败');
        }
    }

    /**
     * @title 检测验证码
     * @desc 最基础的接口注释写法
     * @author 御宅男
     * @url /api/Ems/check
     * @method GET
     * @tag 邮箱 验证码
     * @param name:email type:string require:1 desc:邮箱
     * @param name:event type:string require:1 desc:事件名称
     * @param name:captcha type:string require:1 desc:验证码
     * @return name:data type:array ref:definitions\dictionary
     */
    public function check()
    {

    }

}
