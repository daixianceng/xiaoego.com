<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use common\models\Bill;
use frontend\models\VerifyPasswordForm;
use frontend\models\ChangeMobileForm;
use frontend\models\ChangeMobileSendRequestForm;
use frontend\models\ChangePasswordForm;
use frontend\models\BindEmailForm;
use frontend\models\EmailBindRequestForm;

class IController extends Controller
{
    public $layout = 'column2-1';
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'send-email' => ['post'],
                    'send-msg' => ['post'],
                    'remove-email' => ['post']
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        return $this->redirect(['profile']);
    }
    
    public function actionProfile()
    {
        $model = Yii::$app->user->identity;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '您的资料更新成功！');
            return $this->refresh();
        }
        
        return $this->render('profile', [
            'model' => $model
        ]);
    }
    
    public function actionEmail($step = '1')
    {
        $params = ['step' => $step];
        
        if ($step === '1') {
            $emailBindRequestForm = new EmailBindRequestForm();
            $emailBindRequestForm->load(Yii::$app->request->post());
            
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($emailBindRequestForm);
            }
            
            if (Yii::$app->request->isPost && $emailBindRequestForm->sendEmail()) {
        
                Yii::$app->session->setFlash('success', '验证码已发送，请至邮箱查看。');
                return $this->redirect(['email', 'step' => '2']);
            }

            $params['emailBindRequestForm'] = $emailBindRequestForm;
        } elseif ($step === '2') {
            $bindEmailForm = new BindEmailForm();
            $bindEmailForm->load(Yii::$app->request->post());
        
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($bindEmailForm);
            }
        
            if (Yii::$app->request->isPost && $bindEmailForm->bind()) {
                Yii::$app->session->setFlash('success', '邮箱绑定成功！');
                return $this->redirect(['email']);
            }
        
            $params['bindEmailForm'] = $bindEmailForm;
        } else {
            return $this->redirect(['email']);
        }
        
        return $this->render('email', $params);
    }
    
    public function actionMobile($step = '1')
    {
        $params = ['step' => $step];
        
        if ($step === '1') {
            $verifyPasswordForm = new VerifyPasswordForm();
        
            if ($verifyPasswordForm->load(Yii::$app->request->post()) && $verifyPasswordForm->validate()) {
                Yii::$app->session['passwordVerified'] = true;
                return $this->redirect(['mobile', 'step' => '2']);
            }
            
            $params['verifyPasswordForm'] = $verifyPasswordForm;
        } elseif ($step === '2' && Yii::$app->session->has('passwordVerified') && Yii::$app->session['passwordVerified']) {
            $changeMobileForm = new ChangeMobileForm();
            $changeMobileForm->load(Yii::$app->request->post());
        
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($changeMobileForm);
            }
        
            if (Yii::$app->request->isPost && $changeMobileForm->change()) {
                Yii::$app->session->setFlash('success', '手机更换成功！');
                return $this->redirect(['mobile']);
            }
        
            $params['changeMobileForm'] = $changeMobileForm;
        } else {
            return $this->redirect(['mobile']);
        }
        
        return $this->render('mobile', $params);
    }
    
    public function actionPassword($step = '1')
    {
        $params = ['step' => $step];
        
        if ($step === '1') {
            $verifyPasswordForm = new VerifyPasswordForm();
        
            if ($verifyPasswordForm->load(Yii::$app->request->post()) && $verifyPasswordForm->validate()) {
                Yii::$app->session['passwordVerified'] = true;
                return $this->redirect(['password', 'step' => '2']);
            }
        
            $params['verifyPasswordForm'] = $verifyPasswordForm;
        } elseif ($step === '2' && Yii::$app->session->has('passwordVerified') && Yii::$app->session['passwordVerified']) {
            $changePasswordForm = new ChangePasswordForm();
            $changePasswordForm->load(Yii::$app->request->post());
        
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($changePasswordForm);
            }
            
            if (Yii::$app->request->isPost && $changePasswordForm->change()) {
                Yii::$app->session->setFlash('success', '密码更改成功！');
                return $this->redirect(['password']);
            }
        
            $params['changePasswordForm'] = $changePasswordForm;
        } else {
            return $this->redirect(['password']);
        }
        
        return $this->render('password', $params);
    }
    
    public function actionSendEmail()
    {
        $emailBindRequestForm = new EmailBindRequestForm();
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $emailBindRequestForm->email = Yii::$app->session['emailBind'];
        if ($emailBindRequestForm->validate() && $emailBindRequestForm->sendEmail()) {
            return ['status' => 'ok'];
        }
        
        return ['status' => 'err', 'msg' => $emailBindRequestForm->getFirstError('email')];
    }
    
    public function actionSendMsg()
    {
        $model = new ChangeMobileSendRequestForm();
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model->mobile = Yii::$app->request->post('mobile');
        $message = '';
        
        if ($model->sendMsg()) {
            return ['status' => 'ok'];
        } else {
            $message = $model->getFirstError('mobile') or $message = '验证码发送失败，请稍后再试！';
        }
        
        return ['status' => 'err', 'message' => $message];
    }
    
    public function actionRemoveEmail()
    {
        $user = Yii::$app->user->identity;
        $user->email = null;
        
        if ($user->save(false)) {
            Yii::$app->session->setFlash('success', '邮箱已成功解除绑定。');
        }
        
        return $this->redirect(['email']);
    }
}