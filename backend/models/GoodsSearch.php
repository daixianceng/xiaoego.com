<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use common\models\Goods;

class GoodsSearch extends Goods
{
    public $date;
    
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['name', 'category_id', 'store_id', 'price', 'cost', 'sales', 'surplus', 'status', 'date'], 'safe'],
            [['date', 'price', 'sales', 'surplus'], 'trim']
        ];
    }

    public function search($params)
    {
        $query = Goods::find();
        
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
        
        $this->_addDigitalFilter($query, 'price');
        $this->_addDigitalFilter($query, 'cost');
        $this->_addDigitalFilter($query, 'sales');
        $this->_addDigitalFilter($query, 'surplus');
        
        $dateBegin = strtotime($this->date);
        $dateEnd = $dateBegin + 86400;

        // adjust the query by adding the filters
        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['category_id' => $this->category_id])
              ->andFilterWhere(['store_id' => $this->store_id])
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