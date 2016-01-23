<?php

namespace store\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\models\Goods;
use common\models\GoodsSurplus;
use store\models\GoodsSearch;

/**
 * Goods controller
 */
class GoodsController extends Controller
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
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionUpdate($id)
    {
        /* @var $model Goods */
        $model = Goods::findOne([
            'id' => $id,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该商品。');
        }
    
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (floatval($model->price_original) < 0.01) {
                $model->price_original = null;
            }
            
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', '成功更新商品“'.$model->name.'”。');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', $e->getMessage());
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Yii::$app->request->post('status');
        
        $model = Goods::findOne([
            'id' => $id,
            'status' => [Goods::STATUS_NORMAL, Goods::STATUS_OFF_SHELVES]
        ]);
        
        if (!$model || !in_array($status, [Goods::STATUS_NORMAL, Goods::STATUS_OFF_SHELVES, Goods::STATUS_DELETED])) {
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
    
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Goods::findOne(['id' => $id, 'store_id' => Yii::$app->user->identity->store_id]);
    
        if ($model) {
            $model->status = Goods::STATUS_DELETED;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', '删除成功！');
            } else {
                Yii::$app->session->setFlash('danger', '删除错误！');
            }
        }
    
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionSurplus($id)
    {
        /* @var $model Goods */
        $model = Goods::findOne([
            'id' => $id, 
            'store_id' => Yii::$app->user->identity->store_id
        ]);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该商品。');
        }
    
        $dataProvider = new ActiveDataProvider([
            'query' => GoodsSurplus::find()->where(['goods_id' => $id]),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
    
        return $this->render('surplus', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
}