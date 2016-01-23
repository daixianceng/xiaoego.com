<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\Store;
use common\models\Address;
use common\models\Order;
use common\models\OrderAddress;
use common\models\OrderGoods;

class CreateOrderForm extends Model
{
    public $addressId;
    public $payment;
    public $preferential;
    public $bookTime;
    public $remark;
    public $newDown;
    
    /**
     * @var Store
     */
    private $_store;
    
    /**
     * @var Order
     */
    private $_order;
    private $_addressList;
    private $_cartGoodsList;
    private $_preferentialItems;
    private $_bookTimeItems;
    
    public function __construct($id, $config = [])
    {
        $this->_store = Store::findOne($id);
    
        if (!$this->_store) {
            throw new InvalidParamException('未找到该店铺！');
        }
        if ($this->_store->status === Store::STATUS_DISABLED) {
            throw new InvalidParamException('该店铺已禁用！');
        }
    
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_addressList = Yii::$app->user->identity->getAddress($this->_store->school_id)->all();
        $this->_cartGoodsList = Yii::$app->user->identity->getCartGoods($this->_store->id)->all();
        $this->addressId = isset($this->_addressList[0]) ? $this->_addressList[0]->id : null;
        $this->payment = Order::PAYMENT_ONLINE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['addressId', 'payment'], 'required', 'message' => '请选择{attribute}'],
            [
                ['addressId'],
                'exist',
                'targetClass' => Address::className(),
                'targetAttribute' => 'id',
                'filter' => [
                    'user_id' => Yii::$app->user->id,
                    'school_id' => $this->_store->school_id
                ]
            ],
            [['payment'], 'in', 'range' => [Order::PAYMENT_ONLINE, Order::PAYMENT_OFFLINE]],
            
            [['preferential'], 'default', 'value' => Order::PREFERENTIAL_NONE],
            [['preferential'], 'in', 'range' => [Order::PREFERENTIAL_DOWN, Order::PREFERENTIAL_GIFT, Order::PREFERENTIAL_NONE]],
            
            [['newDown'], 'default', 'value' => '0'],
            [['newDown'], 'boolean'],
            
            [['bookTime'], function ($attribute, $params) {
                $list = $this->getBookTimeItems();
                if (empty($list)) {
                    $this->bookTime = '0';
                } elseif (!isset($list[$this->bookTime])) {
                    $this->addError($attribute, '请重新选择送达时间。');
                }
            }],
            
            [['remark'], 'trim'],
            [['remark'], 'default'],
            [['remark'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'addressId' => '收货地址',
            'payment' => '支付方式',
            'preferential' => '选择优惠',
            'bookTime' => '送达时间',
            'remark' => '备注',
            'newDown' => '新用户优惠'
        ];
    }
    
