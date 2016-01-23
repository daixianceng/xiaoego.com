<?php

namespace frontend\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use common\models\Order;

class PaymentController extends Controller
{
    public $enableCsrfValidation = false;
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'alipay-order-notify' => ['post'],
                    'wxpay-order-notify' => ['post'],
                    'webhooks' => ['post']
                ],
            ],
        ];
    }
    
    public function actionAlipayOrderNotify()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        
        require_once(Yii::getAlias('@vendor') . "/payment/alipay/alipay.config.php");
        require_once(Yii::getAlias('@vendor') . "/payment/alipay/lib/alipay_notify.class.php");
        
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        
        if($verify_result) {
            $out_trade_no = $_POST['out_trade_no'];
        
            $trade_no = $_POST['trade_no'];
        
            $trade_status = $_POST['trade_status'];
        
            $model = Order::findOne(['order_sn' => $out_trade_no, 'status' => [Order::STATUS_UNPAID, Order::STATUS_CANCELLED]]);
            
            if ($model) {
                if ($trade_status === 'TRADE_SUCCESS' || $trade_status === 'TRADE_FINISHED') {
                    if ($model->pay()) {
                        $this->_sendMsg($model);
                        Yii::info("订单支付成功！订单号：{$out_trade_no}");
                    } else {
                        Yii::error("订单支付失败！订单号：{$out_trade_no}");
                        return 'fail';
                    }
                }
            }
            
        } else {
            return 'fail';
        }
        
        return 'success';
    }
    
    public function actionAlipayOrderReturn()
    {
        require_once(Yii::getAlias('@vendor') . "/payment/alipay/alipay.config.php");
        require_once(Yii::getAlias('@vendor') . "/payment/alipay/lib/alipay_notify.class.php");
        
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {
            $out_trade_no = $_GET['out_trade_no'];
        
            $trade_no = $_GET['trade_no'];
        
            $trade_status = $_GET['trade_status'];
            
            $model = Order::findOne(['order_sn' => $out_trade_no]);
            
            if ($model) {
                if ($trade_status === 'TRADE_SUCCESS' || $trade_status === 'TRADE_FINISHED') {
                    if ($model->pay()) {
                        $this->_sendMsg($model);
                        Yii::info("订单支付成功！订单号：{$out_trade_no}");
                    } else {
                        Yii::error("订单支付失败！订单号：{$out_trade_no}");
                        Yii::$app->session->setFlash('danger', '订单支付失败！');
                    }
                
                } elseif ($trade_status === 'TRADE_CLOSED') {
                    Yii::$app->session->setFlash('warning', '订单未支付！');
                }
            }
            
        } else {
            throw new ForbiddenHttpException('参数非法。');
        }
        
        return $this->redirect(['/order/detail', 'order' => $model->order_sn]);
    }
    
    public function actionWxpay($order)
    {
        $model = Order::findOne(['order_sn' => $order, 'status' => Order::STATUS_UNPAID]);
        
        if (!$model) {
            throw new BadRequestHttpException('参数错误！');
        }
        
        require_once(Yii::getAlias('@vendor') . "/payment/wxpay/lib/WxPay.Api.php");
        require_once(Yii::getAlias('@vendor') . "/payment/wxpay/lib/WxPay.JsApiPay.php");
        
        try {
            $tools = new \JsApiPay();
            $openId = $tools->GetOpenid();
            
            $input = new \WxPayUnifiedOrder();
            $input->SetBody('笑e购（xiaoego.com）订单，' . $model->description);
            $input->SetOut_trade_no($model->order_sn);
            $input->SetTotal_fee(bcmul($model->real_fee, 100));
            $input->SetTime_start(date("YmdHis", $model->created_at));
            $input->SetTime_expire(date("YmdHis", $model->timeout));
            $input->SetNotify_url(Url::to(['/payment/wxpay-order-notify'], true));
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            
            $orderform = \WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($orderform);
        } catch (\Exception $e) {
            Yii::error("用户请求支付订单失败！订单号：{$order}，支付平台：wxpay");
            throw new BadRequestHttpException($e->getMessage());
        }
        
        Yii::info("用户请求支付订单成功！订单号：{$order}，支付平台：wxpay");
        
        return $this->renderPartial('wxpay', [
            'model' => $model,
            'jsApiParameters' => $jsApiParameters
        ]);
    }
    
    public function actionWxpayOrderNotify()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
    
        require_once(Yii::getAlias('@vendor') . "/payment/wxpay/class/PayNotifyCallBack.php");
    
        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
    }
    
    public function actionWebhooks()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        
        $data = file_get_contents("php://input");
        $signature = Yii::$app->request->headers->get('x-pingplusplus-signature');
        $pubKey = file_get_contents(Yii::getAlias(Yii::$app->params['pingpp.publicKeyPath']));
        
        // Verify signature
        $result = openssl_verify($data, base64_decode($signature), $pubKey, 'sha256');
        
        if ($result !== 1) {
            Yii::$app->response->statusCode = 403;
            return;
        }
        
        $event = json_decode($data);
        
        if (!isset($event->type)) {
            Yii::$app->response->statusCode = 400;
            return;
        }
        switch ($event->type) {
            case "charge.succeeded":
                $charge = $event->data->object;
                $model = Order::findOne(['order_sn' => $charge->order_no]);
                
                if ($model) {
                    if ($charge->paid && $model->pay()) {
                        $this->_sendMsg($model);
                        Yii::info("订单支付成功！订单号：{$charge->order_no}");
                        return;
                    } else {
                        Yii::error("订单支付失败！订单号：{$charge->order_no}");
                    }
                }
                
                break;
            default:
                break;
        }
        
        Yii::$app->response->statusCode = 400;
        return;
    }
    
    private function _sendMsg(Order $model)
    {
        if ($model->store->enable_sms && !empty($model->store->cellphone)) {
            Yii::$app->smser->send($model->store->cellphone, "亲爱的店长,刚刚有人下了订单,订单号为{$model->order_sn},请您快去查看。");
        }
    }
}