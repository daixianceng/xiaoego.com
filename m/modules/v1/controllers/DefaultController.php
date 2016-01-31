<?php

namespace m\modules\v1\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use common\models\Feedback;
use frontend\models\SignupMobileVerifyForm;
use m\modules\v1\models\SignupForm;
use m\modules\v1\models\LoginForm;

class DefaultController extends Controller
{
    protected function verbs()
    {
        return [
            'login' => ['post'],
            'signup' => ['post'],
            'feedback' => ['post']
        ];
    }
    
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'width' => 80,
                'height' => 27,
                'padding' => 1,
                'offset' => 1,
                'backColor' => 0xFFFFFF,
                'foreColor' => 0xE50A4A,
                'minLength' => 4,
                'maxLength' => 4
            ]
        ];
    }
    
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post(), '');
        
        if ($model->login()) {
            return [
                'status' => 'success',
                'data' => ['token' => $model->user->access_token]
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => ['errors' => $model->getFirstErrors()]
            ];
        }
    }
    
    public function actionSignup($step = '1')
    {
        if ($step === '1') {
            $model = new SignupForm();
            
            if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
                $model->writeSession();
                if ($sent = $this->_sendMsg($model->mobile)) {
                    Yii::info("用户注册发送短信验证码成功！手机号：{$model->mobile}");
                } else {
                    Yii::warning("用户注册发送短信验证码失败！手机号：{$model->mobile}，说明：" . Yii::$app->smser->message);
                }
                
                return [
                    'status' => 'success',
                    'data' => [
                        'isSent' => $sent
                    ]
                ];
            } else {
                return [
                    'status' => 'fail',
                    'data' => [
                        'errors' => $model->getFirstErrors()
                    ]
                ];
            }
        } elseif ($step === '2') {
            $session = Yii::$app->session;
            $session->open();
            
            if (empty($session['mobileSignup']) || empty($session['mobileSignupTimeout']) || $session['mobileSignupTimeout'] < time()) {
                return [
                    'status' => 'fail',
                    'data' => [
                        'errors' => ['对不起，请您重新注册。']
                    ]
                ];
            }
            
            $model = new SignupMobileVerifyForm();
            
            if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
                if ($user = $model->signup()) {
                    $model->clearSession();
                    return [
                        'status' => 'success',
                        'data' => [
                            'accessToken' => $user->access_token
                        ]
                    ];
                }
            }
            
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getErrors('verifyCode') ?: ['注册失败，请稍后再试。']
                ]
            ];
        } else {
            throw new BadRequestHttpException('参数错误！');
        }
    }
    
    public function actionSendMsg()
    {
        $session = Yii::$app->session;
        $session->open();
    
        $message = '';
    
        if (empty($session['mobileSignup']) || empty($session['mobileSignupTimeout']) || $session['mobileSignupTimeout'] < time()) {
            $message = '对不起，请您重新注册。';
        } elseif (isset($session['mobileSignupNext']) && $session['mobileSignupNext'] > time()) {
            $message = '验证码请求过于频繁。';
        } else {
            if ($this->_sendMsg($session['mobileSignup'])) {
                Yii::info("用户注册发送语音验证码成功！手机号：{$session['mobileSignup']}");
                return [
                    'status' => 'success',
                    'data' => []
                ];
            } else {
                $message = '验证码请求失败，请稍后再试！';
                Yii::warning("用户注册请求语音验证码失败！手机号：{$session['mobileSignup']}，说明：" . Yii::$app->smser->message);
            }
        }
    
        return [
            'status' => 'fail',
            'data' => [
                'errors' => [$message]
            ]
        ];
    }
    
    protected function _sendMsg($mobile)
    {
        $session = Yii::$app->session;
        $session->open();
    
        $verifyCode = (string) mt_rand(100000, 999999);
    
        $sent = Yii::$app->smser->send($mobile, "亲爱的用户,您的验证码是{$verifyCode},请于10分钟内使用。如非本人操作,请忽略该短信。");
    
        if ($sent) {
            $session['mobileSignupNext'] = time() + 60;
            $session['mobileSignupVerifyCode'] = $verifyCode;
        }
    
        return $sent;
    }
    
    public function actionFeedback()
    {
        $model = new Feedback();
        
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getFirstErrors()
                ]
            ];
        }
    }
}