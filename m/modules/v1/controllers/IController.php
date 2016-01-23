<?php

namespace m\modules\v1\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use common\filters\auth\HeaderParamAuth;
use frontend\models\VerifyPasswordForm;
use frontend\models\ChangePasswordForm;
use frontend\models\EmailBindRequestForm;
use frontend\models\BindEmailForm;
use frontend\models\ChangeMobileSendRequestForm;
use frontend\models\ChangeMobileForm;

class IController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HeaderParamAuth::className()
        ];
        return $behaviors;
    }
    
    protected function verbs()
    {
        return [
            'profile' => ['get'],
            'nickname' => ['put', 'patch'],
            'gender' => ['put', 'patch'],
            'mobile' => ['post', 'put', 'patch'],
            'verify-password' => ['post'],
            'password' => ['put', 'patch'],
            'email' => ['put', 'patch'],
            'remove-email' => ['delete']
        ];
    }
    
    public function actionProfile()
    {
        $identity = Yii::$app->user->identity;
        
        return [
            'status' => 'success',
            'data' => [
                'hasNewDown' => (bool) $identity->has_new_down,
                'mobile' => $identity->mobile,
                'nickname' => $identity->nickname,
                'gender' => $identity->gender,
                'email' => $identity->email
            ]
        ];
    }
    
    public function actionNickname()
    {
        $model = Yii::$app->user->identity;
        
        $model->nickname = Yii::$app->request->post('nickname');
        
        if ($model->save(true, ['nickname'])) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getErrors('nickname')
                ]
            ];
        }
    }
    
    public function actionGender()
    {
        $model = Yii::$app->user->identity;
    
        $model->gender = Yii::$app->request->post('gender');
        
        if ($model->save(true, ['gender'])) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getErrors('gender')
                ]
            ];
        }
    }
    
    /**
     * 更换手机号
     * 
     * @return array
     */
    public function actionMobile($step = '1')
    {
        if ($step === '1') {
            $model = new ChangeMobileSendRequestForm();
        
            if ($model->load(Yii::$app->request->post(), '') && $model->sendMsg()) {
                return [
                    'status' => 'success',
                    'data' => []
                ];
            } else {
                return [
                    'status' => 'fail',
                    'data' => [
                        'errors' => $model->getErrors('mobile') ?: [Yii::$app->smser->message]
                    ]
                ];
            }
        } elseif ($step === '2') {
            $model = new ChangeMobileForm();
            
            $model->mobile = Yii::$app->session['mobileChange'];// TODO
        
            if ($model->load(Yii::$app->request->post(), '') && $model->change()) {
                return [
                    'status' => 'success',
                    'data' => [
                        'mobile' => Yii::$app->user->identity->mobile
                    ]
                ];
            } else {
                return [
                    'status' => 'fail',
                    'data' => [
                        'errors' => $model->getErrors()
                    ]
                ];
            }
        } else {
            throw new BadRequestHttpException('参数错误！');
        }
    }
    
    /**
     * 验证密码
     *
     * @return array
     */
    public function actionVerifyPassword()
    {
        $model = new VerifyPasswordForm();
    
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getErrors('password')
                ]
            ];
        }
    }
    
    /**
     * 修改密码
     *
     * @return array
     */
    public function actionPassword()
    {
        $model = new ChangePasswordForm();
        
        if ($model->load(Yii::$app->request->post(), '') && $model->change()) {
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
    
    /**
     * 更新邮箱
     *
     * @return array
     */
    public function actionEmail($step = '1')
    {
        if ($step === '1') {
            $model = new EmailBindRequestForm();
            
            if ($model->load(Yii::$app->request->post(), '') && $model->sendEmail()) {
                return [
                    'status' => 'success',
                    'data' => []
                ];
            } else {
                return [
                    'status' => 'fail',
                    'data' => [
                        'errors' => $model->getErrors('email') ?: [Yii::$app->smser->message]
                    ]
                ];
            }
        } elseif ($step === '2') {
            $model = new BindEmailForm();
            
            if ($model->load(Yii::$app->request->post(), '') && $model->bind()) {
                return [
                    'status' => 'success',
                    'data' => [
                        'email' => Yii::$app->user->identity->email
                    ]
                ];
            } else {
                return [
                    'status' => 'fail',
                    'data' => [
                        'errors' => $model->getErrors('verifyCode')
                    ]
                ];
            }
        } else {
            throw new BadRequestHttpException('参数错误！');
        }
    }
    
    /**
     * 解除邮箱绑定
     *
     * @return array
     */
    public function actionRemoveEmail()
    {
        $model = Yii::$app->user->identity;
    
        $model->email = null;
    
        if ($model->save(true, ['email'])) {
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
    
    /**
     * 发送email
     *
     * @return array
     */
    public function actionSendEmail()
    {
        $model = new EmailBindRequestForm();
    
        $model->email = Yii::$app->session['emailBind'];
        if ($model->sendEmail()) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getErrors('email') ?: ['验证码发送失败，请稍后再试！']
                ]
            ];
        }
    }
    
    /**
     * 发送短信
     * 
     * @return array
     */
    public function actionSendMsg()
    {
        $model = new ChangeMobileSendRequestForm();
    
        $model->mobile = Yii::$app->session['mobileChange'];
    
        if ($model->sendMsg()) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getErrors('mobile') ?: [Yii::$app->smser->message]
                ]
            ];
        }
    }
}