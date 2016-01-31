<?php

namespace store\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use common\models\Apply;
use common\models\ApplyLog;
use store\models\ApplySearch;

/**
 * Apply controller
 */
class ApplyController extends Controller
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
                    'complete' => ['post'],
                    'cancel' => ['post']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ApplySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Apply::findOne([
            'id' => $id,
            'store_id' => Yii::$app->user->identity->store_id,
            'status' => Apply::STATUS_REJECTED
        ]);
        
        if (!$model) {
            throw new BadRequestHttpException('参数错误。');
        }
        
        if (Yii::$app->request->isPost) {
            $countArr = Yii::$app->request->post('count');
            $fee = 0;
            $hasError = false;
            
            if (empty($countArr) || !is_array($countArr)) {
                $hasError = true;
                Yii::$app->session->setFlash('danger', '参数错误。');
            }
            if (!$hasError) {
                foreach ($model->goods as $key => $modelApplyGoods) {
                    $modelApplyGoods->count = $countArr[$key];
                
                    if ($modelApplyGoods->validate()) {
                        $fee = bcadd($fee, $modelApplyGoods->fee, 2);
                    } else {
                        $hasError = true;
                    }
                }
                
                if ($hasError) {
                    Yii::$app->session->setFlash('danger', '采购数量必须大于0且不超过500.');
                }
            }
            
            if (!$hasError) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->status = Apply::STATUS_PENDING;
                    $model->fee = $fee;
            
                    if (!$model->save(false)) {
                        throw new \Exception('保存申请失败！');
                    }
            
                    foreach ($model->goods as $modelApplyGoods) {
                        if (!$modelApplyGoods->save(false)) {
                            throw new \Exception('记录商品清单失败！');
                        }
                    }
            
                    $modelApplyLog = new ApplyLog();
                    $modelApplyLog->apply_id = $id;
                    $modelApplyLog->remark = '再次提交申请。';
            
                    if (!$modelApplyLog->save(false)) {
                        throw new \Exception('商品申请记录失败！');
                    }
            
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '采购申请提交成功，请您耐心等待，申请结果将发送至您店铺中的手机号上。');
                    return $this->redirect(['view', 'id' => $id]);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', $e->getMessage());
                }
            }
        }
        
        return $this->render('update', ['model' => $model]);
    }
    
    public function actionView($id)
    {
        $model = Apply::findOne(['id' => $id, 'store_id' => Yii::$app->user->identity->store_id]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该采购记录。');
        }
        
        return $this->render('view', ['model' => $model]);
    }
    
    public function actionComplete($id)
    {
        $model = Apply::findOne([
            'id' => $id,
            'status' => Apply::STATUS_PASSED,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
        
        if (!$model) {
            throw new BadRequestHttpException('参数错误！');
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->status = Apply::STATUS_COMPLETED;
        
            if (!$model->save(false)) {
                throw new \Exception('操作失败！');
            }
            
            foreach ($model->goods as $modelGoods) {
                if (!$modelGoods->goods->moveSurplus(+ $modelGoods->count, "商品采购，采购单号：{$model->apply_sn}。")) {
                    throw new \Exception('商品库存累加失败！');
                }
            }
        
            $modelApplyLog = new ApplyLog();
            $modelApplyLog->apply_id = $id;
            $modelApplyLog->remark = '店铺收货，商品库存自动累加，采购完成。';
        
            if (!$modelApplyLog->save(false)) {
                throw new \Exception('申请日志记录失败！');
            }
        
            $transaction->commit();
            Yii::$app->session->setFlash('success', '操作成功！');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', $e->getMessage());
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }
    
    public function actionCancel($id)
    {
        $model = Apply::findOne([
            'id' => $id,
            'status' => Apply::STATUS_REJECTED,
            'store_id' => Yii::$app->user->identity->store_id
        ]);
        
        if (!$model) {
            throw new BadRequestHttpException('参数错误！');
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->status = Apply::STATUS_CANCELLED;
        
            if (!$model->save(false)) {
                throw new \Exception('操作失败！');
            }
        
            $modelApplyLog = new ApplyLog();
            $modelApplyLog->apply_id = $id;
            $modelApplyLog->remark = '取消申请。';
        
            if (!$modelApplyLog->save(false)) {
                throw new \Exception('申请日志记录失败！');
            }
        
            $transaction->commit();
            Yii::$app->session->setFlash('success', '操作成功！');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', $e->getMessage());
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }
}