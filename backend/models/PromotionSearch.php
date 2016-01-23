<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Promotion;

class PromotionSearch extends Promotion
{
    public $date;
    
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['name', 'store_id', 'time_start', 'time_end', 'status', 'date'], 'safe'],
            [['date'], 'trim']
        ];
    }

    public function search($params)
    {
        $query = Promotion::find();
        
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
        
        $dateBegin = strtotime($this->date);
        $dateEnd = $dateBegin + 86400;

        // adjust the query by adding the filters
        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['store_id' => $this->store_id])
              ->andFilterWhere(['time_start' => $this->time_start])
              ->andFilterWhere(['time_end' => $this->time_end])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['>=', 'created_at', $this->date ? $dateBegin : null])
              ->andFilterWhere(['<', 'created_at', $this->date ? $dateEnd : null]);

        return $dataProvider;
    }
}