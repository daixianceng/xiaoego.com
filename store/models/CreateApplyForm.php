<?php

namespace store\models;

use Yii;
use yii\base\Model;
use common\models\Apply;
use common\models\ApplyGoods;
use common\models\ApplyLog;
use common\models\Purchase;

class CreateApplyForm extends Model
{
    /**
     * @var string
     */
    public $remark;
    
    /**
     * @var static[] an array of Purchase instances, or an empty array.
     */
    private $_purchaseList;
    
    /**
     * @var Apply
     */
    private $_apply;

    /**
     * Creates a form model
     *
     * @param  array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = [])
    {
        $this->_purchaseList = Purchase::findAll(['store_id' => Yii::$app->user->identity->store_id]);
        
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['remark', 'trim'],
            ['remark', 'default'],
            ['remark', 'string', 'length' => [6, 255]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'remark' => '备注'
        ];
    }
    
    public function getPurchaseList()
    {
        return $this->_purchaseList;
    }
    
    public function getApply()
    {
        return $this->_apply;
    }
    
    public function create($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        if (empty($this->_purchaseList)) {
            return false;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
        
            if (Apply::find()->where([
                'store_id' => Yii::$app->user->identity->store_id,
                'status' => [Apply::STATUS_PENDING, Apply::STATUS_REJECTED, Apply::STATUS_PASSED]
            ])->exists()) {
                throw new \Exception('您采购历史中还有未完成的订单，不能继续申请.');
            }
        
            $this->_apply = new Apply();
            $this->_apply->generateApplySn();
            $this->_apply->store_id = Yii::$app->user->identity->store_id;
            $this->_apply->status = Apply::STATUS_PENDING;
            $this->_apply->fee = Purchase::getVolumeByStoreId($this->_apply->store_id);
            $this->_apply->remark = $this->remark;
        
            if (!$this->_apply->save(false)) {
                throw new \Exception('创建申请失败！');
            }
        
            foreach ($this->_purchaseList as $purchase) {
                if ($purchase->isExpired) {
                    throw new \Exception("商品“{$purchase->goods->name}”已失效，请删除该商品然后继续。");
                }
                
                $modelApplyGoods = ApplyGoods::createDuplicate($purchase->goods_id);
                $modelApplyGoods->apply_id = $this->_apply->id;
                $modelApplyGoods->count = $purchase->count;
        
                if (!$modelApplyGoods->save(false)) {
                    throw new \Exception('记录商品清单失败！');
                }
            }
        
            $modelApplyLog = new ApplyLog();
            $modelApplyLog->apply_id = $this->_apply->id;
            $modelApplyLog->remark = '开始提交申请。';
        
            if (!$modelApplyLog->save(false)) {
                throw new \Exception('商品申请记录失败！');
            }
        
            Purchase::clear(Yii::$app->user->identity->store_id);
        
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
