<?php

namespace backend\models;

use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\Goods;

class MoveSurplusForm extends Model
{
    public $amount;
    
    /**
     * @var \common\models\Goods
     */
    private $_goods;
    
    /**
     * Creates a form model given a goods id.
     *
     * @param  string                          $id
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if goods id is empty or not valid
     */
    public function __construct($id, $config = [])
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidParamException('参数错误！');
        }
        $this->_goods = Goods::findOne($id);
    
        if (!$this->_goods) {
            throw new InvalidParamException('未找到该商品！');
        }
    
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['amount', 'required'],
            ['amount', 'integer'],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '!=']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => '变更数值'
        ];
    }
    
    public function getGoods()
    {
        return $this->_goods;
    }
    
    public function move($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
    
        return $this->_goods->moveSurplus($this->amount, '管理人员调整库存。');
    }
}