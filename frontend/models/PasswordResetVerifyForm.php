<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Password reset verify form
 */
class PasswordResetVerifyForm extends Model
{
    public $verifyCode;

    /**
     * @var \yii\web\Session
     */
    private $_session;
    
    /**
     * @var User
     */
    private $_user;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['verifyCode'], 'required'],
            [['verifyCode'], 'string', 'length' => 6],
            [['verifyCode'], function ($attribute, $params) {
                if ($this->_session['passwordResetVerifyCode'] !== $this->verifyCode) {
                    $this->addError($attribute, '验证码不匹配！');
                }
            }],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_session = Yii::$app->session;
        $this->_session->open();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => '手机验证码',
        ];
    }
    
    public function generateToken()
    {
        $mobile = $this->_session['passwordResetMobile'];
        
        $this->_user = User::findByMobile($mobile);
        if (!User::isPasswordResetTokenValid($this->_user->password_reset_token)) {
            $this->_user->generatePasswordResetToken();
        }
        
        return $this->_user->save(false);
    }
    
    public function clearSession()
    {
        unset($this->_session['passwordResetTimeout']);
        unset($this->_session['passwordResetMobile']);
        unset($this->_session['passwordResetSendNext']);
        unset($this->_session['passwordResetVerifyCode']);
    }
    
    public function getUser()
    {
        return $this->_user;
    }
}