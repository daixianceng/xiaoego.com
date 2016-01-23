<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use common\models\Apply;
use common\models\ApplyLog;
use backend\models\ApplySearch;
use backend\models\RejectApplyForm;

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
                    'reject' => ['post'],
                    'pass' => ['post'],
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
    
    public function actionView($id)
    {
        $model = Apply::findOne($id);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该采购记录。');
        }
    
        return $this->render('view', ['model' => $model]);
    }
    
    public function actionReject($id)
    {
        try {
            $rejectApplyForm = new RejectApplyForm($id);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($rejectApplyForm->load(Yii::$app->request->post()) && $rejectApplyForm->reject()) {
            if (!empty($rejectApplyForm->apply->store->cellphone)) {
                Yii::$app->smser->send($rejectApplyForm->apply->store->cellphone, "亲爱的店长,很抱歉您的采购订单{$rejectApplyForm->apply->apply_sn}申请失败,原因:{$rejectApplyForm->remark}");
            }
            Yii::$app->session->setFlash('success', '驳回成功！');
        } else {
            Yii::$app->session->setFlash('danger', '驳回失败！');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }
    
    public function actionPass($id)
    {
        $model = Apply::findOne(['id' => $id, 'status' => Apply::STATUS_PENDING]);
        
        if (!$model) {
            throw new BadRequestHttpException('参数错误！');
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->status = Apply::STATUS_PASSED;
        
            if (!$model->save(false)) {
                throw new \Exception('操作失败！');
            }
        
            $modelApplyLog = new ApplyLog();
            $modelApplyLog->apply_id = $id;
            $modelApplyLog->remark = '申请通过。';
        
            if (!$modelApplyLog->save(false)) {
                throw new \Exception('申请日志记录失败！');
            }
        
            $transaction->commit();
            if (!empty($model->store->cellphone)) {
                Yii::$app->smser->send($model->store->cellphone, "亲爱的店长,恭喜您的采购订单{$model->apply_sn}申请成功,我们的工作人员很快会与您取得联系。");
            }
            Yii::$app->session->setFlash('success', '操作成功！');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', $e->getMessage());
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }
}