<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use common\models\Order;

class OrderSearch extends Order
{
    public $date;
    
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['order_sn', 'school_id', 'store_id', 'user_id', 'payment', 'real_fee', 'status', 'date'], 'safe'],
            [['date', 'real_fee'], 'trim']
        ];
    }

    public function search($params)
    {
        $query = Order::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        // load the seach form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $this->_addDigitalFilter($query, 'real_fee');
        
        $dateBegin = strtotime($this->date);
        $dateEnd = $dateBegin + 86400;

        // adjust the query by adding the filters
        $query->andFilterWhere(['like', 'order_sn', $this->order_sn])
              ->andFilterWhere(['school_id' => $this->school_id])
              ->andFilterWhere(['store_id' => $this->store_id])
              ->andFilterWhere(['user_id' => $this->user_id])
              ->andFilterWhere(['payment' => $this->payment])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['>=', 'created_at', $this->date ? $dateBegin : null])
              ->andFilterWhere(['<', 'created_at', $this->date ? $dateEnd : null]);

        return $dataProvider;
    }
    
    protected function _addDigitalFilter($query, $attribute)
    {
        $pattern = '/^(>|>=|<|<=|=)(\d*\.?\d+)$/';
        if (preg_match($pattern, $this->{$attribute}, $matches) === 1) {
            $query->andFilterWhere([$matches[1], $attribute, $matches[2]]);
        } else {
            $query->andFilterWhere(['like', $attribute, $this->{$attribute}]);
        }
    }
}