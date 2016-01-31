<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use common\models\Store;
use common\models\School;
use common\models\Order;
use common\models\OrderVolume;
use backend\models\StoreSearch;

/**
 * Store controller
 */
class StoreController extends Controller
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
        $searchModel = new StoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionAdd()
    {
        $model = new Store();
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功添加营业点“'.$model->name.'”。');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', '添加营业点失败。');
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = Store::findOne($id);
    
        if (!$model) {
            throw new NotFoundHttpException('未找到该营业点。');
        }
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '成功更新营业点“'.$model->name.'”。');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('danger', '更新营业点失败。');
            }
        }
    
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionView($id)
    {
        $model = Store::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该营业点');
        }
        
        $last15days         = [];
        $last6Month         = [];
        $numDataOrder       = []; // 订单生成数据
        $numDataVolume      = []; // 营业额数据
        $numDataCompleted   = []; // 订单完成数据
        $numDataVolumeMonth = []; // 每月营业额
        
        $today = strtotime("00:00:00");
        $todayEnd = strtotime("23:59:59");
        for ($i = 0; $i < 15; $i++) {
            $timestrap = strtotime('-' . $i . ' days', $today);
            $timestrapEnd = strtotime('-' . $i . ' days', $todayEnd);
            $where = [
                'and',
                ['store_id' => $id],
                ['>=', 'created_at', $timestrap],
                ['<=', 'created_at', $timestrapEnd]
            ];
            array_unshift($last15days, date('m/d', $timestrap));
            array_unshift($numDataOrder, Order::find()->where($where)->count());
        
            $data = OrderVolume::find()->select(['sum(volume) AS volume', 'count(*) AS count'])
                                       ->where($where)
                                       ->asArray()
                                       ->one();
            array_unshift($numDataVolume, $data['volume']);
            array_unshift($numDataCompleted, $data['count']);
        }
        
        for ($i = 0; $i < 6; $i ++) {
            $timestrap = strtotime("first day of -{$i} month", $today);
            $timestrapEnd = strtotime("last day of -{$i} month", $todayEnd);
            $where = [
                'and',
                ['store_id' => $id],
                ['>=', 'created_at', $timestrap],
                ['<=', 'created_at', $timestrapEnd]
            ];
            array_unshift($last6Month, date('Y/m', $timestrap));
            array_unshift($numDataVolumeMonth, OrderVolume::find()->where($where)->sum('volume'));
        }
        
        $data2 = OrderVolume::find()->select(['sum(volume) AS volume', 'count(*) AS count'])
                                    ->where(['store_id' => $id])
                                    ->asArray()
                                    ->one();
        
        return $this->render('view', [
            'model' => $model,
            'last15days' => $last15days,
            'last6Month' => $last6Month,
            'numDataOrder' => $numDataOrder,
            'numDataVolume' => $numDataVolume,
            'numDataCompleted' => $numDataCompleted,
            'numDataVolumeMonth' => $numDataVolumeMonth,
            'countOrder' => Order::getCountByStoreId($id),
            'countCompleted' => $data2['count'],
            'sumVolume' => $data2['volume']
        ]);
    }

    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Yii::$app->request->post('status');
    
        $model = Store::findOne($id);
    
        if (!$model || !in_array($status, [Store::STATUS_ACTIVE, Store::STATUS_REST, Store::STATUS_DISABLED])) {
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
    
    public function actionItems($id)
    {
        $list = Store::getKeyValuePairs($id);
        
        $output = ['status' => 'ok', 'html' => ''];
        foreach ($list as $key => $value) {
            $output['html'] .= '<option value="' . $key . '">' . $value . '</option>';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $output;
    }
    
    public function actionNameFilter($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $output = ['results' => ['id' => '', 'text' => '']];
    
        if (!is_null($q)) {
            $sql = 'SELECT t0.id, CONCAT(t1.name, \'-\', t0.name) AS text FROM ' . Store::tableName() . ' AS t0 LEFT JOIN ' . School::tableName() . ' AS t1 ON t0.school_id = t1.id WHERE t0.name LIKE :like ORDER BY t0.school_id ASC LIMIT 25';
            $output['results'] = Yii::$app->db->createCommand($sql, [':like' => "%$q%"])->queryAll();
        } elseif ($id > 0 && ($store = Store::findOne($id))) {
            $output['results'] = ['id' => $id, 'text' => $store->school->name . '-' . $store->name];
        }
    
        return $output;
    }
}