<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Goods;
use common\models\GoodsImg;
use common\models\Category;
use common\models\Store;

/**
 * Goods controller
 * 
 * @author Cosmo <daixianceng@gmail.com>
 */
class GoodsController extends Controller
{
    /**
     * 刷新商品月销量，每日凌晨4点刷新
     * 
     * @return string
     */
    public function actionRefreshSales()
    {
        $transaction = Yii::$app->db->beginTransaction();
    
        try {
    
            $aMonthAgo = strtotime('-1 month', strtotime("00:00:00"));
            $sql = "UPDATE {{%goods}} SET sales = (SELECT IFNULL(SUM(t0.count), 0) FROM {{%order_goods}} AS t0 LEFT JOIN {{%order_volume}} AS t1 ON t0.order_id = t1.order_id WHERE t0.goods_id = {{%goods}}.id AND t1.id IS NOT NULL AND t1.created_at >= $aMonthAgo)";
            $count = Yii::$app->db->createCommand($sql)->execute();
    
            $transaction->commit();
            echo "{$count} of goods affected by the execution.\n";
            return static::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return static::EXIT_CODE_ERROR;
        }
    }
    
    /**
     * 将商品克隆到新的店铺
     * 
     * @param array $id  源商品ID或一个ID列表
     * @param string $to 目标店铺
     * @return string
     */
    public function actionClone(array $id, $to, $surplus = null)
    {
        if (!Store::find()->where(['id' => $to])->exists()) {
            echo "The parameter error.\n";
            return static::EXIT_CODE_ERROR;
        }
        
        $query = Goods::find()->where(['id' => $id]);
        
        if (($count = $query->count()) > 0) {
            if (!static::confirm("Next we will clone {$count} goods, are you sure?")) {
                echo "Cancelled.\n";
                return static::EXIT_CODE_NORMAL;
            }
        } else {
            echo "The goods count is 0.\n";
            return static::EXIT_CODE_NORMAL;
        }
        
        $goodsList = $query->asArray()->all();
        $time = time();
        $sql = "INSERT INTO {{%goods}} (name, store_id, category_id, cover, price, price_original, cost, description, status, surplus, sales, unit, is_new, is_hot, is_promotion, created_at, updated_at) VALUES (:name, :store_id, :category_id, :cover, :price, :price_original, :cost, :description, :status, :surplus, :sales, :unit, :is_new, :is_hot, :is_promotion, :created_at, :updated_at)";
        $sql2 = "INSERT INTO {{%goods_img}} (name, goods_id) VALUES (:name, :goods_id)";
        $sql3 = "INSERT INTO {{%goods_surplus}} (goods_id, surplus_before, amount, surplus_after, remark, created_at, updated_at) VALUES (:goods_id, :surplus_before, :amount, :surplus_after, :remark, :created_at, :updated_at)";
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            foreach ($goodsList as $goods) {
                Yii::$app->db->createCommand($sql, [
                    ':name' => $goods['name'],
                    ':store_id' => $to,
                    ':category_id' => $goods['category_id'],
                    ':cover' => $goods['cover'],
                    ':price' => $goods['price'],
                    ':price_original' => $goods['price_original'],
                    ':cost' => $goods['cost'],
                    ':description' => $goods['description'],
                    ':status' => $goods['status'],
                    ':surplus' => $surplus ?: $goods['surplus'],
                    ':sales' => '0',
                    ':unit' => $goods['unit'],
                    ':is_new' => $goods['is_new'],
                    ':is_hot' => $goods['is_hot'],
                    ':is_promotion' => $goods['is_promotion'],
                    ':created_at' => $time,
                    ':updated_at' => $time
                ])->execute();
        
                $goodsId = Yii::$app->db->getLastInsertID();
                $goodsImgList = GoodsImg::find()->where(['goods_id' => $goods['id']])->asArray()->all();
        
                foreach ($goodsImgList as $goodsImg) {
                    Yii::$app->db->createCommand($sql2, [
                        ':name' => $goodsImg['name'],
                        ':goods_id' => $goodsId
                    ])->execute();
                }
                
                Yii::$app->db->createCommand($sql3, [
                    ':goods_id' => $goodsId,
                    ':surplus_before' => 0,
                    ':amount' => $surplus ?: $goods['surplus'],
                    ':surplus_after' => $surplus ?: $goods['surplus'],
                    ':remark' => '初始化库存。',
                    ':created_at' => $time,
                    ':updated_at' => $time
                ])->execute();
            }
        
            $transaction->commit();
            echo "Success!\n";
            return static::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return static::EXIT_CODE_ERROR;
        }
    }
    
    /**
     * 将商品从一个店铺克隆到另一个店铺
     * 
     * @param string $from     源商品所在店铺ID
     * @param string $to       目标店铺ID
     * @param string $category 过滤商品类型
     * @param string $status   过滤商品状态
     * @return string
     */
    public function actionCloneAll($from, $to, $category = 'all', $status = 'all')
    {
        $modelFrom = Store::findOne($from);
        $modelTo = Store::findOne($to);
        
        if (!$modelFrom || !$modelTo) {
            echo "The parameter error.\n";
            return static::EXIT_CODE_ERROR;
        }
        
        if ($modelFrom->type !== $modelTo->type) {
            if (!static::confirm("The types of two stores are not the same, continue?")) {
                echo "Cancelled.\n";
                return static::EXIT_CODE_NORMAL;
            }
        }
        
        unset($modelFrom, $modelTo);
        
        $query = Goods::find()->where(['store_id' => $from]);
        
        if ($category !== 'all') {
            $modelCate = Category::findOne(['slug' => $category]);
            
            if ($modelCate) {
                $query->andWhere(['category_id' => $modelCate->id]);
                unset($modelCate);
            } else {
                echo "The category \"{$category}\" can not be exists.\n";
                return static::EXIT_CODE_ERROR;
            }
        }
        
        if ($status !== 'all') {
            if (in_array($status, [Goods::STATUS_NORMAL, Goods::STATUS_OFF_SHELVES, Goods::STATUS_DELETED])) {
                $query->andWhere(['status' => $status]);
            } else {
                echo "The status was error.\n";
                return static::EXIT_CODE_ERROR;
            }
        }
        
        if (($count = $query->count()) > 0) {
            if (!static::confirm("Next we will clone {$count} goods, are you sure?")) {
                echo "Cancelled.\n";
                return static::EXIT_CODE_NORMAL;
            }
        } else {
            echo "The goods count is 0.\n";
            return static::EXIT_CODE_NORMAL;
        }
        
        $goodsList = $query->asArray()->all();
        $time = time();
        $sql = "INSERT INTO {{%goods}} (name, store_id, category_id, cover, price, price_original, cost, description, status, surplus, sales, unit, is_new, is_hot, is_promotion, created_at, updated_at) VALUES (:name, :store_id, :category_id, :cover, :price, :price_original, :cost, :description, :status, :surplus, :sales, :unit, :is_new, :is_hot, :is_promotion, :created_at, :updated_at)";
        $sql2 = "INSERT INTO {{%goods_img}} (name, goods_id) VALUES (:name, :goods_id)";
        $sql3 = "INSERT INTO {{%goods_surplus}} (goods_id, surplus_before, amount, surplus_after, remark, created_at, updated_at) VALUES (:goods_id, :surplus_before, :amount, :surplus_after, :remark, :created_at, :updated_at)";
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            foreach ($goodsList as $goods) {
                Yii::$app->db->createCommand($sql, [
                    ':name' => $goods['name'],
                    ':store_id' => $to,
                    ':category_id' => $goods['category_id'],
                    ':cover' => $goods['cover'],
                    ':price' => $goods['price'],
                    ':price_original' => $goods['price_original'],
                    ':cost' => $goods['cost'],
                    ':description' => $goods['description'],
                    ':status' => $goods['status'],
                    ':surplus' => $goods['surplus'],
                    ':sales' => '0',
                    ':unit' => $goods['unit'],
                    ':is_new' => $goods['is_new'],
                    ':is_hot' => $goods['is_hot'],
                    ':is_promotion' => $goods['is_promotion'],
                    ':created_at' => $time,
                    ':updated_at' => $time
                ])->execute();
                
                $goodsId = Yii::$app->db->getLastInsertID();
                $goodsImgList = GoodsImg::find()->where(['goods_id' => $goods['id']])->asArray()->all();
                
                foreach ($goodsImgList as $goodsImg) {
                    Yii::$app->db->createCommand($sql2, [
                        ':name' => $goodsImg['name'],
                        ':goods_id' => $goodsId
                    ])->execute();
                }
                
                Yii::$app->db->createCommand($sql3, [
                    ':goods_id' => $goodsId,
                    ':surplus_before' => 0,
                    ':amount' => $goods['surplus'],
                    ':surplus_after' => $goods['surplus'],
                    ':remark' => '初始化库存。',
                    ':created_at' => $time,
                    ':updated_at' => $time
                ])->execute();
            }
            
            $transaction->commit();
            echo "Success!\n";
            return static::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
            return static::EXIT_CODE_ERROR;
        }
    }
}