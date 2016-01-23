<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Password reset mobile request form
 */
class PasswordResetMobileRequestForm extends Model
{
    public $mobile;
    public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'captcha'], 'trim'],
            [['mobile', 'captcha'], 'required'],
            ['mobile', 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/'],
            ['mobile', 'exist',
                'targetClass' => User::className(),
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => '该手机号不存在或已禁用！'
            ],
            [['captcha'], 'captcha', 'captchaAction' => 'site/password-reset-captcha'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'captcha' => '验证码',
        ];
    }

    public function sendMsg()
    {
        $session = Yii::$app->session;
        $session->open();
        
        $verifyCode = (string) mt_rand(100000, 999999);
        
        $sent = Yii::$app->smser->send($this->mobile, "亲爱的用户,您的验证码为{$verifyCode},该验证码仅作为找回密码使用。");
        
        if ($sent) {
            $session['passwordResetSendNext'] = time() + 60;
            $session['passwordResetVerifyCode'] = $verifyCode;
        }
        
        return $sent;
    }
}
