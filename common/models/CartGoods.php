<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%cart_goods}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $goods_id
 * @property string $store_id
 * @property string $price
 * @property string $count
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $isExpired read-only $isExpired
 * @property boolean $isTooMuch read-only $isTooMuch
 */
class CartGoods extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cart_goods}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'store_id', 'goods_id', 'price'], 'required'],
            [['user_id', 'store_id', 'goods_id', 'count'], 'integer'],
            [['price'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'goods_id' => '商品',
            'store_id' => '营业点',
            'price' => '加入购物车时的商品价格',
            'count' => '商品数量',
            'created_at' => '加入时间',
            'updated_at' => '更新时间'
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
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(GoodsImg::className(), ['goods_id' => 'goods_id']);
    }
    
    /**
     * 获取该商品是否失效
     * 
     * @return boolean
     */
    public function getIsExpired()
    {
        return $this->goods->status !== Goods::STATUS_NORMAL;
    }
    
    /**
     * 获取该商品数量是否超出范围
     *
     * @return boolean
     */
    public function getIsTooMuch()
    {
        return $this->count > $this->goods->surplus;
    }
}
