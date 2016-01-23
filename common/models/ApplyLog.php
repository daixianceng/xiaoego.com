<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%apply_log}}".
 *
 * @property string $id
 * @property string $apply_id
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 */
class ApplyLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%apply_log}}';
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
            'apply_id' => '采购申请',
            'remark' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApply()
    {
        return $this->hasOne(Apply::className(), ['id' => 'apply_id']);
    }
}
