<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $mobile;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'password'], 'required'],
            [['mobile'], 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/'],
            
            [['password'], 'match', 'pattern' => '/^\S+$/'],
            [['password'], 'string', 'length' => [6, 32]],
            [['password'], 'validatePassword'],
            
            [['rememberMe'], 'boolean'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '手机号或密码不正确！');
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'password' => '密码',
            'rememberMe' => '下次记住我'
        ];
    }

    /**
     * Logs in a user using the provided mobile and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[mobile]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByMobile($this->mobile);
        }

        return $this->_user;
    }
}
