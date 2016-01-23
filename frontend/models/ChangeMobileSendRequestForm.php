<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class ChangeMobileSendRequestForm extends Model
{
    public $mobile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mobile', 'trim'],
            ['mobile', 'required'],
            ['mobile', 'match', 'pattern' => '/^1[3|5|7|8|][0-9]{9}$/'],
            ['mobile', 'unique', 'targetClass' => '\common\models\User', 'message' => '该手机号已被注册！'],
            ['mobile', function ($attribute, $params) {
                $session = Yii::$app->session;
                if ($session->has('mobileChangeNext') && $session['mobileChangeNext'] > time()) {
                    $this->addError($attribute, '发送验证码过于频繁。');
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
            'mobile' => '手机号',
        ];
    }

    public function sendMsg($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        $session = Yii::$app->session;
        $session['mobileChangeTimeout'] = time() + 600;
        $session['mobileChangeNext'] = time() + 60;
        $session['mobileChange'] = $this->mobile;
        $session['mobileChangeVerifyCode'] = (string) mt_rand(100000, 999999);

        return Yii::$app->smser->send($this->mobile, "亲爱的用户,您的验证码是{$session['mobileChangeVerifyCode']},请于10分钟内使用。如非本人操作,请忽略该短信。");
    }
}