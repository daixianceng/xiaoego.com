<?php

namespace common\filters\auth;

use yii\filters\auth\AuthMethod;

/**
 * HeaderParamAuth is an action filter that supports the authentication based on the access token passed through a header parameter.
 *
 * @author Cosmo <daixianceng@gmail.com>
 */
class HeaderParamAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'X-Auth-Token';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->headers->get($this->tokenParam);
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
