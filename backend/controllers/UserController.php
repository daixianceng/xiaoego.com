<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\Order;
use backend\models\UserSearch;

/**
 * User controller
 */
class UserController extends Controller
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
            ]
        ];
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionView($id)
    {
        $model = User::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('未找到该用户');
        }
        
        $last15days = [];
        $numDataOrder = [];
        $today = strtotime("00:00:00");
        $todayEnd = strtotime("23:59:59");
        for ($i = 0; $i < 15; $i++) {
            $timestrap = strtotime('-' . $i . ' days', $today);
            $timestrapEnd = strtotime('-' . $i . ' days', $todayEnd);
            $where = [
                'and',
                ['user_id' => $id],
                ['>=', 'created_at', $timestrap],
                ['<=', 'created_at', $timestrapEnd]
            ];
            array_unshift($last15days, date('m/d', $timestrap));
            array_unshift($numDataOrder, Order::find()->where($where)->count('id'));
        }
        
        return $this->render('view', [
            'model' => $model,
            'last15days' => $last15days,
            'numDataOrder' => $numDataOrder
        ]);
    }

    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Yii::$app->request->post('status');
    
        $model = User::findOne($id);
    
        if (!$model || !in_array($status, [User::STATUS_ACTIVE, User::STATUS_BLOCKED])) {
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
    
    public function actionMobileFilter($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $output = ['results' => ['id' => '', 'text' => '']];
    
        if (!is_null($q)) {
            $sql = 'SELECT id, mobile AS text FROM ' . User::tableName() . ' WHERE mobile LIKE :like ORDER BY mobile ASC LIMIT 25';
            $output['results'] = Yii::$app->db->createCommand($sql, [':like' => "%$q%"])->queryAll();
        } elseif ($id > 0 && ($user = User::findOne($id))) {
            $output['results'] = ['id' => $id, 'text' => $user->mobile];
        }
    
        return $output;
    }
}