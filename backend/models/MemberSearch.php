<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use common\models\Member;

class MemberSearch extends Member
{
    public $date;
    
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['store_id', 'username', 'real_name', 'gender', 'email', 'mobile', 'status', 'date'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Member::find();
        
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
        $query->andFilterWhere(['store_id' => $this->store_id])
              ->andFilterWhere(['like', 'username', $this->username])
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