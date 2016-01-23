<?php

namespace common\models;

use Yii;
use himiklab\sortablegrid\SortableGridBehavior;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $sort
 * @property string $typeMsg read-only $typeMsg
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }
    
    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','slug'],'required'],
            [['name','slug'],'string','max' => 60],
            [['slug'],'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'slug' => '唯一字符串',
            'sort' => '排序'
        ];
    }

    public static function getKeyValuePairs()
    {
        $sql = 'SELECT id, name FROM ' . self::tableName() . ' ORDER BY sort ASC';
        
        return Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_KEY_PAIR);
    }
    
    public static function getCategoryPairs()
    {
        $sql = 'SELECT slug, name FROM ' . self::tableName() . ' ORDER BY sort ASC';
    
        return Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_KEY_PAIR);
    }
}
