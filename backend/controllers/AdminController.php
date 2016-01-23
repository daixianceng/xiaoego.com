<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use common\models\Admin;
use backend\models\AdminSearch;

/**
 * Admin controller
 */
class AdminController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionAdd()
    {
        $model = new Admin();
        $model->setScenario('insert');
        $model->load(Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->isPost && $model->validate()) {
            
            $model->setPassword($model->password);
            $model->generateAuthKey();
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', '成功添加后台用户“'.$model->username.'”。');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', '添加后台用户失败。');
            }
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Admin::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该后台用户。');
        }
        
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
                Yii::$app->session->setFlash('success', '成功更新后台用户“'.$model->username.'”。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '更新后台用户失败。');
            }
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionProfile()
    {
        return $this->runAction('update', ['id' => Yii::$app->user->id]);
    }
    
    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Yii::$app->request->post('status');
    
        $model = Admin::findOne($id);
    
        if (!$model || !in_array($status, [Admin::STATUS_ACTIVE, Admin::STATUS_BLOCKED])) {
            throw new BadRequestHttpException('请求错误！');
        }
    
        $model->status = $status;
    
        if ($model->save(false)) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'message' => '更新出错！'
                ]
            ];
        }
    }
}