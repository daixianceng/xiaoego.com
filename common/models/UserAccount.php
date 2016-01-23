<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_account}}".
 *
 * @property string $id
 * @property string $balance
 * @property string $password_hash
 */
class UserAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_account}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['balance'], 'number'],
            [['password_hash'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'balance' => '余额',
            'password_hash' => '支付密码',
        ];
    }
}
