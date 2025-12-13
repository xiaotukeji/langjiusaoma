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
// | 支付API接口控制器
// +----------------------------------------------------------------------
namespace app\index\controller;

use addons\pay\library\Service;
use app\common\controller\Homebase;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use think\facade\Session;
use think\Response;
use Yansongda\Pay\Exceptions\GatewayException;
use Yansongda\Pay\Pay;

class Payapi extends HomeBase
{
    /**
     * 微信支付
     * @return string
     */
    public function wechat()
    {
        $config    = Service::getConfig('wechat');
        $isWechat  = stripos($this->request->server('HTTP_USER_AGENT'), 'MicroMessenger') !== false;
        $orderData = Session::get("wechatorderdata");
        $jumpUrl   = Session::get("jumpUrl") ?: url('index/pay/amountlog');
        if ($isWechat) {
            $type = 'jsapi';
            $this->assign("orderData", $orderData);
        } else {
            //发起PC支付(Native支付)
            $data = [
                'body'         => $orderData['body'],
                'code_url'     => $orderData['code_url'],
                'out_trade_no' => $orderData['out_trade_no'],
                'total_fee'    => $orderData['total_fee'],
            ];
            //检测订单状态
            if ($this->request->isPost()) {
                $pay = Pay::wechat($config);
                try {
                    $result = $pay->find($orderData['out_trade_no']);
                    if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                        $this->success('', $jumpUrl, ['trade_state' => $result['trade_state']]);
                    } else {
                        $this->error("查询失败");
                    }
                } catch (GatewayException $e) {
                    $this->error("查询失败");
                }
            }
            $type = 'pc';
            $this->assign("data", $data);
        }
        $this->assign("type", $type);
        return $this->fetch('wechat');
    }

    /**
     * 支付宝支付
     * @return string
     */
    public function alipay()
    {
        $config    = Service::getConfig('alipay');
        $orderData = Session::get("alipayorderdata");
        $jumpUrl   = Session::get("jumpUrl") ?: url('index/pay/amountlog');
        $data      = [
            'body'         => $orderData['body'],
            'qr_code'      => $orderData['qr_code'],
            'out_trade_no' => $orderData['out_trade_no'],
            'total_fee'    => $orderData['total_fee'],
        ];
        //检测订单状态
        if ($this->request->isPost()) {
            $pay = Pay::alipay($config);
            try {
                $result = $pay->find($orderData['out_trade_no']);
                if (in_array($result['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                    $this->success('', $jumpUrl, ['trade_status' => $result['trade_status']]);
                } else {
                    $this->error("查询失败");
                }
            } catch (GatewayException $e) {
                $this->error("查询失败");
            }
        }
        $this->assign("data", $data);
        return $this->fetch('alipay');
    }

    /**
     * 生成二维码
     * @return Response
     */
    public function qrcode()
    {
        $text   = $this->request->get('text', 'hello world');
        $qrCode = new QrCode($text);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setSize(250);
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setValidateResult(false);

        return new Response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

}
