<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%apply_goods}}".
 *
 * @property string $id
 * @property string $apply_id
 * @property string $goods_id
 * @property string $name
 * @property string $category
 * @property string $count
 * @property string $price
 * @property string $cover
 * @property string $unit
 * @property string $fee read-only
 */
class ApplyGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%apply_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['count'], 'required'],
            [['count'], 'integer', 'min' => 1, 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apply_id' => '采购申请',
            'goods_id' => '商品',
            'name' => '商品名称',
            'category' => '商品分类',
            'count' => '数量',
            'price' => '单价',
            'cover' => '封面',
            'unit' => '单位',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApply()
    {
        return $this->hasOne(Apply::className(), ['id' => 'apply_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
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
    
    /**
     * 从某个商品创建副本
     * 
     * @param string $goodsId
     * @return ApplyGoods
     */
    public static function createDuplicate($goodsId)
    {
        $goods = Goods::findOne($goodsId);
    
        $model = new self();
        $model->goods_id = $goodsId;
        $model->name = $goods->name;
        $model->category = $goods->category->name;
        $model->price = $goods->price;
        $model->cover = $goods->cover;
        $model->unit = $goods->unit;
    
        return $model;
    }
}
