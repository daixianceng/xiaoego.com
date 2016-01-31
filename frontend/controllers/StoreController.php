<?php

namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\Cookie;
use yii\filters\VerbFilter;
use common\models\Store;
use common\models\Category;

/**
 * Store controller
 */
class StoreController extends Controller
{
    public $layout = 'column1';


    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'load-goods' => ['post']
                ],
            ],
        ];
    }
    
    /**
     * 店铺
     * 
     * @param integer $id
     * @param string $category
     * @throws NotFoundHttpException
     * @return string
     */
    public function actionIndex($id, $category = 'all', $q = '')
    {
        /* @var $model Store */
        $model = Store::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该营业点！');
        }
        if ($model->status === Store::STATUS_DISABLED) {
            throw new BadRequestHttpException('该店铺已被禁用，请重新选择！');
        }
        
        $modelCate = $category === 'all' ? null : Category::findOne(['slug' => $category]);
        
        $this->_remember($model);
        
        return $this->render('index', [
            'model' => $model,
            'modelCate' => $modelCate,
            'q' => $q
        ]);
    }
    
    protected function _remember(Store $model)
    {
        $expire = time() + 86400 * 30;
        $cookieStore = new Cookie([
            'name' => 'storeId',
            'value' => $model->id,
            'expire' => $expire,
        ]);
        $cookieSchool = new Cookie([
            'name' => 'schoolId',
            'value' => $model->school_id,
            'expire' => $expire,
        ]);
        
        Yii::$app->response->cookies->add($cookieStore);
        Yii::$app->response->cookies->add($cookieSchool);
        
        Yii::$app->params['schoolModel'] = $model->school;
        Yii::$app->params['storeModel'] = $model;
    }
    
    public function actionLoadGoods($id, $category = 'all', $q = '')
    {
        $offset = (int) Yii::$app->request->post('offset');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        /* @var $model Store */
        $model = Store::findOne(['id' => $id, 'status' => [Store::STATUS_ACTIVE, Store::STATUS_REST]]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该营业点！');
        }
        
        if ($q !== '') {
            $query = $model->getGoods()->andWhere(['or', ['like', 'name', $q], ['like', 'description', $q]]);
        } else {
            $modelCate = $category === 'all' ? null : Category::findOne(['slug' => $category]);
            
            if ($modelCate) {
                $query = $model->getGoods($modelCate->id);
            } else {
                $query = $model->getGoods();
            }
        }
        
        $limit = 8;
        $goodsList = $query->offset($offset)->limit($limit)->all();
        $output = [
            'status' => 'ok',
            'html' => '',
            'length' => count($goodsList)
        ];
        
        $output['end'] = $output['length'] < $limit;
        
        foreach ($goodsList as $goods) {
            $output['html'] .= $this->renderPartial('_item', ['goods' => $goods, 'lazy' => false]);
        }
        
        return $output;
    }
}
