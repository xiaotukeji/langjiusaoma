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
// | 支付模块模型
// +----------------------------------------------------------------------
namespace addons\pay\model;
use app\member\model\Member as MemberModel;
use think\Db;
use app\member\model\Member;
use app\member\service\User;
use PayPal\Common\PayPalModel;
use think\Model;
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
class PayRechargeOrder extends Model
{
    // 定义时间戳字段名
    protected $autoWriteTimestamp = true;
    protected $updateTime         = false;

    /**
     * 发起订单支付
     * @param float  $money
     * @param string $paytype
     */
    public static function submitOrder($money, $pay_type = 'wechat', $method = 'web', $openid = '', $notifyurl = '', $returnurl = '',$buy_years = 0)
    {
        $auth    = user::instance();
        $user_id = $auth->isLogin() ? $auth->id : 0;



        $order = self::where('user_id', $user_id)->where('amount', $money)->where('status', 'unpay')->order('id', 'desc')->find();
        if (!$order) {

            $orderid = date("Ymdhis") . sprintf("%08d", $user_id) . mt_rand(1000, 9999);
            $data    = [
                'orderid'   => $orderid,
                'user_id'   => $user_id,
                'amount'    => $money,
                'payamount' => 0,
                'pay_type'  => $pay_type,
                'ip'        => request()->ip(),
                'useragent' => substr(request()->server('HTTP_USER_AGENT'), 0, 255),
                'status'    => 'unpay',
                'buy_years'=>$buy_years
            ];
            $order = self::create($data);
        }



        if ($pay_type == 'paypal'){
/*            $clientId = 'AdaNca4SiKs8CVTc_7_yLro44smIqsyht06pIAqBusP8X3cpcNyEfhndFmaMJ8g1a2Iu1xV6GunYxvCj';
            $clientSecret = 'EAoy_dSMBSsXbXKYrgGUSqH8Upl20hPQvMxkyTsivxOUEm2twpUmHVnjTbqdkrG_0Tv1oWUvSOVL8wCo';*/
                 $clientId = 'ATjkEfxfTDSe_kWRvirh24_pveBq6mbi50bFcmVLotteiVK4kg237dFolfQxdQ3wAvYsIuGkjRsgjPbq';
                 $clientSecret = 'EDPO00ssFi6x-NwHgPCSCO7hKLaJqxc6BpIl3GEwwQDutV_X22vdp7gZoXf2W8UBk8daEaa2RO_XJWDv';

            $clientId = 'AVZk4GX2z7jsGiSugX9qL26n2cbvV0252ld0O-vo-jFnYR6I1GyiiBTg3NcuKSxxgnHxSNY5Dgas26p1';
            $clientSecret = 'EOm3d-bt33-twsDpY1GEPoxjwz9t8LmnNregV7oI35ctOG8SDx_nj2t63BV4fvbUUCtCBpFNH6CGxVeH';

            $apiContext = new ApiContext(
                new OAuthTokenCredential(
                    $clientId,
                    $clientSecret
                )
            );

            $apiContext->setConfig(
                array(
                    /*'mode' => 'sandbox',*/
                    'mode' => 'live',
                    'log.LogEnabled' => true,
                    'log.FileName' => '../runtime/log/PayPal.log',
                    'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                    'cache.enabled' => true,

                )
            );

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
                ->setSubtotal($money);

            $amount = new Amount();
            $amount->setCurrency("USD")
                ->setTotal($money)
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
            $redirectUrls->setReturnUrl(request()->root(true) ."/index/pay/ExecutePayment?success=true")
                ->setCancelUrl(request()->root(true) ."/index/pay/ExecutePayment?success=false");

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
            $payment->create($apiContext);
            $approvalUrl = $payment->getApprovalLink();

            //需要将payment_id写入到order表
            $temp = json_decode($payment,true);
            $paypal_id = $temp['id'];

            $pay_data = [
              'paypal_id' =>  $paypal_id
            ];
            $rs = DB::name('pay_recharge_order')
                ->where('orderid',$order->orderid)
                ->update($pay_data);



            header("location:{$approvalUrl}");
            /*var_dump($approvalUrl);*/
            exit;
        }





        $notifyurl = request()->root(true) . '/index/pay/epay/type/notify/pay_type/' . $pay_type;
        $returnurl = request()->root(true) . '/index/pay/epay/type/return/pay_type/' . $pay_type;

        //小程序和公众号openid不能为空
        if (in_array($method, ['mp', 'miniapp']) && empty($openid)) {
            throw new Exception("公众号和小程序支付openid不能为空！");
        }

        \addons\pay\library\Service::submitOrder($money, $order->orderid, $pay_type, "充值 {$money}元", $notifyurl, $returnurl);
        exit;

    }

    /**
     * 订单结算
     * @param int    $orderid
     * @param string $payamount
     * @param string $remark
     * @return bool
     */
    public static function settle($orderid, $payamount = null, $remark = '')
    {
        $order = self::getByOrderid($orderid);
        if (!$order) {
            return false;
        }

      /*  var_dump($order);*/
        if ($order['status'] != 'succ') {
            $order->payamount = $payamount;
            $order->pay_time  = time();
            $order->status    = 'succ';
            $order->remark    = $remark;
            $order->save();
            // 更新会员余额
            Member::amount($payamount, $order->user_id, 'VIP-Charge');

            //升级会员
            $money = $order['amount'];
            $buy_years = $order['buy_years'];
            $uid = $order['user_id'];
            //升级IVP

            $member_info = DB::name("member")->where('id',$uid)->find();
            if ($member_info && $buy_years > 0){
                $member_overduedate =  $member_info['overduedate'];
                $buydate = 365*$buy_years*86400;
                $overduedate = $member_overduedate > time() ? ($member_overduedate + $buydate) : (time() + $buydate);
                MemberModel::where('id', $uid)->update(['groupid' => 2, 'overduedate' => $overduedate, 'vip' => 1]);
                //消费记录
                MemberModel::amount(-$money, $uid, 'VIP-Use');
            }



        }
        return true;
    }

}
