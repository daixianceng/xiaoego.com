<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\InvalidParamException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\models\Goods;
use common\models\GoodsImg;
use common\models\GoodsSurplus;
use backend\models\GoodsSearch;
use backend\models\MoveSurplusForm;

/**
 * Goods controller
 */
class GoodsController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'move-surplus' => ['post'],
                    'status' => ['post']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionAdd()
    {
        $model = new Goods();
        $model->setScenario('insert');
    
        if ($model->load(Yii::$app->request->post())) {
            
            $model->image = UploadedFile::getInstance($model, 'image');
            
            if ($model->validate()) {
            
                $model->cover = Yii::$app->security->generateRandomString(10) . '.' . $model->image->extension;
                if (floatval($model->price_original) < 0.01) {
                    $model->price_original = null;
                }
            
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save(false)) {
                        throw new \Exception('商品添加失败！');
                    }
                    
                    $filename = Yii::getAlias(Yii::$app->params['goods.coverPath']) . DIRECTORY_SEPARATOR . $model->cover;
                    if (!$model->image->saveAs($filename)) {
                        throw new \Exception('封面图片添加失败！');
                    }
                    
                    // 记录商品库存
                    $goodsSurplus = new GoodsSurplus();
                    $goodsSurplus->goods_id = $model->id;
                    $goodsSurplus->surplus_before = 0;
                    $goodsSurplus->amount = $model->surplus;
                    $goodsSurplus->surplus_after = $model->surplus;
                    $goodsSurplus->remark = '初始化库存。';
                    
                    if (!$goodsSurplus->save(false)) {
                        throw new \Exception('商品库存记录失败！');
                    }
                    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '成功添加商品“'.$model->name.'”。');
                    return $this->refresh();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', $e->getMessage());
                }
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Goods::findOne($id);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该商品。');
        }
    
        if ($model->load(Yii::$app->request->post())) {
        
            $model->image = UploadedFile::getInstance($model, 'image');
        
            if ($model->validate()) {
        
                if ($model->image !== null) {
                    $coverOriginal = $model->cover;
                    $model->cover = Yii::$app->security->generateRandomString(10) . '.' . $model->image->extension;
                }
                
                if (floatval($model->price_original) < 0.01) {
                    $model->price_original = null;
                }
        
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save(false)) {
                        throw new \Exception('商品更新失败！');
                    }
        
                    if ($model->image !== null) {
                        $filename = Yii::getAlias(Yii::$app->params['goods.coverPath']) . DIRECTORY_SEPARATOR . $model->cover;
                        if (!$model->image->saveAs($filename)) {
                            throw new \Exception('封面图片添加失败！');
                        }
                    }
        
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '成功更新商品“'.$model->name.'”。');
                    return $this->refresh();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', $e->getMessage());
                }
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Yii::$app->request->post('status');
        
        $model = Goods::findOne($id);
        
        if (!$model || !in_array($status, [Goods::STATUS_NORMAL, Goods::STATUS_OFF_SHELVES, Goods::STATUS_DELETED])) {
            throw new BadRequestHttpException('请求错误！');
        }
        
        $model->status = $status;
        
        if ($model->save(false)) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'message' => '更新出错！'
                ]
            ];
        }
    }
    
    public function actionImg($id)
    {
        /* @var $model Goods */
        $model = Goods::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该商品。');
        }
        
        $model->setScenario('updateImages');
        
        if (Yii::$app->request->isPost) {
            $model->photos = UploadedFile::getInstances($model, 'photos');
        
            if ($model->validate()) {
        
                $transaction = Yii::$app->db->beginTransaction();
        
                try {
        
                    $imgs = [];
        
                    foreach ($model->photos as $key => $image) {
                        $imgs[$key] = new GoodsImg();
                        $imgs[$key]->goods_id = $model->id;
                        $imgs[$key]->name = Yii::$app->security->generateRandomString(10) . '.' . $image->extension;
        
                        if (!$imgs[$key]->save(false)) {
                            throw new \Exception();
                        }
                    }
        
                    foreach ($model->photos as $key => $image) {
                        $image->saveAs(Yii::getAlias(Yii::$app->params['goods.imagePath']) . DIRECTORY_SEPARATOR . $imgs[$key]->name);
                    }
                    
                    $transaction->commit();
                    
                    Yii::$app->session->setFlash('success', '更新成功！');
                    return $this->refresh();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', '更新失败！');
                }
            }
        }
        
        return $this->render('form-img', [
            'model' => $model
        ]);
    }
    
    public function actionSurplus($id)
    {
        /* @var $model Goods */
        $model = Goods::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该商品。');
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => GoodsSurplus::find()->where(['goods_id' => $id]),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        
        return $this->render('surplus', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionMoveSurplus($id)
    {
        try {
            $moveSurplusForm = new MoveSurplusForm($id);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($moveSurplusForm->load(Yii::$app->request->post()) && $moveSurplusForm->move()) {
            Yii::$app->session->setFlash('success', '库存调整完成。');
        } else {
            Yii::$app->session->setFlash('danger', '库存调整失败。');
        }
        
        return $this->redirect(['surplus', 'id' => $id]);
    }
}