    /**
     * 创建订单
     * 
     * @param string $runValidation
     * @throws \Exception
     * @return boolean
     */
    public function create($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        if ($this->_store->status === Store::STATUS_REST) {
            throw new \Exception('该店铺休息中…');
        }
        
        $volume = Yii::$app->user->identity->getCartGoodsRealVolume($this->_store->id);
        if ($this->_store->has_least && $this->_store->least_val > $volume) {
            throw new \Exception('购物车商品未满起送价！');
        }
        
        if (empty($this->_cartGoodsList)) {
            throw new \Exception('当前购物车为空！');
        }
        
        foreach ($this->_cartGoodsList as $cartGoods) {
            if ($cartGoods->isExpired) {
                throw new \Exception('商品“' . $cartGoods->goods->name . '”已失效！请您删除该商品然后继续。');
            }
            if ($cartGoods->isTooMuch) {
                throw new \Exception('商品“' . $cartGoods->goods->name . '”数量已超出库存数量！请返回购物车中修改。');
            }
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            
            $order = new Order();
            $order->generateOrderSn();
            $order->user_id = Yii::$app->user->id;
            $order->store_id = $this->_store->id;
            $order->school_id = $this->_store->school_id;
            $order->status = $this->payment === Order::PAYMENT_OFFLINE ? Order::STATUS_UNSHIPPED : Order::STATUS_UNPAID;
            $order->payment = $this->payment;
            $order->fee = $volume;
            $order->preferential = $this->preferential;
            $order->down_val = null;
            $order->gift_val = null;
            $order->new_down_val = null;
            $order->book_time = $this->bookTime == 0 ? null : $this->bookTime;
            $order->remark = $this->remark;
            $order->cancelled_msg = null;
            
            // 判断优惠类型
            switch ($this->preferential) {
                case Order::PREFERENTIAL_DOWN :
                    if ($this->_store->has_down && $order->fee >= $this->_store->down_upper) {
                        $order->real_fee = bcsub($order->fee, $this->_store->down_val, 2);
                        $order->down_val = $this->_store->down_val;
                    }
                    break;
                case Order::PREFERENTIAL_GIFT :
                    if ($this->_store->has_gift && $order->fee >= $this->_store->gift_upper) {
                        $order->real_fee = $order->fee;
                        $order->gift_val = $this->_store->gift_val;
                    }
                    break;
                case Order::PREFERENTIAL_NONE :
                    $order->real_fee = $order->fee;
                    break;
                default :
                    throw new \Exception('优惠选择错误！');
            }
            
            // 新用户立减优惠
            if (Yii::$app->params['enableNewDown'] && $this->newDown &&
                $order->fee >= Yii::$app->params['newDownUpper'] && Yii::$app->user->identity->has_new_down) {
                
                $order->new_down_val = Yii::$app->params['newDownVal'];
                $order->real_fee = bcsub($order->real_fee, $order->new_down_val, 2);
                Yii::$app->user->identity->has_new_down = 0;
                
                if (!Yii::$app->user->identity->save(false)) {
                    throw new \Exception('用户错误！');
                }
                
                if ($order->real_fee < 0) {
                    $order->real_fee = 0;
                    $order->status = ORDER::STATUS_UNSHIPPED;
                }
            }
            
            if (!$order->save(false)) {
                throw new \Exception('订单错误！');
            }
            
            $this->_order = $order;
            
            $address = OrderAddress::createDuplicate($this->addressId);
            $address->order_id = $order->id;
            
            if (!$address->save(false)) {
                throw new \Exception('收货地址错误！');
            }
            
            foreach ($this->_cartGoodsList as $cartGoods) {
                $goods = OrderGoods::createDuplicate($cartGoods->goods_id);
                $goods->order_id = $order->id;
                $goods->count = $cartGoods->count;
                
                if (!$goods->save(false)) {
                    throw new \Exception('订单商品错误！');
                }
                if (!$cartGoods->goods->moveSurplus(- $goods->count, "创建订单：{$order->order_sn}。")) {
                    throw new \Exception('商品错误！');
                }
            }
            
            Yii::$app->user->identity->clearCartGoods($this->_store->id);
            
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public function getStore()
    {
        return $this->_store;
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function getAddressList()
    {
        return $this->_addressList;
    }
    
    public function getCartGoodsList()
    {
        return $this->_cartGoodsList;
    }
    
    public function getPreferentialItems()
    {
        if ($this->_preferentialItems === null) {
            $this->_preferentialItems = [];
            $fee = Yii::$app->user->identity->getCartGoodsRealVolume($this->_store->id);
            
            if ($this->_store->has_down && $fee >= $this->_store->down_upper) {
                $this->_preferentialItems[Order::PREFERENTIAL_DOWN] = $this->_store->downMsg;
            }
            if ($this->_store->has_gift && $fee >= $this->_store->gift_upper) {
                $this->_preferentialItems[Order::PREFERENTIAL_GIFT] = $this->_store->giftMsg;
            }
            if (!empty($this->_preferentialItems)) {
                $this->_preferentialItems[Order::PREFERENTIAL_NONE] = '不使用优惠';
            }
        }
        
        return $this->_preferentialItems;
    }
    
    public function getBookTimeItems()
    {
        if ($this->_bookTimeItems === null) {
            $this->_bookTimeItems = [];
            
            if ($this->_store->has_book) {
            	$datetime = new \DateTime();
            	$interval = new \DateInterval('PT30M');
            	$datetime->add(new \DateInterval('PT1H'));
            	
            	if ($datetime->format('i') <= 30) {
            		$datetime->setTime($datetime->format('H'), 0, 0);
            	} else {
            		$datetime->setTime($datetime->format('H'), 30, 0);
            	}
            	
            	$this->_bookTimeItems[] = '立即送出';
            	
            	for ($i = 0; $i < 4; $i ++) {
            		if ($datetime->format('H') == 0) break;
            		$this->_bookTimeItems[$datetime->getTimestamp()] = $datetime->format('H:i');
            		$datetime->add($interval);
            	}
            }
        }
        
        return $this->_bookTimeItems;
    }
}
