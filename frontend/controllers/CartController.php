<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Goods;
use common\models\CartGoods;

/**
 * Cart controller
 */
class CartController extends Controller
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
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'add' => ['post'],
                    'subtract' => ['post'],
                    'refresh' => ['post'],
                    'clear' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionAdd()
    {
        $goodsId = (int) Yii::$app->request->post('goodsId');
        
        $output = ['status' => 'no'];
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $goods = Goods::findOne(['id' => $goodsId, 'status' => Goods::STATUS_NORMAL]);
        if (!$goods) {
            return $output;
        }
        
        $model = CartGoods::findOne(['goods_id' => $goodsId, 'user_id' => Yii::$app->user->id]);
        
        if (!$model) {
            if (Yii::$app->user->identity->getCartGoodsCount($goods->store_id) >= Yii::$app->params['goods.cartLimit']) {
                $output['msg'] = '您的购物车已爆满！最多只能容下' . Yii::$app->params['goods.cartLimit'] . '种商品。';
                return $output;
            }
            
            $model = new CartGoods();
            $model->user_id = Yii::$app->user->id;
            $model->goods_id = $goodsId;
            $model->store_id = $goods->store_id;
            $model->price = $goods->price;
            $model->count = 0;
        }
        
        $model->count ++;
        
        if ($model->count > $goods->surplus) {
            $model->count = $goods->surplus;
        }
        
        if ($model->count > 0 && !$model->save(false)) {
            return $output;
        }
        
        $output = [
            'status' => 'ok', 
            'name' => $goods->name,
            'price' => $goods->price,
            'surplus' => $goods->surplus,
            'cart' => $model->count
        ];
        
        return $output;
    }
    
    public function actionSubtract()
    {
        $goodsId = (int) Yii::$app->request->post('goodsId');
        
        $output = ['status' => 'no'];
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $goods = Goods::findOne(['id' => $goodsId, 'status' => Goods::STATUS_NORMAL]);
        if (!$goods) {
            return $output;
        }
        
        $model = CartGoods::findOne(['goods_id' => $goodsId, 'user_id' => Yii::$app->user->id]);
        
        if ($model) {
            $model->count --;
            
            if ($model->count < 1) {
                $model->delete();
            } else {
                if ($model->count > $goods->surplus) {
                    $model->count = $goods->surplus;
                }
                if (!$model->save(false)) {
                    return $output;
                }
            }
        }
        
        $output = [
            'status' => 'ok',
            'name' => $goods->name,
            'price' => $goods->price,
            'surplus' => $goods->surplus,
            'cart' => $model ? $model->count : 0
        ];
        
        return $output;
    }
    
    public function actionRefresh()
    {
        $storeId = (int) Yii::$app->request->post('storeId');
        
        $output = [
            'status' => 'ok',
            'html' => $this->renderPartial('refresh', ['storeId' => $storeId]),
            'length' => Yii::$app->user->identity->getCartGoodsRealLength($storeId),
            'volume' => Yii::$app->user->identity->getCartGoodsRealVolume($storeId)
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $output;
    }
    
    public function actionClear()
    {
        $storeId = (int) Yii::$app->request->post('storeId');
        
        $output = ['status' => 'ok'];
        Yii::$app->user->identity->clearCartGoods($storeId);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $output;
    }
    
    public function actionDelete()
    {
        $goodsId = (int) Yii::$app->request->post('goodsId');
        
        $output = ['status' => 'no'];
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = CartGoods::findOne(['goods_id' => $goodsId, 'user_id' => Yii::$app->user->id]);
        
        if ($model) {
            if (!$model->delete()) {
                return $output;
            }
        }
        
        $output['status'] = 'ok';
        
        return $output;
    }
}
