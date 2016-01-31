<?php

namespace m\modules\v1\controllers;

use common\models\GoodsImg;

class GoodsController extends Controller
{
    public function verbs()
    {
        return [
            'images' => ['get']
        ];
    }
    
    public function actionImages($id)
    {
        return GoodsImg::findByGoodsId($id);
    }
}