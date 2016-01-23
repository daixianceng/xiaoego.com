<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%apply}}".
 *
 * @property string $id
 * @property string $apply_sn
 * @property string $store_id
 * @property string $fee
 * @property string $status
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 * @property string $statusMsg read-only $statusMsg
 */
class Apply extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PASSED = 'passed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    
    private static $_statusList;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%apply}}';
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
            'apply_sn' => '申请单号',
            'store_id' => '店铺',
            'fee' => '总计',
            'status' => '状态',
            'remark' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasMany(ApplyGoods::className(), ['apply_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(ApplyLog::className(), ['apply_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }
    
    public function generateApplySn()
    {
        $unique = md5(Yii::$app->security->generateRandomString());
    
        $this->apply_sn = date('Ymd') . strtoupper(substr($unique, 0, 4));
    }
    
    public static function getStatusList()
    {
        if (self::$_statusList === null) {
            self::$_statusList = [
                self::STATUS_PENDING => '申请中',
                self::STATUS_REJECTED => '驳回',
                self::STATUS_PASSED => '通过',
                self::STATUS_COMPLETED => '完成',
                self::STATUS_CANCELLED => '取消'
            ];
        }
    
        return self::$_statusList;
    }
    
    public function getStatusMsg()
    {
        $list = self::getStatusList();
    
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }
}
