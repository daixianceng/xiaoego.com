<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%school}}".
 *
 * @property string $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class School extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%school}}';
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
            [['name'], 'required'],
            [['name'], 'string', 'max' => 60]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '学校名称',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(Store::className(), ['school_id' => 'id'])->where(['<>', 'status', Store::STATUS_DISABLED])->orderBy('name ASC');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildings()
    {
        return $this->hasMany(Building::className(), ['school_id' => 'id']);
    }
    
    public static function getKeyValuePairs()
    {
        $sql = 'SELECT id, name FROM ' . self::tableName() . ' ORDER BY name ASC';
    
        return Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_KEY_PAIR);
    }
}
