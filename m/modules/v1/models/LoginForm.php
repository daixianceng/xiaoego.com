<?php

namespace m\modules\v1\models;

use Yii;

/**
 * Login form
 */
class LoginForm extends \frontend\models\LoginForm
{
    /**
     * Logs in a user using the provided mobile and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->user->generateAccessToken();
            return $this->user->save(false) && Yii::$app->user->loginByAccessToken($this->user->access_token);
        } else {
            return false;
        }
    }
}
