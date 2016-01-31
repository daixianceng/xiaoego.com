<?php

namespace store\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use common\models\Purchase;
use common\models\Goods;
use store\models\CreateApplyForm;

/**
 * Purchase controller
 */
class PurchaseController extends Controller
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
                    'add' => ['post'],
                    'delete' => ['post'],
                    'count' => ['post']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $createApplyForm = new CreateApplyForm();
        
        if (Yii::$app->request->isPost && $createApplyForm->load(Yii::$app->request->post())) {
            try {
                if ($createApplyForm->create()) {
                    Yii::$app->session->setFlash('success', '采购申请已创建成功，请您耐心等待，申请结果将发送至您店铺中的手机号上。');
                    return $this->redirect(['/apply/view', 'id' => $createApplyForm->apply->id]);
                } else {
                    Yii::$app->session->setFlash('danger', '采购申请创建失败。');
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('danger', $e->getMessage());
            }
        }
        
        return $this->render('index', [
            'createApplyForm' => $createApplyForm
        ]);
    }
    
    public function actionAdd($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!Goods::find()->where(['id' => $id, 'store_id' => Yii::$app->user->identity->store_id, 'status' => [Goods::STATUS_NORMAL, Goods::STATUS_OFF_SHELVES]])->exists() ||
            Purchase::hasGoods($id)) {
            throw new BadRequestHttpException('拒绝操作。');
        }
        
        $model = new Purchase();
        $model->goods_id = $id;
        $model->store_id = Yii::$app->user->identity->store_id;
        $model->count = 1;
        
        if ($model->save(false)) {
            return [
                'status' => 'success',
                'data' => []
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => [
                    'message' => '加入失败。'
                ]
            ];
        }
    }
    
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Purchase::findOne(['goods_id' => $id, 'store_id' => Yii::$app->user->identity->store_id]);
        
        if ($model) {
            if (!$model->delete()) {
                return [
                    'status' => 'fail',
                    'data' => [
                        'message' => '删除失败。'
                    ]
                ];
            }
        }
        
        return [
            'status' => 'success',
            'data' => []
        ];
    }
    
    public function actionCount($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $count = (int) Yii::$app->request->post('value', 1);
        $model = Purchase::findOne(['id' => $id, 'store_id' => Yii::$app->user->identity->store_id]);
        
        if (!$model || $count < 1 || $count > 500) {
            throw new BadRequestHttpException('参数错误。');
        }
        
        $model->count = $count;
        
        if ($model->save(false)) {
            return ['status' => 'ok'];
        } else {
            return ['status' => 'no', 'msg' => '设置错误。'];
        }
    }
}