<?php

namespace store\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Member controller
 */
class MemberController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionProfile()
    {
        $model = Yii::$app->user->identity;
        
        $model->load(Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->isPost && $model->validate()) {
            
            if (!empty($model->password)) {
                $model->setPassword($model->password);
            }
            
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', '您的资料修改成功。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '您的资料修改失败！');
            }
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
}