<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%purchase}}".
 *
 * @property string $id
 * @property string $goods_id
 * @property string $store_id
 * @property string $count
 * @property string $created_at
 * @property string $updated_at
 */
class Purchase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchase}}';
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
    public function rules()
    {
        return [
            [['count'], 'integer']
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
            'store_id' => '店铺',
            'count' => '采购数量',
            'created_at' => '创建时间',
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
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }
    
    /**
     * 获取该商品是否失效
     *
     * @return boolean
     */
    public function getIsExpired()
    {
        return $this->goods->status === Goods::STATUS_DELETED;
    }
    
    public static function hasGoods($id)
    {
        return static::find()->where(['goods_id' => $id])->exists();
    }
    
    public static function clear($storeId)
    {
        return static::deleteAll(['store_id' => $storeId]);
    }
    
    public static function getVolumeByStoreId($storeId)
    {
        $sql = 'SELECT sum(t0.count * t1.price) FROM ' . static::tableName() . ' AS t0 LEFT JOIN ' . Goods::tableName() .
               ' AS t1 ON t0.goods_id = t1.id WHERE t0.store_id=:store_id;';
        $volume = Yii::$app->db->createCommand($sql, [':store_id' => $storeId])->queryScalar();
        
        return bcadd($volume, '0', 2);
    }
}
