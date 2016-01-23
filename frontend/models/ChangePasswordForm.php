<?php

namespace frontend\models;

use yii\base\Model;
use Yii;

/**
 * Password change form
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $passwordRepeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'passwordRepeat'], 'required'],
            [['password', 'passwordRepeat'], 'string', 'min' => 6, 'max' => 24],
            [['password', 'passwordRepeat'], 'match', 'pattern' => '/^\S+$/'],
            
            ['password', 'validatePassword'],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => '两次密码不一致。'],
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
            $user = Yii::$app->user->identity;
            if ($user->validatePassword($this->password)) {
                $this->addError($attribute, '新密码不可与当前密码相同。');
            }
        }
    }
    
    public function attributeLabels()
    {
        return [
            'password' => '新密码',
            'passwordRepeat' => '确认新密码'
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function change($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        $user = Yii::$app->user->identity;
        $user->password = $this->password;

        return $user->save(false);
    }
}