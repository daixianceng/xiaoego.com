<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property string $order_sn
 * @property string $user_id
 * @property string $store_id
 * @property string $school_id
 * @property string $status
 * @property string $payment
 * @property string $fee
 * @property string $real_fee
 * @property string $cost
 * @property string $preferential
 * @property string $down_val
 * @property string $gift_val
 * @property string $new_down_val
 * @property string $book_time
 * @property string $remark
 * @property string $cancelled_msg
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $statusMsg read-only $statusMsg
 * @property string $paymentMsg read-only $paymentMsg
 * @property string $downMsg read-only $downMsg
 * @property string $giftMsg read-only $giftMsg
 * @property string $newDownMsg read-only $newDownMsg
 * @property string $bookTimeMsg read-only $bookTimeMsg
 * @property string $preferentialPrettyMsg read-only $preferentialPrettyMsg
 * @property string $preferentialMsg read-only $preferentialMsg
 * @property string $hasPreferential read-only $hasPreferential
 * @property string $description read-only $description
 * @property string $timeout read-only $timeout
 */
class Order extends \yii\db\ActiveRecord
{
    const EVENT_BEFORE_PAYMENT = 'beforePayment';
    const EVENT_AFTER_PAYMENT = 'afterPayment';
    const EVENT_BEFORE_SHIP = 'beforeShip';
    const EVENT_AFTER_SHIP = 'afterShip';
    const EVENT_BEFORE_COMPLETE = 'beforeComplete';
    const EVENT_AFTER_COMPLETE = 'afterComplete';
    const EVENT_BEFORE_CANCEL = 'beforeCancel';
    const EVENT_AFTER_CANCEL = 'afterCancel';
    const EVENT_BEFORE_DISCARD = 'beforeDiscard';
    const EVENT_AFTER_DISCARD = 'afterDiscard';
    
    const STATUS_UNSHIPPED = 'unshipped';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DELETED = 'deleted';
    
    const PAYMENT_ONLINE = 'online';
    const PAYMENT_OFFLINE = 'offline';
    
    const PREFERENTIAL_DOWN = 'down';
    const PREFERENTIAL_GIFT = 'gift';
    const PREFERENTIAL_NONE = 'none';
    
    private static $_statusList;
    private static $_paymentList;
    private static $_preferentialList;
    
    private $_cost;
    private $_description;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => '订单编号',
            'user_id' => '买家',
            'store_id' => '营业点',
            'school_id' => '学校',
            'status' => '订单状态',
            'payment' => '支付方式',
            'fee' => '费用',
            'real_fee' => '实付款',
            'preferential' => '优惠',
            'down_val' => '满减优惠金额',
            'gift_val' => '满送优惠礼品',
            'book_time' => '送达时间',
            'remark' => '备注',
            'cancelled_msg' => '取消说明',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchool()
    {
        return $this->hasOne(School::className(), ['id' => 'school_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(OrderAddress::className(), ['order_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'id']);
    }
    
    public function beforePayment()
    {
        $this->trigger(self::EVENT_BEFORE_PAYMENT);
    }
    
    public function afterPayment()
    {
        $this->trigger(self::EVENT_AFTER_PAYMENT);
    }
    
    public function beforeShip()
    {
        $this->trigger(self::EVENT_BEFORE_SHIP);
    }
    
    public function afterShip()
    {
        $this->trigger(self::EVENT_AFTER_SHIP);
    }
    
    public function beforeComplete()
    {
        $this->trigger(self::EVENT_BEFORE_COMPLETE);
    }
    
    public function afterComplete()
    {
        $this->trigger(self::EVENT_AFTER_COMPLETE);
    }
    
    public function beforeCancel()
    {
        $this->trigger(self::EVENT_BEFORE_CANCEL);
    }
    
    public function afterCancel()
    {
        $this->trigger(self::EVENT_AFTER_CANCEL);
    }
    
    public function beforeDiscard()
    {
        $this->trigger(self::EVENT_BEFORE_DISCARD);
    }
    
    public function afterDiscard()
    {
        $this->trigger(self::EVENT_AFTER_DISCARD);
    }
    
    public function getDescription()
    {
        if ($this->_description === null) {
            $count = 0;
            $text = '包括';
            
            foreach ($this->goods as $key => $goods) {
                if ($key !== 0) {
                    $text .= '，';
                }
            
                $count += $goods->count;
                $text .= "{$goods->count}{$goods->unit}{$goods->name}";
            }
            
            $this->_description = "该订单共{$count}件商品，{$text}";
        }
        
        return $this->_description;
    }
    
    public function getCost()
    {
        if ($this->_cost === null) {
            $this->_cost = $this->getGoods()->sum('cost * count');
            $this->_cost = bcadd($this->_cost, 0, 4);
        }
        
        return $this->_cost;
    }
    
    public function generateOrderSn()
    {
        $unique = md5(Yii::$app->security->generateRandomString());
        
        $this->order_sn = 'X' . date('Ymd') . strtoupper(substr($unique, 0, 5));
    }
    
    public function clearCancelledMsg()
    {
        $this->cancelled_msg = null;
    }
    
