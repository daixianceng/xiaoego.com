<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\Apply;
use common\models\ApplyLog;

/**
 * Reject apply form
 */
class RejectApplyForm extends Model
{
    public $remark;

    /**
     * @var Apply
     */
    private $_apply;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remark'], 'trim'],
            [['remark'], 'required'],
            [['remark'], 'string', 'length' => [6, 60]]
        ];
    }
    
    /**
     * Creates a form model given an apply id.
     *
     * @param  string                          $id
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if waybill sn is empty or not valid
     */
    public function __construct($id, $config = [])
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidParamException('参数错误！');
        }
        $this->_apply = Apply::findOne($id);
    
        if (!$this->_apply) {
            throw new InvalidParamException('未找到该申请！');
        }
        if ($this->_apply->status !== Apply::STATUS_PENDING) {
            throw new InvalidParamException('请求错误！');
        }
    
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'remark' => '驳回理由'
        ];
    }

    /**
     * @return boolean
     */
    public function reject($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->_apply->status = Apply::STATUS_REJECTED;
        
            if (!$this->_apply->save(false)) {
                throw new \Exception('保存失败！');
            }
        
            $modelApplyLog = new ApplyLog();
            $modelApplyLog->apply_id = $this->_apply->id;
            $modelApplyLog->remark = '申请被驳回，原因：' . $this->remark;
        
            if (!$modelApplyLog->save(false)) {
                throw new \Exception('申请日志记录失败！');
            }
        
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @return Apply
     */
    public function getApply()
    {
        return $this->_apply;
    }
}
