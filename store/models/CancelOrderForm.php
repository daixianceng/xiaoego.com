<?php

namespace store\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\Order;

class CancelOrderForm extends Model
{
    public $msg;
    
    /**
     * @var \common\models\Order
     */
    private $_order;

    /**
     * Creates a form model given a order id.
     *
     * @param  string                          $id
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if waybill sn is empty or not valid
     */
    public function __construct($id, $config = [])
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidParamException('参数错误！');
        }
        $this->_order = Order::findOne([
            'id' => $id,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
        
        if (!$this->_order) {
            throw new InvalidParamException('您没有该订单！');
        }
        
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['msg', 'trim'],
            ['msg', 'required'],
            ['msg', 'string', 'length' => [6, 60]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msg' => '取消理由'
        ];
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function cancel($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        if ($this->_order->status !== Order::STATUS_UNSHIPPED) {
            return false;
        }
        
        if ($this->_order->payment !== Order::PAYMENT_OFFLINE) {
            return false;
        }
        
        return $this->_order->cancel($this->msg);
    }
}
