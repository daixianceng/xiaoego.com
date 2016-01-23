<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\models\School;
use backend\models\SchoolSearch;

/**
 * School controller
 */
class SchoolController extends Controller
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
        $searchModel = new SchoolSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionAdd()
    {
        $model = new School();
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功添加学校“'.$model->name.'”。');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', '添加学校失败。');
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = School::findOne($id);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该学校。');
        }
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功更新学校“'.$model->name.'”。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '更新学校失败。');
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionNameFilter($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $output = ['results' => ['id' => '', 'text' => '']];
    
        if (!is_null($q)) {
            $sql = 'SELECT id, name AS text FROM ' . School::tableName() . ' WHERE name LIKE :like ORDER BY name ASC LIMIT 25';
            $output['results'] = Yii::$app->db->createCommand($sql, [':like' => "%$q%"])->queryAll();
        } elseif ($id > 0 && ($school = School::findOne($id))) {
            $output['results'] = ['id' => $id, 'text' => $school->name];
        }
    
        return $output;
    }
}