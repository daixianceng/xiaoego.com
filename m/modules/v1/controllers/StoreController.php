<?php

namespace m\modules\v1\controllers;

use yii\web\BadRequestHttpException;
use common\models\Store;
use common\models\Category;

class StoreController extends Controller
{
    public function verbs()
    {
        return [
            'detail' => ['get']
        ];
    }
    
    public function actionDetail($id)
    {
        /* @var $model Store */
        $model = Store::findOne($id);
        
        if (!$model) {
            throw new BadRequestHttpException('未找到该营业点！');
        }
        if ($model->status === Store::STATUS_DISABLED) {
            throw new BadRequestHttpException('该店铺已被禁用，请重新选择！');
        }
        
        $data['store'] = $model->toArray(['id', 'name', 'school_id', 'address', 'cellphone', 'telephone', 'notice', 'status', 'hours', 'has_book', 'has_down', 'has_gift', 'has_least', 'down_upper', 'down_val', 'gift_upper', 'gift_val', 'least_val', 'created_at']);
        $data['store']['downMsg'] = $model->downMsg;
        $data['store']['giftMsg'] = $model->giftMsg;
        $data['categories'] = Category::getKeyValuePairs();
        
        foreach ($data['categories'] as $key => $name) {
            $goodsList = $model->getGoods($key)->all();
            if ($goodsList) {
                foreach ($goodsList as $goods) {
                    $goodsArr = $goods->toArray();
                    $data['goodsList'][$key][] = $goodsArr;
                }
            } else {
                $data['goodsList'][$key] = [];
            }
        }
        
        return $data;
    }
}