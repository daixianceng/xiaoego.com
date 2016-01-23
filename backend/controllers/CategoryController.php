<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Category;
use backend\models\CategorySearch;
use himiklab\sortablegrid\SortableGridAction;

/**
 * Category controller
 */
class CategoryController extends Controller
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
    
    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => Category::className(),
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionAdd()
    {
        $model = new Category();
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功添加分类“'.$model->name.'”。');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', '分类添加失败。');
            }
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Category::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该分类。');
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功更新分类“'.$model->name.'”。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '分类更新失败。');
            }
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
}