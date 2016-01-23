<?php

namespace store\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Tag;
use store\models\TagSearch;
use himiklab\sortablegrid\SortableGridAction;

/**
 * Tag controller
 */
class TagController extends Controller
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
                'modelName' => Tag::className(),
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionAdd()
    {
        $model = new Tag();
        
        if ($model->load(Yii::$app->request->post())) {
            $model->store_id = Yii::$app->user->identity->store_id;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功添加标签“'.$model->name.'”。');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', '标签添加失败。');
            }
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Tag::findOne([
            'id' => $id,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该标签。');
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功更新标签“'.$model->name.'”。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '标签添加失败。');
            }
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionDelete($id)
    {
        $model = Tag::findOne([
            'id' => $id,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
        
        if ($model) {
            if ($model->delete()) {
                Yii::$app->session->setFlash('success', '成功删除标签“'.$model->name.'”。');
            } else {
                Yii::$app->session->setFlash('danger', '标签删除失败。');
            }
        }
        
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }
}