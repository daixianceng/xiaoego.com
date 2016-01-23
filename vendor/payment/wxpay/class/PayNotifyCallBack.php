<?php
use common\models\Order;

require_once(Yii::getAlias('@vendor') . "/payment/wxpay/lib/WxPay.Api.php");
require_once(Yii::getAlias('@vendor') . "/payment/wxpay/lib/WxPay.Notify.php");

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
	    if(!array_key_exists("transaction_id", $data)){
	        $msg = "输入参数不正确";
	        return false;
	    }
	    //查询订单，判断订单真实性
	    //if(!$this->Queryorder($data["transaction_id"])){
	    //    $msg = "订单查询失败";
	    //    return false;
	    //}
	    
	    $model = Order::findOne(['order_sn' => $data['out_trade_no'], 'status' => [Order::STATUS_UNPAID, Order::STATUS_CANCELLED]]);
	    
	    if ($model) {
            if ($model->pay()) {
                Yii::info("订单支付成功！订单号：{$model->order_sn}");
                return true;
            } else {
                Yii::error("订单支付失败！订单号：{$model->order_sn}");
            }
	    }
	    
		return false;
	}
}
