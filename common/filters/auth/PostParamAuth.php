<?php

namespace common\filters\auth;

use yii\filters\auth\AuthMethod;

/**
 * PostParamAuth is an action filter that supports the authentication based on the access token passed through a post parameter.
 *
 * @author Cosmo <daixianceng@gmail.com>
 */
class PostParamAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->post($this->tokenParam);
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
