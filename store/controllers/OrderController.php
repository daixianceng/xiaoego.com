<?php

namespace store\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\InvalidParamException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use common\models\Order;
use common\models\User;
use store\models\OrderSearch;
use store\models\CancelOrderForm;

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'ship' => ['post'],
                    'cancel' => ['post'],
                    'count' => ['post']
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
        $model = Order::findOne([
            'id' => $id,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该订单');
        }
        
        return $this->render('view', [
            'model' => $model
        ]);
    }
    
    public function actionShip($id)
    {
        $model = Order::findOne([
            'id' => $id,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
    
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
    
    public function actionUserMobileFilter($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $output = ['results' => ['id' => '', 'text' => '']];
        
        if (!is_null($q)) {
            $sql = 'SELECT id, mobile AS text FROM ' . User::tableName() . ' WHERE mobile LIKE :like ORDER BY mobile ASC LIMIT 25';
            $output['results'] = Yii::$app->db->createCommand($sql, [':like' => "%$q%"])->queryAll();
        } elseif ($id > 0 && ($user = User::findOne($id))) {
            $output['results'] = ['id' => $id, 'text' => $user->mobile];
        }
        
        return $output;
    }
    
    public function actionCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        $count = Order::getCountByStoreId(Yii::$app->user->identity->store_id);
    
        return ['count' => $count];
    }
}