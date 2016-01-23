<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%feedback}}".
 *
 * @property string $id
 * @property string $content
 * @property string $mobile
 * @property string $created_at
 * @property string $updated_at
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%feedback}}';
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
            [['content', 'mobile'], 'required'],
            [['content'], 'string', 'length' => [5, 255]],
            [['mobile'], 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '内容',
            'mobile' => '手机号',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
