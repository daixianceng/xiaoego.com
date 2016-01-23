<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use common\models\Order;
use backend\models\OrderSearch;
use backend\models\CancelOrderForm;

/**
 * Order controller
 */
class OrderController extends Controller
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
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionView($id)
    {
        $model = Order::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该订单！');
        }
        
        return $this->render('view', [
            'model' => $model
        ]);
    }
    
    public function actionShip($id)
    {
        $model = Order::findOne($id);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该订单！');
        }
    
        if ($model->ship()) {
            Yii::$app->session->setFlash('success', '设置配送成功！');
        } else {
            Yii::$app->session->setFlash('danger', '配送请求失败！');
        }
    
        return $this->redirect(['view', 'id' => $id]);
    }
    
    public function actionCancel($id)
    {
        try {
            $cancelOrderForm = new CancelOrderForm($id);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    
        if ($cancelOrderForm->load(Yii::$app->request->post()) && $cancelOrderForm->cancel()) {
            Yii::$app->session->setFlash('success', '取消成功！');
        } else {
            Yii::$app->session->setFlash('danger', '取消失败！');
        }
    
        return $this->redirect(['view', 'id' => $id]);
    }
}