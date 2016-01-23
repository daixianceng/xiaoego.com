<?php

namespace common\helpers;

use Yii;
use common\models\Store;

class Url extends \yii\helpers\Url
{
    public static function toCover($name)
    {
        $baseUrl = Yii::$app->params['imageBaseUrl'];
        
        return $baseUrl . '/cover/' . $name;
    }
    
    public static function toGoods($name)
    {
        $baseUrl = Yii::$app->params['imageBaseUrl'];
        
        return $baseUrl . '/goods/' . $name;
    }
}