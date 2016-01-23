<?php

namespace store\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Tag;

class TagSearch extends Tag
{
    public $date;
    
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['name', 'date'], 'safe'],
            [['date'], 'trim']
        ];
    }

    public function search($params)
    {
        $query = Tag::find()->where(['store_id' => Yii::$app->user->identity->store_id]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sort' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 0
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
              ->andFilterWhere(['>=', 'created_at', $this->date ? $dateBegin : null])
              ->andFilterWhere(['<', 'created_at', $this->date ? $dateEnd : null]);

        return $dataProvider;
    }
}