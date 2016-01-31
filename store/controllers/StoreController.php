<?php

namespace store\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\Store;

/**
 * Store controller
 */
class StoreController extends Controller
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
    
    public function actionUpdate()
    {
        $model = Yii::$app->user->identity->store;
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功更新营业点');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '更新营业点失败。');
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionActive()
    {
        $model = Yii::$app->user->identity->store;
        
        if ($model->status !== Store::STATUS_DISABLED) {
            $model->status = Store::STATUS_ACTIVE;
            
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', '您的店铺已设置为营业状态。');
            } else {
                Yii::$app->session->setFlash('danger', '您的店铺设置失败！');
            }
        }
        
        return $this->redirect(['site/index']);
    }
    
    public function actionRest()
    {
        $model = Yii::$app->user->identity->store;
    
        if ($model->status !== Store::STATUS_DISABLED) {
            $model->status = Store::STATUS_REST;
    
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', '您的店铺已设置为休息状态。');
            } else {
                Yii::$app->session->setFlash('danger', '您的店铺设置失败！');
            }
        }
    
        return $this->redirect(['site/index']);
    }
}