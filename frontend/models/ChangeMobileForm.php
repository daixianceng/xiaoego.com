<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class ChangeMobileForm extends Model
{
    public $mobile;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'verifyCode'], 'trim'],
            [['mobile', 'verifyCode'], 'required'],
            
            [['mobile'], 'match', 'pattern' => '/^1[3|5|7|8|][0-9]{9}$/'],
            [['mobile'], 'unique', 'targetClass' => '\common\models\User', 'message' => '该手机号已被注册！'],
            [['mobile'], function ($attribute, $params) {
                $session = Yii::$app->session;
                if ($session->has('mobileChange') && $session['mobileChange'] !== $this->mobile) {
                    $this->addError($attribute, '该手机号与上次不匹配！');
                }
            }],
            
            [['verifyCode'], 'string', 'length' => 6],
            [['verifyCode'], function ($attribute, $params) {
                $session = Yii::$app->session;
                if (!$session->has('mobileChange') || !$session->has('mobileChangeTimeout') || !$session->has('mobileChangeVerifyCode')) {
                    $this->addError($attribute, '请您发送验证码！');
                    return;
                }
                if ($session['mobileChangeTimeout'] < time()) {
                    $this->addError($attribute, '您的验证码已经过期，请重新发送。');
                    return;
                }
                if ($session['mobileChangeVerifyCode'] !== $this->verifyCode) {
                    $this->addError($attribute, '验证码不匹配！');
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '新手机号',
            'verifyCode' => '验证码',
        ];
    }
    
    public function change($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        $user = Yii::$app->user->identity;
        $user->mobile = $this->mobile;
    
        return $user->save(false);
    }
}
