<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use himiklab\sortablegrid\SortableGridBehavior;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property string $id
 * @property string $store_id
 * @property string $name
 * @property string $sort
 * @property string $created_at
 * @property string $updated_at
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'name'], 'required'],
            [['store_id'], 'integer'],
            [['store_id'], 'exist', 'targetClass' => Store::className(), 'targetAttribute' => 'id'],
            [['name'], 'string', 'max' => 8]
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '营业点',
            'name' => '标签名',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
