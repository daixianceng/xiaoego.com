<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Store;

/**
 * Store controller
 * 
 * @author Cosmo <daixianceng@gmail.com>
 */
class StoreController extends Controller
{
    /**
     * 自动将相应店铺开启营业状态，每天早上8点整执行一次
     * 
     * @return string
     */
    public function actionToActive()
    {
        $transaction = Yii::$app->db->beginTransaction();
    
        try {
    
            $sql = 'UPDATE {{%store}} SET status=:status WHERE auto_toggle=:auto_toggle AND status<>:status_where AND toggle_type IN (:type_active, :type_both)';
            $count = Yii::$app->db->createCommand($sql, [
                ':status' => Store::STATUS_ACTIVE,
                ':auto_toggle' => '1',
                ':status_where' => Store::STATUS_DISABLED,
                ':type_active' => Store::TOGGLE_TYPE_ACTIVE,
                ':type_both' => Store::TOGGLE_TYPE_BOTH
            ])->execute();
    
            $transaction->commit();
            echo "{$count} of stores affected by the execution.\n";
            return static::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return static::EXIT_CODE_ERROR;
        }
    }
    
    /**
     *自动将相应店铺开启休息状态，每天凌晨零点整执行一次
     *
     * @return string
     */
    public function actionToRest()
    {
        $transaction = Yii::$app->db->beginTransaction();
    
        try {
    
            $sql = 'UPDATE {{%store}} SET status=:status WHERE auto_toggle=:auto_toggle AND status<>:status_where AND toggle_type IN (:type_rest, :type_both)';
            $count = Yii::$app->db->createCommand($sql, [
                ':status' => Store::STATUS_REST,
                ':auto_toggle' => '1',
                ':status_where' => Store::STATUS_DISABLED,
                ':type_rest' => Store::TOGGLE_TYPE_REST,
                ':type_both' => Store::TOGGLE_TYPE_BOTH
            ])->execute();
    
            $transaction->commit();
            echo "{$count} of stores affected by the execution.\n";
            return static::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return static::EXIT_CODE_ERROR;
        }
    }
}