    public function pay()
    {
        $this->beforePayment();
        $this->status = self::STATUS_UNSHIPPED;
        $this->clearCancelledMsg();
        
        if (!$this->save(false)) {
            return false;
        }
        
        $this->afterPayment();
        return true;
    }
    
    public function ship()
    {
        if ($this->status !== self::STATUS_UNSHIPPED) {
            return false;
        }
        $this->beforeShip();
        $this->status = self::STATUS_SHIPPED;
        
        if (!$this->save(false)) {
            return false;
        }
        
        $this->afterShip();
        return true;
    }
    
    public function complete()
    {
        $this->beforeComplete();
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            
            $this->status = self::STATUS_COMPLETED;
            $this->clearCancelledMsg();
            if (!$this->save(false)) {
                throw new \Exception('订单错误！');
            }
            
            $orderVolume = new OrderVolume();
            $orderVolume->volume = $this->real_fee;
            $orderVolume->cost = $this->cost;
            $orderVolume->profit = bcsub($this->real_fee, $this->cost, 4);
            $orderVolume->order_id = $this->id;
            $orderVolume->payment = $this->payment;
            $orderVolume->user_id = $this->user_id;
            $orderVolume->store_id = $this->store_id;
            
            if (!$orderVolume->save(false)) {
                throw new \Exception('记录交易错误！');
            }
            
            $transaction->commit();
            $this->afterComplete();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    
    public function cancel($msg = null)
    {
        $this->beforeCancel();
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
        
            $this->status = self::STATUS_CANCELLED;
            $this->cancelled_msg = $msg;
            if (!$this->save(false)) {
                throw new \Exception('订单错误！');
            }
        
            foreach ($this->goods as $goods) {
                if (!$goods->goods->moveSurplus(+ $goods->count, "取消订单：{$this->order_sn}。")) {
                    throw new \Exception('订单商品错误！');
                }
            }
        
            $transaction->commit();
            $this->afterCancel();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    
    public function discard()
    {
        if (!in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED])) {
            return false;
        }
        
        $this->beforeDiscard();
        $this->status = self::STATUS_DELETED;
        $this->clearCancelledMsg();
        
        if (!$this->save(false)) {
            return false;
        }
        
        $this->afterDiscard();
        return true;
    }
    
    public function getTimeout()
    {
        return $this->created_at + 15 * 60;
    }
    
    public static function getCountByStoreId($id)
    {
        return static::find()->where(['store_id' => $id])->count();
    }
    
    public static function getCountByUserId($id)
    {
        return static::find()->where(['user_id' => $id])->count();
    }
    
    public static function getStatusList()
    {
        if (self::$_statusList === null) {
            self::$_statusList = [
                self::STATUS_UNSHIPPED => '未发货',
                self::STATUS_SHIPPED => '配送中',
                self::STATUS_UNPAID => '待付款',
                self::STATUS_COMPLETED => '订单完成',
                self::STATUS_CANCELLED => '订单取消',
                self::STATUS_DELETED => '订单删除'
            ];
        }
    
        return self::$_statusList;
    }
    
    public function getStatusMsg()
    {
        $list = self::getStatusList();
    
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }
    
    public static function getPaymentList()
    {
        if (self::$_paymentList === null) {
            self::$_paymentList = [
                self::PAYMENT_ONLINE => '在线支付',
                self::PAYMENT_OFFLINE => '货到付款'
            ];
        }
    
        return self::$_paymentList;
    }
    
    public function getPaymentMsg()
    {
        $list = self::getPaymentList();
    
        return isset($list[$this->payment]) ? $list[$this->payment] : null;
    }
    
    public static function getPreferentialList()
    {
        if (self::$_preferentialList === null) {
            self::$_preferentialList = [
                self::PREFERENTIAL_DOWN => '满减优惠',
                self::PREFERENTIAL_GIFT => '满送优惠',
                self::PREFERENTIAL_NONE => '未使用优惠'
            ];
        }
    
        return self::$_preferentialList;
    }
    
    public function getPreferentialMsg()
    {
        $list = self::getPreferentialList();
    
        return isset($list[$this->preferential]) ? $list[$this->preferential] : null;
    }
    
    public function getHasPreferential()
    {
        return in_array($this->preferential, [self::PREFERENTIAL_DOWN, self::PREFERENTIAL_GIFT]);
    }
    
    public static function findByOrderSn($orderSn)
    {
        return static::findOne(['order_sn' => $orderSn]);
    }
    
    public function getBookTimeMsg()
    {
        return $this->book_time === null ? '立即送出' : date('Y-m-d H:i', $this->book_time);
    }
    
    public function getDownMsg()
    {
        return $this->preferential === self::PREFERENTIAL_DOWN ? "已使用满减优惠，减{$this->down_val}元。" : null;
    }
    
    public function getGiftMsg()
    {
        return $this->preferential === self::PREFERENTIAL_GIFT ? "已使用满送优惠，送{$this->gift_val}。" : null;
    }
    
    public function getNewDownMsg()
    {
        return $this->new_down_val ? "已使用新用户立减优惠，减{$this->new_down_val}元" : null;
    }
    
    public function getPreferentialPrettyMsg()
    {
        $msg = null;
        
        if ($this->hasPreferential) {
            $msg = $this->downMsg . $this->giftMsg;
        }
        
        return $msg;
    }
}
