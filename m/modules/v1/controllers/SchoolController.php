<?php

namespace m\modules\v1\controllers;

use yii\web\BadRequestHttpException;
use common\models\School;
use common\models\Building;

class SchoolController extends Controller
{
    public function verbs()
    {
        return [
            'all' => ['get'],
            'detail' => ['get'],
            'buildings' => ['get']
        ];
    }
    
    public function actionAll()
    {
        return School::getKeyValuePairs();
    }
    
    public function actionDetail($id)
    {
        $model = School::findOne($id);
        
        if (!$model) {
            throw new BadRequestHttpException('未找到该学校！');
        }
        
        $data['name'] = $model->name;
        $data['stores'] = $model->getStores()->select(['id', 'name', 'status', 'address'])->all();
        $data['stores'] = array_chunk($data['stores'], 2);
        
        return $data;
    }
    
    public function actionBuildings($id)
    {
        return Building::getKeyValuePairs($id);
    }
}