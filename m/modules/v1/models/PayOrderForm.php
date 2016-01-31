<?php

namespace m\modules\v1\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use common\models\Order;

class PayOrderForm extends Model
{
    const CHANNEL_ALIPAY_WAP = 'alipay_wap';
    const CHANNEL_WX_PUB = 'wx_pub';
    const CHANNEL_ALIPAY_PC_DIRECT = 'alipay_pc_direct';
    
    public $channel;
    
    /**
     * @var \common\models\Order
     */
    private $_order;

    /**
     * Creates a form model given an order id.
     *
     * @param  string                          $id
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if order id is not valid
     */
    public function __construct($id, $config = [])
    {
        $this->_order = Order::find()->where([
            'and',
            ['id' => $id],
            ['user_id' => Yii::$app->user->id],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
        
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
            ['channel', 'required'],
            ['channel', 'in', 'range' => [self::CHANNEL_ALIPAY_WAP, self::CHANNEL_WX_PUB, self::CHANNEL_ALIPAY_PC_DIRECT]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'channel' => '支付渠道'
        ];
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function pay($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        if ($this->_order->status !== Order::STATUS_UNPAID) {
            return false;
        }
        
        require_once(Yii::getAlias('@vendor') . "/pingplusplus/pingpp-php/init.php");
        \Pingpp\Pingpp::setApiKey(Yii::$app->params['pingpp.apiKey']);
        
        try {
            $extra = [];
            switch ($this->channel) {
                case self::CHANNEL_ALIPAY_WAP :
                    $extra = [
                        'success_url' => Yii::$app->request->hostInfo . '/#/order/detail/' . $this->_order->id,
                        'cancel_url' => Yii::$app->request->hostInfo . '/#/order/pay/' . $this->_order->id
                    ];
                    break;
                case self::CHANNEL_WX_PUB :
                    Yii::$app->session->open();
                    $extra = [
                        'open_id' => Yii::$app->session['wechatOpenid']
                    ];
                    break;
                case self::CHANNEL_ALIPAY_PC_DIRECT :
                    $extra = [
                        'success_url' => Yii::$app->request->hostInfo . '/#/order/detail/' . $this->_order->id
                    ];
                    break;
                default :
                    throw new InvalidValueException('支付渠道错误！');
            }
            
            $ch = \Pingpp\Charge::create([
                'subject'     => '笑e购订单',
                'body'        => '笑e购（xiaoego.com）订单，订单号：' . $this->_order->order_sn,
                'amount'      => bcmul($this->_order->real_fee, 100),
                'order_no'    => $this->_order->order_sn,
                'currency'    => 'cny',
                'extra'       => $extra,
                'channel'     => $this->channel,
                'client_ip'   => Yii::$app->request->userIP,
                'time_expire' => $this->_order->timeout + 1800,
                'app'         => ['id' => Yii::$app->params['pingpp.appId']],
                'description' => mb_strlen($this->_order->description, 'UTF-8') <= 255 ? $this->_order->description : substr($this->_order->description, 0, 253) . '……'
            ]);
            
            return $ch;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
