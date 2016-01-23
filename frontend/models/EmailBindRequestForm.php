<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class EmailBindRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => '该邮箱已被使用。'],
            ['email', function ($attribute, $params) {
                $session = Yii::$app->session;
                if ($session->has('emailNext') && $session['emailNext'] > time()) {
                    $this->addError($attribute, '发送邮件过于频繁。');
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
            'email' => '邮箱',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @return boolean whether the email was sent
     */
    public function sendEmail($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        $session = Yii::$app->session;
        $session['emailTimeout'] = time() + 1800;
        $session['emailNext'] = time() + 60;
        $session['emailBind'] = $this->email;
        $session['emailVerifyCode'] = (string) mt_rand(100000, 999999);
        
        return Yii::$app->mailer->compose(['html' => 'bindEmail-html', 'text' => 'bindEmail-text'], ['verifyCode' => $session['emailVerifyCode'], 'mobile' => Yii::$app->user->identity->mobile])
                                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                                ->setTo($this->email)
                                ->setSubject('绑定邮箱')
                                ->send();
    }
}
