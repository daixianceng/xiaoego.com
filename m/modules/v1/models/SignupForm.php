<?php

namespace m\modules\v1\models;

/**
 * Signup form
 */
class SignupForm extends \frontend\models\SignupForm
{
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
            
            [['captcha'], 'captcha', 'captchaAction' => 'v1/default/captcha'],
        ];
    }
}
