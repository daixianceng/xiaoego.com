<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\models\Goods;
use common\helpers\Url;

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'detail' => ['post']
                ],
            ],
        ];
    }
    
    public function actionDetail($id)
    {
        $model = Goods::findOne(['id' => $id, 'status' => Goods::STATUS_NORMAL]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该商品');
        }
        
        $output = [
            'status' => 'ok',
            'name' => $model->name,
            'price' => $model->price,
            'surplus' => $model->surplus,
            'description' => '【笑e购】' . $model->description,
            'cart' => Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->getCartGoodsQuantity($id),
            'image' => $model->images ? Url::toGoods($model->images[0]->name) : Yii::$app->params['goods.defaultImageUrl']
        ];
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $output;
    }
}
