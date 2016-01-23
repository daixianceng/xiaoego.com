<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_img}}".
 *
 * @property string $id
 * @property string $name
 * @property string $goods_id
 */
class GoodsImg extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_img}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '图片名',
            'goods_id' => '商品'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }
    
    public static function findByGoodsId($goodsId)
    {
        return static::findAll(['goods_id' => $goodsId]);
    }
}
