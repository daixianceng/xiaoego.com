<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Order;

/**
 * Order controller
 * 
 * @author Cosmo <daixianceng@gmail.com>
 */
class OrderController extends Controller
{
    /**
     * 取消超过15分钟未付款的订单，每60秒执行一次
     * 
     * @return string
     */
    public function actionCancel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            
            $fifteenMinutesAgo = time() - 900;
            
            $orders = Order::find()->where([
                'and',
                ['status' => Order::STATUS_UNPAID],
                ['<=', 'created_at', $fifteenMinutesAgo]
            ])->all();
            
            $count1 = 0;
            $count2 = 0;
            
            foreach ($orders as $key => $order) {
                if ($order->cancel('支付超时。')) {
                    $count1 ++;
                } else {
                    $count2 ++;
                }
                
                unset($orders[$key]);
            }
            
            $transaction->commit();
            echo "{$count1} of orders affected by the execution, {$count2} fails.\n";
            return static::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return static::EXIT_CODE_ERROR;
        }
    }
    
    /**
     * 完成超过2小时未收货的订单，每小时执行一次
     * 
     * @return string
     */
    public function actionComplete()
    {
        $transaction = Yii::$app->db->beginTransaction();
    
        try {
    
            $twelveHoursAgo = time() - 3600 * 2;
            $orders = Order::find()->where([
                'and',
                ['status' => Order::STATUS_SHIPPED],
                ['<=', 'created_at', $twelveHoursAgo]
            ])->all();
            
            $count1 = 0;
            $count2 = 0;
            
            foreach ($orders as $key => $order) {
                if ($order->complete()) {
                    $count1 ++;
                } else {
                    $count2 ++;
                }
            
                unset($orders[$key]);
            }
    
            $transaction->commit();
            echo "{$count1} of orders affected by the execution, {$count2} fails.\n";
            return static::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return static::EXIT_CODE_ERROR;
        }
    }
    
    /**
     * 提醒超过15分钟未发货的商家
     * 
     * @return string
     */
    public function actionRemind()
    {
        $sql = "SELECT t1.cellphone, count(t0.id) AS count FROM {{%order}} AS t0 LEFT JOIN {{%store}} AS t1 ON t0.store_id = t1.id WHERE t0.status=:status AND t0.created_at < CURRENT_TIMESTAMP - 900 AND t0.created_at >= CURRENT_TIMESTAMP - 1800 GROUP BY t1.id;";
        
        $result = Yii::$app->db->createCommand($sql, [
            ':status' => Order::STATUS_UNSHIPPED
        ])->queryAll(\PDO::FETCH_BOTH);
        
        if ($result) {
            foreach ($result as $row) {
                Yii::$app->smser->send($row[0], "亲爱的店长，您有{$row[1]}笔订单等待发货，请您快去查看。");
                sleep(1);
            }
            
            echo "Reminded " . count($result) . " stores.\n";
        } else {
            echo "Nothing to do.\n";
        }
        
        return static::EXIT_CODE_NORMAL;
    }
}