<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $mobile;
    public $captcha;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'captcha', 'password'], 'trim'],
            [['mobile', 'captcha', 'password'], 'required'],

            [['mobile'], 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/'],
            [['mobile'], 'unique', 'targetClass' => '\common\models\User', 'message' => '该手机号已被注册！'],
            
            [['password'], 'match', 'pattern' => '/^\S+$/'],
            [['password'], 'string', 'length' => [6, 32]],
            
            [['captcha'], 'captcha'],
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
            'password' => '密码'
        ];
    }
    
    public function writeSession()
    {
        $session = Yii::$app->session;
        $session->open();
        $session['mobileSignupTimeout'] = time() + 600;
        $session['mobileSignup'] = $this->mobile;
        $session['mobileSignupPassword'] = $this->password;
    }
}
