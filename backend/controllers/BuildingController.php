<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Building;
use backend\models\BuildingSearch;

/**
 * Building controller
 */
class BuildingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new BuildingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionAdd()
    {
        $model = new Building();
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功添加学校建筑“'.$model->name.'”。');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', '添加学校建筑失败。');
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Building::findOne($id);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该学校建筑。');
        }
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功更新学校建筑“'.$model->name.'”。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '更新学校建筑失败。');
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
}