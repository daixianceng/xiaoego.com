<?php

namespace m\modules\v1\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\data\SqlDataProvider;
use common\models\Order;
use common\filters\auth\HeaderParamAuth;
use frontend\models\CreateOrderForm;
use m\modules\v1\models\PayOrderForm;

class OrderController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HeaderParamAuth::className()
        ];
        return $behaviors;
    }
    
    protected function verbs()
    {
        return [
            'index' => ['get'],
            'detail' => ['get'],
            'create' => ['get', 'post'],
            'pay' => ['post'],
            'receive' => ['put', 'patch'],
            'cancel' => ['put', 'patch'],
            'timeout' => ['put', 'patch'],
            'delete' => ['delete']
        ];
    }
    
    public function actionIndex()
    {
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{%order}} WHERE user_id=:uid AND status<>:deleted', [
            ':uid' => Yii::$app->user->id,
            ':deleted' => Order::STATUS_DELETED
        ])->queryScalar();
        
        $dataProvider = new SqlDataProvider([
            'sql' => 'SELECT t0.id, t0.order_sn, t0.status, t0.real_fee, t0.created_at, t1.name AS store_name, t2.cover AS first_cover FROM {{%order}} AS t0 LEFT JOIN {{%store}} AS t1 ON t0.store_id=t1.id LEFT JOIN {{%order_goods}} AS t2 ON t2.id=(SELECT MIN(t2.id) FROM {{%order_goods}} AS t2 WHERE t0.id=t2.order_id) WHERE t0.user_id=:uid AND t0.status<>:deleted ORDER BY t0.id DESC',
            'params' => [
                ':uid' => Yii::$app->user->id,
                ':deleted' => Order::STATUS_DELETED
            ],
            'totalCount' => (int) $count,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        
        return $dataProvider;
    }
    
    public function actionDetail($id)
    {
        /* @var $model Order */
        $model = Order::findOne($id);
        
        if (!$model) {
            throw new BadRequestHttpException('未找到该订单！');
        }
        
        return [
            'id' => $model->id,
            'orderSn' => $model->order_sn,
            'storeId' => $model->store_id,
            'storeName' => $model->store->name,
            'status' => $model->status,
            'statusMsg' => $model->statusMsg,
            'payment' => $model->payment,
            'paymentMsg' => $model->paymentMsg,
            'fee' => $model->fee,
            'realFee' => $model->real_fee,
            'preferential' => $model->preferential,
            'downVal' => $model->down_val,
            'downMsg' => $model->downMsg,
            'giftVal' => $model->gift_val,
            'giftMsg' => $model->giftMsg,
            'newDownVal' => $model->new_down_val,
            'newDownMsg' => $model->newDownMsg,
            'bookTime' => $model->book_time,
            'bookTimeMsg' => $model->bookTimeMsg,
            'cancelledMsg' => $model->cancelled_msg,
            'description' => $model->description,
            'remark' => $model->remark,
            'timeout' => $model->timeout,
            'createdAt' => $model->created_at,
            'address' => $model->address,
            'addressMsg' => $model->address->addressMsg,
            'goods' => $model->goods
        ];
    }
    
    public function actionCreate($id)
    {
        try {
            $createOrderForm = new CreateOrderForm($id);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if (Yii::$app->request->method === 'GET') {
            return [
                'fee' => Yii::$app->user->identity->getCartGoodsRealVolume($id),
                'store' => $createOrderForm->store,
                'cartGoodsList' => $createOrderForm->cartGoodsList,
                'addressList' => $createOrderForm->addressList,
                'preferentialItems' => $createOrderForm->preferentialItems,
                'bookTimeItems' => $createOrderForm->bookTimeItems
            ];
        } else {
            if ($createOrderForm->load(Yii::$app->request->post(), '') && $createOrderForm->validate()) {
                try {
                    Yii::info('用户开始创建订单，用户id：' . Yii::$app->user->id);
                    if ($createOrderForm->create(false)) {
                        Yii::info("用户创建订单成功！订单号：{$createOrderForm->order->order_sn}");
                        if ($createOrderForm->payment === Order::PAYMENT_ONLINE) {
                        } else {
                            if ($createOrderForm->store->enable_sms && !empty($createOrderForm->store->cellphone)) {
                                Yii::$app->smser->send($createOrderForm->store->cellphone, "亲爱的店长,刚刚有人下了订单,订单号为{$createOrderForm->order->order_sn},请您快去查看。");
                            }
                        }
                        
                        return [
                            'status' => 'success',
                            'data' => [
                                'id' => $createOrderForm->order->id,
                                'orderSn' => $createOrderForm->order->order_sn,
                                'payment' => $createOrderForm->order->payment,
                            ]
                        ];
                    } else {
                        Yii::error('用户创建订单失败！用户id：' . Yii::$app->user->id);
                    }
                } catch (\Exception $e) {
                    Yii::error('用户创建订单失败！用户id：' . Yii::$app->user->id . ' 错误信息：' . $e->getMessage());
                    return [
                        'status' => 'fail',
                        'data' => [
                            'errors' => [$e->getMessage()]
                        ]
                    ];
                }
            }
            
            return [
                'status' => 'fail',
                'data' => [
                    'errors' => $createOrderForm->getFirstErrors() ?: ['订单创建失败！']
                ]
            ];
        }
    }
    
    
    public function actionPay($id)
    {
        try {
            $payOrderForm = new PayOrderForm($id);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($payOrderForm->load(Yii::$app->request->post(), '')) {
            try {
                if ($result = $payOrderForm->pay()) {
                    Yii::info("用户请求支付订单成功！订单号：{$payOrderForm->order->order_sn}，支付渠道：{$payOrderForm->channel}");
                    return [
                        'status' => 'success',
                        'data' => [
                            'charge' => $result->__toArray(true)
                        ]
                    ];
                } else {
                    $message = '请求支付订单失败！';
                }
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
        } else {
            $message = $payOrderForm->getFirstError('channel');
        }
        
        Yii::error("用户请求支付订单失败！订单号：{$payOrderForm->order->order_sn}，支付渠道：{$payOrderForm->channel}，说明：{$message}");
        
        return [
            'status' => 'fail',
            'data' => [
                'message' => $message
            ]
        ];
    }
    
    public function actionReceive($id)
    {
        $model = Order::find()->where([
            'and',
            ['user_id' => Yii::$app->user->id],
            ['id' => $id],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
    
        if (!$model) {
            throw new BadRequestHttpException('未找到订单！');
        }
    
        if ($model->status !== Order::STATUS_SHIPPED) {
            throw new BadRequestHttpException('参数错误！');
        }
    
        if ($model->complete()) {
            return [
                'status' => 'success',
                'data' => [
                    'status' => $model->status,
                    'statusMsg' => $model->statusMsg
                ]
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => []
            ];
        }
    }
    
    public function actionCancel($id)
    {
        $model = Order::find()->where([
            'and',
            ['user_id' => Yii::$app->user->id],
            ['id' => $id],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
    
        if (!$model) {
            throw new BadRequestHttpException('您没有该订单！');
        }
    
        if ($model->status !== Order::STATUS_UNPAID && $model->status !== Order::STATUS_CANCELLED) {
            throw new BadRequestHttpException('您不能取消该订单！');
        }
    
        if ($model->status === Order::STATUS_CANCELLED || $model->cancel()) {
            return [
                'status' => 'success',
                'data' => [
                    'status' => $model->status,
                    'statusMsg' => $model->statusMsg
                ]
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => []
            ];
        }
    }
    
    public function actionTimeout($id)
    {
        $model = Order::findOne([
            'user_id' => Yii::$app->user->id,
            'id' => $id,
            'status' => Order::STATUS_UNPAID
        ]);
    
        if ($model && $model->cancel('支付超时。')) {
            return [
                'status' => 'success',
                'data' => [
                    'status' => $model->status,
                    'statusMsg' => $model->statusMsg
                ]
            ];
        } else {
            return [
                'status' => 'fail',
                'data' => []
            ];
        }
    }
    
    public function actionDelete($id)
    {
        $model = Order::find()->where([
            'and',
            ['id' => $id],
            ['user_id' => Yii::$app->user->id],
            ['<>', 'status', Order::STATUS_DELETED]
        ])->one();
        
        if ($model) {
            if (!$model->discard()) {
                return [
                    'status' => 'fail',
                    'data' => []
                ];
            }
        }
        
        return [
            'status' => 'success',
            'data' => []
        ];
    }
}