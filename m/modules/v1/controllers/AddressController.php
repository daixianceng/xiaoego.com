<?php

namespace m\modules\v1\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use common\models\Address;
use common\filters\auth\HeaderParamAuth;

class AddressController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HeaderParamAuth::className()
        ];
        return $behaviors;
    }
    
    public function verbs()
    {
        return [
            'all' => ['get'],
            'create' => ['post'],
            'update' => ['put', 'patch'],
            'delete' => ['delete']
        ];
    }
    
    public function actionAll($schoolId = null)
    {
        $sql = 'SELECT t0.id, t0.consignee, t0.cellphone, t0.gender, t0.school_id, t0.building_id, t0.room, t0.is_default, t1.name AS school_name, t2.name AS building_name FROM {{%address}} AS t0 LEFT JOIN {{%school}} AS t1 ON t0.school_id = t1.id LEFT JOIN {{%building}} AS t2 ON t0.building_id = t2.id WHERE t0.user_id=:uid ORDER BY t1.id, t0.is_default DESC';
        $params = [':uid' => Yii::$app->user->id];
        if ($schoolId !== null) {
            $sql .= ' AND t1.id=:sid';
            $params[':sid'] = $schoolId;
        }
        
        $list = Yii::$app->db->createCommand($sql, $params)->queryAll(\PDO::FETCH_ASSOC);
        
        return $list;
    }
    
    public function actionCreate()
    {
        $model = new Address();
        $model->user_id = Yii::$app->user->id;
        
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getFirstErrors()
                ]
            ];
        }
    }
    
    public function actionDetail($id)
    {
        $model = Address::findOne($id);
        
        if (!$model) {
            throw new BadRequestHttpException('未找到该收货地址！');
        }
        
        return $model;
    }
    
    public function actionUpdate($id)
    {
        $model = Address::findOne($id);
        
        if (!$model) {
            throw new BadRequestHttpException('未找到该收货地址！');
        }
        
        $model->user_id = Yii::$app->user->id;
        
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $model->getFirstErrors()
                ]
            ];
        }
    }
    
    public function actionDelete($id)
    {
        $model = Address::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        
        if ($model) {
            if (!$model->delete()) {
                return [
                    'status' => 'fail',
                    'data' => [
                        'message' => '收货地址删除失败！'
                    ]
                ];
            }
        }
        
        return [
            'status' => 'success',
            'data' => []
        ];
    }
}