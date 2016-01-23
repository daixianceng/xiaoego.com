<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%building}}".
 *
 * @property string $id
 * @property string $name
 * @property string $school_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $statusMsg read-only $statusMsg
 */
class Building extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';
    
    private static $_statusList;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building}}';
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
            [['name', 'school_id'], 'required'],
            [['name'], 'string', 'max' => 60],
            [['school_id'], 'integer'],
            [['school_id'], 'exist', 'targetClass' => School::className(), 'targetAttribute' => 'id']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '建筑名',
            'school_id' => '学校',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchool()
    {
        return $this->hasOne(School::className(), ['id' => 'school_id']);
    }

    public static function getStatusList()
    {
        if (self::$_statusList === null) {
            self::$_statusList = [
                self::STATUS_ENABLED => '正常',
                self::STATUS_DISABLED => '禁用'
            ];
        }
        
        return self::$_statusList;
    }

    public function getStatusMsg()
    {
        $list = self::getStatusList();
        
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }
    
    public static function getKeyValuePairs($schoolId)
    {
        $sql = 'SELECT id, name FROM ' . self::tableName() . ' WHERE school_id=:school_id ORDER BY name ASC';
        
        return Yii::$app->db->createCommand($sql, [':school_id' => $schoolId])->queryAll(\PDO::FETCH_KEY_PAIR);
    }
}
