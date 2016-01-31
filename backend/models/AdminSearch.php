<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use common\models\Admin;

class AdminSearch extends Admin
{
    public $date;
    
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['username', 'real_name', 'gender', 'email', 'mobile', 'status', 'date'], 'safe'],
            [['date'], 'trim']
        ];
    }

    public function search($params)
    {
        $query = Admin::find();
        
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
        $query->andFilterWhere(['like', 'username', $this->username])
              ->andFilterWhere(['like', 'real_name', $this->real_name])
              ->andFilterWhere(['gender' => $this->gender])
              ->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'mobile', $this->mobile])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['>=', 'created_at', $this->date ? $dateBegin : null])
              ->andFilterWhere(['<', 'created_at', $this->date ? $dateEnd : null]);

        return $dataProvider;
    }
}