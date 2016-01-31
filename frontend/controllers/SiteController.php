<?php

namespace frontend\controllers;

use Yii;
use frontend\models\LoginForm;
use frontend\models\SignupForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\PasswordResetMobileRequestForm;
use frontend\models\PasswordResetVerifyForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupMobileVerifyForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $layout = 'column2';
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'send-msg' => ['post']
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'width' => 100,
                'height' => 34,
                'padding' => 1,
                'offset' => 1,
                'backColor' => 0xFFFFFF,
                'foreColor' => 0xE50A4A,
                'minLength' => 4,
                'maxLength' => 4
            ],
            'password-reset-captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'width' => 100,
                'height' => 34,
                'padding' => 1,
                'offset' => 1,
                'backColor' => 0xFFFFFF,
                'foreColor' => 0xE50A4A,
                'minLength' => 4,
                'maxLength' => 4
            ],
        ];
    }

    public function actionIndex()
    {
        if ($store = Yii::$app->params['storeModel']) {
            return $this->redirect(Url::to(['/store/index', 'id' => $store->id]));
        }
        if ($school = Yii::$app->params['schoolModel']) {
            return $this->redirect(['/school/index', 'id' => $school->id]);
        }
        
        $this->layout = 'base';
        
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $model->load(Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->isPost && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
    
    public function actionJoinus()
    {
        return $this->render('joinus');
    }
    
    public function actionHelp()
    {
        return $this->render('help');
    }
    
    public function actionPage($slug)
    {
        $this->layout = 'base';
        
        return $this->render('page/' . $slug);
    }

    public function actionSignup($step = '1')
    {
        if ($step !== '2') {
            $model = new SignupForm();
            $model->load(Yii::$app->request->post());
            
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            
            if (Yii::$app->request->isPost && $model->validate()) {
                $model->writeSession();
                if ($this->_sendMsg($model->mobile)) {
                    Yii::info("用户注册发送短信验证码成功！手机号：{$model->mobile}");
                    Yii::$app->session->setFlash('sentSuccess');
                } else {
                    Yii::warning("用户注册发送短信验证码失败！手机号：{$model->mobile}，说明：" . Yii::$app->smser->message);
                    Yii::$app->session->setFlash('failedToSend', '验证码发送失败，请您再试一次！');
                }
                return $this->redirect(['signup', 'step' => '2']);
            }
            
            $params = ['model' => $model, 'step' => $step];
        } else {
            $session = Yii::$app->session;
            $session->open();
            
            if (empty($session['mobileSignup']) || empty($session['mobileSignupTimeout']) || $session['mobileSignupTimeout'] < time()) {
                $session->setFlash('resignup', '对不起，请您重新注册。');
                return $this->redirect(['signup']);
            }
            
            $signupMobileVerifyForm = new SignupMobileVerifyForm();
            $signupMobileVerifyForm->load(Yii::$app->request->post());
            
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($signupMobileVerifyForm);
            }
            
            if (Yii::$app->request->isPost && $signupMobileVerifyForm->validate()) {
                if ($user = $signupMobileVerifyForm->signup()) {
                    $signupMobileVerifyForm->clearSession();
                    if (Yii::$app->user->login($user)) {
                        return $this->goHome();
                    }
                }
            }
            
            $params = ['signupMobileVerifyForm' => $signupMobileVerifyForm, 'step' => $step];
        }
        
        return $this->render('signup', $params);
    }
    
    public function actionSendMsg()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
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
                return ['status' => 'ok'];
            } else {
                $message = '验证码请求失败，请稍后再试！';
                Yii::warning("用户注册请求语音验证码失败！手机号：{$session['mobileSignup']}，说明：" . Yii::$app->smser->message);
            }
        }
        
        return ['status' => 'no', 'message' => $message];
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

    public function actionRequestPasswordReset($type = 'sms', $step = '1')
    {
        if ($type === 'sms') {
            
            $session = Yii::$app->session;
            $session->open();
            
            if ($step !== '2') {
                $model = new PasswordResetMobileRequestForm();
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    if ($model->sendMsg()) {
                        $session['passwordResetTimeout'] = time() + 900; // 15 minutes
                        $session['passwordResetMobile'] = $model->mobile;
                        
                        return $this->redirect(['request-password-reset', 'type' => $type, 'step' => '2']);
                    } else {
                        $session->setFlash('smsFailure', '对不起，验证码发送失败。');
                    }
                }
            } elseif (isset($session['passwordResetTimeout']) && $session['passwordResetTimeout'] >= time()) {
                $model = new PasswordResetVerifyForm();
                $model->load(Yii::$app->request->post());
                
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                
                if (Yii::$app->request->isPost && $model->validate()) {
                    
                    if ($model->generateToken()) {
                        $model->clearSession();
                        return $this->redirect(['reset-password', 'token' => $model->user->password_reset_token]);
                    } else {
                        $session->setFlash('resetErr', '操作失败，请稍后再试！');
                    }
                }
            } else {
                $session->setFlash('resetAgain', '对不起，请您重新开始一次。');
                return $this->redirect(['request-password-reset', 'type' => $type]);
            }
            
        } elseif ($type === 'email') {
            $model = new PasswordResetRequestForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->sendEmail()) {
                    Yii::$app->getSession()->setFlash('emailSent', '我们已经为您发送了一条链接，请进入邮箱查看。');
            
                    return $this->refresh();
                } else {
                    Yii::$app->getSession()->setFlash('emailFailure', '对不起，我们无法对您的账户进行重置密码操作。');
                }
            }
        } else {
            throw new BadRequestHttpException('参数错误！');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
            'type' => $type,
            'step' => $step
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', '新密码设置成功！');

            return $this->redirect(['/site/login']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
