<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%order_goods}}".
 *
 * @property string $id
 * @property string $order_id
 * @property string $goods_id
 * @property string $name
 * @property string $category
 * @property string $price
 * @property string $cost
 * @property string $count
 * @property string $cover
 * @property string $unit
 * @property string $fee read-only $fee
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单',
            'goods_id' => '商品',
            'name' => '商品名称',
            'category' => '分类',
            'price' => '单价',
            'cost' => '成本价',
            'count' => '数量',
            'cover' => '封面图片',
            'unit' => '单位',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
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
     * 获取总价
     * 
     * @return string
     */
    public function getFee()
    {
        return bcmul($this->price, $this->count, 2);
    }
    
    public static function createDuplicate($goodsId)
    {
        $goods = Goods::findOne($goodsId);
    
        $model = new self();
        $model->goods_id = $goodsId;
        $model->name = $goods->name;
        $model->category = $goods->category->name;
        $model->price = $goods->price;
        $model->cost = $goods->cost;
        $model->cover = $goods->cover;
        $model->unit = $goods->unit;
    
        return $model;
    }
}
