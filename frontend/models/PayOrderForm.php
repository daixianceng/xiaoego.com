<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use yii\helpers\Url;
use common\models\Order;

class PayOrderForm extends Model
{
    const PLATFORM_ALIPAY = 'alipay';
    
    public $platform;
    
    /**
     * @var \common\models\Order
     */
    private $_order;

    /**
     * Creates a form model given an order sn.
     *
     * @param  string                          $orderSn
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if order sn is empty or not valid
     */
    public function __construct($orderSn, $config = [])
    {
        if (empty($orderSn) || !is_string($orderSn)) {
            throw new InvalidParamException('订单号错误！');
        }
        $this->_order = Order::find()->where([
            'and',
            ['order_sn' => $orderSn],
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
            ['platform', 'required'],
            ['platform', 'in', 'range' => [self::PLATFORM_ALIPAY]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'platform' => '支付平台'
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
        
        try {
            
            switch ($this->platform) {
                case self::PLATFORM_ALIPAY :
                    require_once(Yii::getAlias('@vendor') . '/payment/alipay/alipay.config.php');
                    require_once(Yii::getAlias('@vendor') . '/payment/alipay/lib/alipay_submit.class.php');

                    $parameter = [
                        'service'           => 'create_direct_pay_by_user',
                        'partner'           => trim($alipay_config['partner']),
                        'seller_email'      => trim($alipay_config['seller_email']),
                        'payment_type'      => '1',
                        'notify_url'        => Url::to(['/payment/alipay-order-notify'], true),
                        'return_url'        => Url::to(['/payment/alipay-order-return'], true),
                        'out_trade_no'      => $this->_order->order_sn,
                        'subject'           => '订单支付',
                        'total_fee'         => $this->_order->real_fee,
                        'body'              => '笑e购（xiaoego.com）订单，订单号：' . $this->_order->order_sn,
                        'show_url'          => Url::to(['/order/detail', 'order' => $this->_order->order_sn], true),
                        'anti_phishing_key' => '',
                        'exter_invoke_ip'   => '',
                        '_input_charset'    => trim(strtolower($alipay_config['input_charset']))
                    ];

                    //建立请求
                    $alipaySubmit = new \AlipaySubmit($alipay_config);
                    $htmlText = $alipaySubmit->buildRequestForm($parameter, 'post', '');

                    break;
                default :
                    throw new InvalidValueException();
            }
            
            return $htmlText;
        } catch (\Exception $e) {
            return false;
        }
    }
}
