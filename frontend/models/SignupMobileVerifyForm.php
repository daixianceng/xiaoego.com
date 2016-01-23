<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserAccount;

class SignupMobileVerifyForm extends Model
{
    public $verifyCode;
    
    /**
     * @var \yii\web\Session
     */
    private $_session;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['verifyCode'], 'string', 'length' => 6],
            [['verifyCode'], function ($attribute, $params) {
                if ($this->_session['mobileSignupVerifyCode'] !== $this->verifyCode) {
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

    /**
     * Signs user up.
     *
     * @return User|false the saved model or false if saving fails
     */
    public function signup($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
    
        $user = new User();
        $user->mobile = $this->_session['mobileSignup'];
        $user->setPassword($this->_session['mobileSignupPassword']);
        $user->generateAuthKey();
        $user->generateAccessToken();
    
        $transaction = Yii::$app->db->beginTransaction();
        try {
    
            if (!$user->save(false)) {
                throw new \Exception();
            }
    
            $userAccount = new UserAccount();
            $userAccount->id = $user->id;
            $userAccount->password_hash = null;
    
            if (!$userAccount->save(false)) {
                throw new \Exception();
            }
    
            $transaction->commit();
            return $user;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    
    public function clearSession()
    {
        unset($this->_session['mobileSignupTimeout']);
        unset($this->_session['mobileSignup']);
        unset($this->_session['mobileSignupPassword']);
        unset($this->_session['mobileSignupNext']);
        unset($this->_session['mobileSignupVerifyCode']);
    }
}