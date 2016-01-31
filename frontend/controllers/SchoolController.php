<?php

namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Cookie;
use common\models\School;

/**
 * School controller
 */
class SchoolController extends Controller
{
    public $layout = 'column1';

    public function actionIndex($id)
    {
        $model = School::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该学校！');
        }
        
        $cookieSchool = new Cookie([
            'name' => 'schoolId',
            'value' => $id,
            'expire' => time() + 86400 * 30,
        ]);
        
        Yii::$app->response->cookies->add($cookieSchool);
        
        Yii::$app->params['schoolModel'] = $model;
        
        return $this->render('index', [
            'model' => $model
        ]);
    }
}
