<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use common\models\Order;
use common\models\Address;
use common\models\Store;
use frontend\models\CreateOrderForm;
use frontend\models\PayOrderForm;

/**
 * Order controller
 */
class OrderController extends Controller
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
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'to-offline' => ['post'],
                    'receive' => ['post'],
                    'cancel' => ['post'],
                    'delete' => ['post'],
                    'address-add' => ['post'],
                    'address-edit' => ['post'],
                    'address-load' => ['post'],
                    'timeout' => ['post'],
                    'real-fee' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = Order::find()->where([
            'and',
            ['user_id' => Yii::$app->user->id],
            ['<>', 'status', Order::STATUS_DELETED]
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCreate($id)
    {
        $this->layout = 'column2';
        
        try {
            $createOrderForm = new CreateOrderForm($id);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($createOrderForm->load(Yii::$app->request->post()) && $createOrderForm->validate()) {
            try {
                Yii::info('用户开始创建订单，用户id：' . Yii::$app->user->id);
                if ($createOrderForm->create(false)) {
                    Yii::info("用户创建订单成功！订单号：{$createOrderForm->order->order_sn}");
                    if ($createOrderForm->payment === Order::PAYMENT_ONLINE) {
                        Yii::$app->session->setFlash('success', '订单创建成功！');
                        return $this->redirect(['pay', 'order' => $createOrderForm->order->order_sn]);
                    } else {
                        if ($createOrderForm->store->enable_sms && !empty($createOrderForm->store->cellphone)) {
                            Yii::$app->smser->send($createOrderForm->store->cellphone, "亲爱的店长,刚刚有人下了订单,订单号为{$createOrderForm->order->order_sn},请您快去查看。");
                        }
                        return $this->render('create-success', ['order' => $createOrderForm->order]);
                    }
                } else {
                    Yii::error('用户创建订单失败！用户id：' . Yii::$app->user->id);
                    Yii::$app->session->setFlash('danger', '订单创建失败！');
                }
            } catch (\Exception $e) {
                Yii::error('用户创建订单失败！用户id：' . Yii::$app->user->id . ' 错误信息：' . $e->getMessage());
                Yii::$app->session->setFlash('danger', $e->getMessage());
            }
        }
        
        return $this->render('create', [
            'createOrderForm' => $createOrderForm,
            'store' => $createOrderForm->store,
            'cartGoodsList' => $createOrderForm->cartGoodsList,
            'addressList' => $createOrderForm->addressList
        ]);
    }
    
    public function actionPay($order)
    {
        $this->layout = 'column2';
        
        try {
            $payOrderForm = new PayOrderForm($order);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($payOrderForm->order->status !== Order::STATUS_UNPAID) {
            return $this->redirect(['/order/detail', 'order' => $order]);
        }
        
        if ($payOrderForm->load(Yii::$app->request->post())) {
            if ($result = $payOrderForm->pay()) {
                Yii::info("用户请求支付订单成功！订单号：{$order}，支付平台：{$payOrderForm->platform}");
                $this->layout = false;
                return $result;
            } else {
                Yii::error("用户请求支付订单失败！订单号：{$order}，支付平台：{$payOrderForm->platform}");
            }
        }
        
        return $this->render('pay', [
            'model' => $payOrderForm,
            'order' => $payOrderForm->order
        ]);
    }
    
    public function actionToOffline($order)
    {
        $model = Order::find()->where([
            'and',
            ['order_sn' => $order],
            ['user_id' => Yii::$app->user->id],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
    
        if (!$model) {
            throw new NotFoundHttpException('您没有该订单！');
        }
        
        if ($model->status !== Order::STATUS_UNPAID) {
            throw new BadRequestHttpException('参数错误！');
        }
        
        $model->status = Order::STATUS_UNSHIPPED;
        $model->payment = Order::PAYMENT_OFFLINE;
        
        if ($model->save(false)) {
            if ($model->store->enable_sms && !empty($model->store->cellphone)) {
                Yii::$app->smser->send($model->store->cellphone, "亲爱的店长,刚刚有人下了订单,订单号为{$order},请您快去查看。");
            }
            Yii::$app->session->setFlash('success', '订单成功由在线支付转为货到付款。');
            return $this->redirect(['detail', 'order' => $order]);
        } else {
            Yii::$app->session->setFlash('danger', '订单转换失败');
            return $this->redirect(['pay', 'order' => $order]);
        }
    }
    
    public function actionDetail($order)
    {
        $model = Order::find()->where([
            'and',
            ['order_sn' => $order],
            ['user_id' => Yii::$app->user->id],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
        
        if (!$model) {
            throw new NotFoundHttpException('您没有该订单！');
        }
        
        return $this->render('detail', [
            'model' => $model
        ]);
    }
    
    public function actionCancel($order)
    {
        $model = Order::find()->where([
            'and',
            ['user_id' => Yii::$app->user->id],
            ['order_sn' => $order],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
        
        if (!$model) {
            throw new NotFoundHttpException('您没有该订单！');
        }
        
        if ($model->status !== Order::STATUS_UNPAID) {
            throw new BadRequestHttpException('您不能取消该订单！');
        }
        
        if ($model->cancel()) {
            Yii::$app->session->setFlash('success', '订单成功取消！');
        } else {
            Yii::$app->session->setFlash('danger', '订单取消失败！');
        }
    
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionTimeout($order)
    {
        $model = Order::findOne([
            'user_id' => Yii::$app->user->id,
            'order_sn' => $order,
            'status' => Order::STATUS_UNPAID
        ]);
        
        if ($model && $model->cancel('支付超时。')) {
            Yii::$app->session->setFlash('warning', '订单取消，支付超时！');
        }
        
        return $this->redirect(['/order/detail', 'order' => $order]);
    }
    
    public function actionReceive($order)
    {
        $model = Order::find()->where([
            'and',
            ['user_id' => Yii::$app->user->id],
            ['order_sn' => $order],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
        
        if (!$model) {
            throw new NotFoundHttpException('未找到订单！');
        }
        
        if ($model->status !== Order::STATUS_SHIPPED) {
            throw new BadRequestHttpException('参数错误！');
        }
    
        if ($model->complete()) {
            Yii::$app->session->setFlash('success', '订单完成！');
        } else {
            Yii::$app->session->setFlash('danger', '确认失败！');
        }
    
        return $this->redirect(['/order/detail', 'order' => $order]);
    }
    
    public function actionDelete($order)
    {
        $model = Order::find()->where([
            'and',
            ['order_sn' => $order],
            ['user_id' => Yii::$app->user->id],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
        
        if ($model) {
            if ($model->discard()) {
                Yii::$app->session->setFlash('success', '已成功删除一个订单');
            } else {
                Yii::$app->session->setFlash('danger', '删除失败！');
                if (Yii::$app->request->referrer) {
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }
        
        return $this->redirect(['index']);
    }
    
    public function actionAddressAdd()
    {
        $model = new Address();
        $model->load(Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->isPost) {
            $model->user_id = Yii::$app->user->id;
            $model->save();
        }
        
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionAddressEdit($id)
    {
        $model = Address::findOne([
            'id' => $id,
            'user_id' => Yii::$app->user->id
        ]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该地址！');
        }
        
        $model->load(Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->isPost) {
            $model->save();
        }
        
        if (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['index']);
        }
    }
    
    public function actionAddressLoad($id)
    {
        $model = Address::findOne([
            'id' => $id,
            'user_id' => Yii::$app->user->id
        ]);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该地址！');
        }
        
        $output = [
            'consignee' => $model->consignee,
            'gender' => $model->gender,
            'cellphone' => $model->cellphone,
            'building_id' => $model->building_id,
            'room' => $model->room
        ];
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $output;
    }
    
    public function actionRealFee()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $storeId = Yii::$app->request->post('storeId', 0);
        $preferential = Yii::$app->request->post('preferential');
        $newDown = Yii::$app->request->post('newdown');
        $payment = Yii::$app->request->post('payment');
        
        /* @var $model Store */
        $model = Store::findOne($storeId);
        
        if (!$model) {
            return ['status' => 'no'];
        }
        
        $realFee = $fee = Yii::$app->user->identity->getCartGoodsRealVolume($storeId);
        
        switch ($preferential) {
            case Order::PREFERENTIAL_DOWN :
                if ($model->has_down && $fee >= $model->down_upper) {
                    $realFee = bcsub($realFee, $model->down_val, 2);
                }
                break;
            default:
                break;
        }
        
        if (Yii::$app->params['enableNewDown'] && $newDown &&
            $fee >= Yii::$app->params['newDownUpper'] && Yii::$app->user->identity->has_new_down) {

            $realFee = bcsub($realFee, Yii::$app->params['newDownVal'], 2);

            if ($realFee < 0) {
                $realFee = '0.00';
            }
        }
        
        return ['status' => 'ok', 'realFee' => $realFee];
    }
}