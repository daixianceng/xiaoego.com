<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use common\models\Address;
use common\models\Building;

/**
 * Address controller
 */
class AddressController extends Controller
{
    public $layout = 'column2-1';
    
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
                    ]
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Yii::$app->user->identity->getAddress(),
            'pagination' => [
                'pageSize' => 0,
            ]
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionAdd()
    {
        $model = new Address();
        $model->load(Yii::$app->request->post());
        $model->user_id = Yii::$app->user->id;
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->isPost && $model->save()) {
            Yii::$app->session->setFlash('success', '您已成功添加收货人是 '.$model->consignee.' 的收货地址。');
            return $this->redirect(['index']);
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Address::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到收货地址！');
        }
        
        $model->load(Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->isPost && $model->save()) {
            Yii::$app->session->setFlash('success', '您已成功更新收货人是 '.$model->consignee.' 的收货地址。');
            return $this->redirect(['index']);
        }
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionDelete($id)
    {
        $model = Address::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        
        if ($model && $model->delete()) {
            Yii::$app->session->setFlash('success', '已成功删除收货人是 '.$model->consignee.' 的收货地址。');
        }
        
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionDefault($id)
    {
        $model = Address::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到收货地址！');
        }
        
        $model->is_default = Address::BOOL_TRUE;
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', '设置默认地址成功。');
        } else {
            Yii::$app->session->setFlash('danger', '设置默认地址失败。');
        }
    
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionBuildings($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $output = ['status' => 'ok', 'html' => ''];
        
        $list = Building::getKeyValuePairs($id);
        
        foreach ($list as $key => $name) {
            $output['html'] .= "<option value=\"$key\">$name</option>";
        }
        
        return $output;
    }
}