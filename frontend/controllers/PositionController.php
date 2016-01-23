<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use common\models\PositionProvince as Province;
use common\models\PositionCity as City;
use common\models\PositionCounty as County;

class PositionController extends Controller
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
                    'province' => ['post'],
                    'city' => ['post'],
                    'county' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionProvince()
    {
        $list = Province::getKeyValuePairs();
        
        $output = ['status' => 'ok', 'html' => ''];
        foreach ($list as $key => $value) {
            $output['html'] .= '<option value="' . $key . '">' . $value . '</option>';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $output;
    }
    
    public function actionCity($provinceId)
    {
        $list = City::getKeyValuePairsByProvinceId($provinceId);
    
        $output = ['status' => 'ok', 'html' => ''];
        foreach ($list as $key => $value) {
            $output['html'] .= '<option value="' . $key . '">' . $value . '</option>';
        }
    
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        return $output;
    }
    
    public function actionCounty($cityId)
    {
        $list = County::getKeyValuePairsByCityId($cityId);
    
        $output = ['status' => 'ok', 'html' => ''];
        foreach ($list as $key => $value) {
            $output['html'] .= '<option value="' . $key . '">' . $value . '</option>';
        }
    
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        return $output;
    }
}