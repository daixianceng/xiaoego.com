<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%goods_surplus}}".
 *
 * @property string $id
 * @property string $goods_id
 * @property string $surplus_before
 * @property integer $amount
 * @property string $surplus_after
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 */
class GoodsSurplus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_surplus}}';
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
            'goods_id' => '商品',
            'surplus_before' => '变更之前',
            'amount' => '变更值',
            'surplus_after' => '变更之后',
            'remark' => '备注',
            'created_at' => '记录时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }
}
