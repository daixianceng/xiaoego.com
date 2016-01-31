<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use common\models\Category;

class CategorySearch extends Category
{
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['name', 'slug'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Category::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sort' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        // load the seach form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}