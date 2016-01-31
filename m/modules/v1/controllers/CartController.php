<?php

namespace m\modules\v1\controllers;

use Yii;
use common\filters\auth\HeaderParamAuth;
use common\models\Goods;
use common\models\CartGoods;

/**
 * Cart controller
 */
class CartController extends Controller
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
            'index' => ['get'],
            'add' => ['post'],
            'subtract' => ['post'],
            'clear' => ['put', 'patch'],
            'delete' => ['delete']
        ];
    }
    
    public function actionIndex($id)
    {
        $cartGoodsList = Yii::$app->user->identity->getCartGoods($id)->all();
        $list = [];
        
        foreach ($cartGoodsList as $goods) {
            $list[$goods->goods_id] = [
                'name' => $goods->goods->name,
                'cover' => $goods->goods->cover,
                'price' => $goods->goods->price,
                'priceOriginal' => $goods->goods->price_original,
                'status' => $goods->goods->status,
                'sales' => $goods->goods->sales,
                'surplus' => $goods->goods->surplus,
                'unit' => $goods->goods->unit,
                'count' => $goods->count,
                'isExpired' => $goods->isExpired
            ];
        }
        
        return [
            'goodsList' => $list,
            'length' => Yii::$app->user->identity->getCartGoodsRealLength($id),
            'volume' => Yii::$app->user->identity->getCartGoodsRealVolume($id)
        ];
    }
    
    public function actionAdd()
    {
        $goodsId = (int) Yii::$app->request->post('goodsId');
        
        $output = ['status' => 'fail', 'data' => []];
        
        $goods = Goods::findOne(['id' => $goodsId, 'status' => Goods::STATUS_NORMAL]);
        if (!$goods) {
            return $output;
        }
        
        $model = CartGoods::findOne(['goods_id' => $goodsId, 'user_id' => Yii::$app->user->id]);
        
        if (!$model) {
            if (Yii::$app->user->identity->getCartGoodsCount($goods->store_id) >= Yii::$app->params['goods.cartLimit']) {
                $output['data']['message'] = '您的购物车已爆满！最多只能容下' . Yii::$app->params['goods.cartLimit'] . '种商品。';
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
            'status' => 'success',
            'data' => [
                'name' => $goods->name,
                'price' => $goods->price,
                'surplus' => $goods->surplus,
                'cart' => $model->count
            ]
        ];
        
        return $output;
    }
    
    public function actionSubtract()
    {
        $goodsId = (int) Yii::$app->request->post('goodsId');
        
        $output = ['status' => 'fail', 'data' => []];
        
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
            'status' => 'success',
            'data' => [
                'name' => $goods->name,
                'price' => $goods->price,
                'surplus' => $goods->surplus,
                'cart' => $model ? $model->count : 0
            ]
        ];
        
        return $output;
    }
    
    public function actionClear()
    {
        $storeId = (int) Yii::$app->request->post('storeId');
        
        Yii::$app->user->identity->clearCartGoods($storeId);
        
        return ['status' => 'success', 'data' => []];
    }
    
    public function actionDelete($id)
    {
        $output = ['status' => 'fail', 'data' => []];
        
        $model = CartGoods::findOne(['goods_id' => $id, 'user_id' => Yii::$app->user->id]);
        
        if ($model) {
            if (!$model->delete()) {
                return $output;
            }
        }
        
        $output['status'] = 'success';
        
        return $output;
    }
}
