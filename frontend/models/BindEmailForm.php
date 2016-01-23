<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class BindEmailForm extends Model
{
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['verifyCode', 'required'],
            ['verifyCode', 'string', 'length' => 6],
            ['verifyCode', function ($attribute, $params) {
                $session = Yii::$app->session;
                if (!$session->has('emailBind') ||
                    !$session->has('emailTimeout') ||
                    !$session->has('emailVerifyCode')) {
                    $this->addError($attribute, '请您发送验证码！');
                }
                if ($session['emailTimeout'] < time()) {
                    $this->addError($attribute, '您的验证码已经过期，请重新发送。');
                    return false;
                }
                if ($session['emailVerifyCode'] !== $this->verifyCode) {
                    $this->addError($attribute, '验证码不匹配！');
                }
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => '验证码',
        ];
    }
    
    public function bind($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        $user = Yii::$app->user->identity;
        $user->email = Yii::$app->session['emailBind'];
    
        return $user->save(false);
    }
}
