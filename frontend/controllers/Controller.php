<?php

namespace frontend\controllers;

use Yii;
use common\models\School;
use common\models\Store;

/**
 * Controller
 */
class Controller extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $schoolId = Yii::$app->request->cookies['schoolId'];
        $storeId = Yii::$app->request->cookies['storeId'];
        
        if ($schoolId) {
            Yii::$app->params['schoolModel'] = School::findOne($schoolId);
        }
        if ($storeId) {
            Yii::$app->params['storeModel'] = Store::findOne($storeId);
        }
    }
}
