<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property integer $store_id
 * @property string $cover
 * @property string $price
 * @property string $price_original
 * @property string $cost
 * @property string $description
 * @property string $status
 * @property integer $surplus
 * @property integer $sales
 * @property string $unit
 * @property integer $is_new
 * @property integer $is_hot
 * @property integer $is_promotion
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $statusMsg read-only $statusMsg
 * @property string $isNewMsg read-only $isNewMsg
 * @property string $isHotMsg read-only $isHotMsg
 * @property string $isPromotionMsg read-only $isPromotionMsg
 */
class Goods extends \yii\db\ActiveRecord
{
    const STATUS_NORMAL = 'normal';
    const STATUS_OFF_SHELVES = 'off_shelves';
    const STATUS_DELETED = 'deleted';

    const BOOL_TRUE = 1;
    const BOOL_FALSE = 0;

    private static $_statusList;
    private static $_isNewList;
    private static $_isHotList;
    private static $_isPromotionList;

    public $image;
    public $photos;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'category_id', 'store_id', 'price', 'unit', 'description'], 'required'],

            ['image', 'required', 'on' => 'insert'],
            [
                'image',
                'image',
                'extensions' => 'jpg, png',
                'mimeTypes' => 'image/jpeg, image/png',
                'checkExtensionByMimeType' => false,
                'minSize' => 100,
                'maxSize' => 204800,
                'tooBig' => '{attribute}最大不能超过200KB',
                'tooSmall' => '{attribute}最小不能小于0.1KB',
                'notImage' => '{file} 不是图片文件'
            ],
            
            [
                'photos',
                'image',
                'maxFiles' => 2,
                'extensions' => 'jpg, png',
                'mimeTypes' => 'image/jpeg, image/png',
                'checkExtensionByMimeType' => false,
                'minSize' => 10240,
                'maxSize' => 512000,
                'tooSmall' => '{attribute}的大小至少10KB。',
                'tooBig' => '{attribute}的大小不能超过500KB。',
                'notImage' => '{file} 不是图片文件',
                'skipOnEmpty' => false,
                'on' => 'updateImages',
            ],
    
            [['category_id', 'store_id', 'is_new', 'is_hot'], 'integer'],
            [['category_id'], 'exist', 'targetClass' => Category::className(), 'targetAttribute' => 'id'],
            
            [['store_id'], 'exist', 'targetClass' => Store::className(), 'targetAttribute' => 'id'],
            
            [['is_new', 'is_hot', 'is_promotion'], 'default', 'value' => self::BOOL_FALSE],
            [['is_new', 'is_hot', 'is_promotion'], 'in', 'range' => [self::BOOL_FALSE, self::BOOL_TRUE]],
    
            [['price', 'price_original', 'cost'], 'number', 'min' => 0],
            
            [['surplus'], 'integer', 'min' => 0, 'max' => 999, 'on' => 'insert'],
            [['surplus'], 'default', 'value' => 0, 'on' => 'insert'],
            
            [['sales'], 'integer', 'min' => 0],
            [['sales', 'cost'], 'default', 'value' => 0],
    
            [['name'], 'string', 'max' => 60],
            [['unit'], 'string', 'max' => 10],
            
            ['description', 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels ()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'category_id' => '分类',
            'store_id' => '营业点',
            'cover' => '封面图片',
            'image' => '封面图片',
            'photos' => '商品图片',
            'price' => '现价',
            'price_original' => '原价',
            'cost' => '成本价',
            'description' => '描述',
            'status' => '商品状态',
            'surplus' => '库存',
            'sales' => '月售',
            'unit' => '数量单位',
            'is_new' => '是否最新商品',
            'is_hot' => '是否热门商品',
            'is_promotion' => '是否促销商品',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(GoodsImg::className(), ['goods_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurpluses()
    {
        return $this->hasMany(GoodsSurplus::className(), ['goods_id' => 'id']);
    }
    
    /**
     * 调整库存
     * 
     * @param integer $number
     * @param string $remark
     * @throws \Exception
     * @return boolean
     */
    public function moveSurplus($number, $remark)
    {
        $number = (int) $number;
        $surplusOriginal = $this->surplus;
        $this->surplus += $number;
        
        if ($this->surplus < 0) {
            $this->surplus = 0;
        }
        
        if ($surplusOriginal == $this->surplus) {
            return true;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
        
            if (!$this->save(false)) {
                throw new \Exception('商品错误！');
            }
        
            $goodsSurplus = new GoodsSurplus();
            $goodsSurplus->goods_id = $this->id;
            $goodsSurplus->surplus_before = $surplusOriginal;
            $goodsSurplus->amount = $number;
            $goodsSurplus->surplus_after = $this->surplus;
            $goodsSurplus->remark = $remark;
            
            if (!$goodsSurplus->save(false)) {
                throw new \Exception('商品库存记录失败！');
            }
        
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public static function getStatusList()
    {
        if (self::$_statusList === null) {
            self::$_statusList = [
                self::STATUS_NORMAL => '正常',
                self::STATUS_OFF_SHELVES => '下架',
                self::STATUS_DELETED => '已删除'
            ];
        }
        
        return self::$_statusList;
    }

    public function getStatusMsg()
    {
        $list = self::getStatusList();
        
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }

    public static function getIsNewList()
    {
        if (self::$_isNewList === null) {
            self::$_isNewList = [
                self::BOOL_TRUE => '新',
                self::BOOL_FALSE => '非新'
            ];
        }
        
        return self::$_isNewList;
    }

    public function getIsNewMsg()
    {
        $list = self::getIsNewList();
        
        return isset($list[$this->is_new]) ? $list[$this->is_new] : null;
    }

    public static function getIsHotList()
    {
        if (self::$_isHotList === null) {
            self::$_isHotList = [
                self::BOOL_TRUE => '热门',
                self::BOOL_FALSE => '非热门'
            ];
        }
        
        return self::$_isHotList;
    }

    public function getIsHotMsg()
    {
        $list = self::getIsHotList();
        
        return isset($list[$this->is_hot]) ? $list[$this->is_hot] : null;
    }
    
    public static function getIsPromotionList()
    {
        if (self::$_isPromotionList === null) {
            self::$_isPromotionList = [
                self::BOOL_TRUE => '促销',
                self::BOOL_FALSE => '非促销'
            ];
        }
    
        return self::$_isPromotionList;
    }
    
    public function getIsPromotionMsg()
    {
        $list = self::getIsPromotionList();
    
        return isset($list[$this->is_promotion]) ? $list[$this->is_promotion] : null;
    }
}
