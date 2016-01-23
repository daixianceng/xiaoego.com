<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%store}}".
 *
 * @property string $id
 * @property string $name
 * @property string $school_id
 * @property string $address
 * @property string $cellphone
 * @property string $telephone
 * @property string $hours
 * @property string $has_book
 * @property string $has_down
 * @property string $has_gift
 * @property string $has_least
 * @property string $down_upper
 * @property string $down_val
 * @property string $gift_upper
 * @property string $gift_val
 * @property string $least_val
 * @property string $notice
 * @property string $status
 * @property string $layout
 * @property string $enable_sms
 * @property string $auto_toggle
 * @property string $toggle_type
 * @property string $created_at
 * @property string $updated_at
 * @property string $typeMsg read-only $typeMsg
 * @property string $statusMsg read-only $statusMsg
 * @property string $downMsg read-only $downMsg
 * @property string $giftMsg read-only $giftMsg
 * @property integer $goodsCount read-only $goodsCount
 */
class Store extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_REST = 'rest';
    const STATUS_DISABLED = 'disabled';
    
    const LAYOUT_MERGER = 'merger';
    const LAYOUT_OPEN = 'open';
    
    const TOGGLE_TYPE_ACTIVE = 'active';
    const TOGGLE_TYPE_REST = 'rest';
    const TOGGLE_TYPE_BOTH = 'both';

    private static $_statusList;
    private static $_layoutList;
    private static $_toggleTypeList;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%store}}';
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
            [['name', 'address', 'cellphone', 'telephone', 'hours', 'notice', 'gift_val'], 'trim'],
            [['name', 'school_id', 'address', 'cellphone'], 'required'],
            
            [['school_id'], 'integer'],
            [['school_id'], 'exist', 'targetClass' => School::className(), 'targetAttribute' => 'id'],
            
            [['layout'], 'default', 'value' => self::LAYOUT_MERGER],
            [['layout'], 'in', 'range' => [self::LAYOUT_MERGER, self::LAYOUT_OPEN]],
            
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_REST, self::STATUS_DISABLED]],
            
            [['cellphone'], 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/'],
            
            [['name', 'cellphone', 'telephone'], 'string', 'max' => 20],
            [['hours', 'gift_val'], 'string', 'max' => 60],
            [['address', 'notice'], 'string', 'max' => 255],
            
            [['enable_sms'], 'default', 'value' => '1'],
            [['has_book', 'has_down', 'has_gift', 'has_least', 'down_upper', 'down_val', 'gift_upper', 'auto_toggle', 'least_val'], 'default', 'value' => '0'],
            [['has_book', 'has_down', 'has_gift', 'has_least', 'auto_toggle', 'enable_sms'], 'boolean'],

            [['toggle_type'], 'default', 'value' => self::TOGGLE_TYPE_ACTIVE],
            [['toggle_type'], 'in', 'range' => [self::TOGGLE_TYPE_ACTIVE, self::TOGGLE_TYPE_REST, self::TOGGLE_TYPE_BOTH]],

            [['down_upper', 'gift_upper'], 'integer', 'max' => 10000],
            [['down_val', 'least_val'], 'number', 'max' => 99],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商店名称',
            'school_id' => '学校',
            'type' => '类型',
            'address' => '地址',
            'cellphone' => '手机',
            'telephone' => '电话',
            'notice' => '公告',
            'hours' => '营业时间',
            'has_book' => '支持预定',
            'has_down' => '满减优惠',
            'has_gift' => '满送优惠',
            'has_least' => '最低起送',
            'down_upper' => '满减优惠价',
            'down_val' => '满减优惠金额',
            'gift_upper' => '满送优惠价',
            'gift_val' => '满送优惠礼品',
            'least_val' => '起送价',
            'status' => '状态',
            'layout' => '页面布局',
            'enable_sms' => '启用短信',
            'auto_toggle' => '自动切换营业状态',
            'toggle_type' => '切换类型',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(Member::className(), ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchool()
    {
        return $this->hasOne(School::className(), ['id' => 'school_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoods($categoryId = null)
    {
        $query = $this->hasMany(Goods::className(), ['store_id' => 'id'])->where(['status' => Goods::STATUS_NORMAL]);
        
        if ($categoryId !== null) {
            $query->andWhere(['category_id' => $categoryId]);
        }
        
        return $query->orderBy(['is_promotion' => SORT_DESC, 'is_hot' => SORT_DESC, 'sales' => SORT_DESC, 'name' => SORT_ASC]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['store_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    public static function getStatusList()
    {
        if (self::$_statusList === null) {
            self::$_statusList = [
                self::STATUS_ACTIVE => '营业中',
                self::STATUS_REST => '休息中',
                self::STATUS_DISABLED => '禁用'
            ];
        }
        
        return self::$_statusList;
    }

    public function getStatusMsg()
    {
        $list = self::getStatusList();
        
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }
    
    public static function getLayoutList()
    {
        if (self::$_layoutList === null) {
            self::$_layoutList = [
                self::LAYOUT_MERGER => '合并',
                self::LAYOUT_OPEN => '展开'
            ];
        }
    
        return self::$_layoutList;
    }
    
    public function getLayoutMsg()
    {
        $list = self::getLayoutList();
    
        return isset($list[$this->layout]) ? $list[$this->layout] : null;
    }

    public static function getToggleTypeList()
    {
        if (self::$_toggleTypeList === null) {
            self::$_toggleTypeList = [
                self::TOGGLE_TYPE_ACTIVE => '仅每天早上8点开启营业',
                self::TOGGLE_TYPE_REST => '仅每天凌晨零点关闭营业',
                self::TOGGLE_TYPE_BOTH => '两者都有'
            ];
        }
    
        return self::$_toggleTypeList;
    }
    
    public function getToggleTypeMsg()
    {
        $list = self::getToggleTypeList();
    
        return isset($list[$this->toggle_type]) ? $list[$this->toggle_type] : null;
    }
    
    /**
     * 获取店铺所具有的商品分类
     * 
     * @return array
     */
    public function getGoodsCategories()
    {
        return Goods::find()->select('category_id')->where([
            'store_id' => $this->id,
            'status' => Goods::STATUS_NORMAL
        ])->distinct()->column();
    }
    
    public function getGoodsCount()
    {
        return Goods::find()->where([
            'store_id' => $this->id,
            'status' => Goods::STATUS_NORMAL
        ])->count();
    }
    
    public function getDownMsg()
    {
        return $this->has_down ? "满{$this->down_upper}减{$this->down_val}" : null;
    }
    
    public function getGiftMsg()
    {
        return $this->has_gift ? "满{$this->gift_upper}送{$this->gift_val}" : null;
    }
    
    public static function getKeyValuePairs($schoolId)
    {
        $sql = 'SELECT id, name FROM ' . self::tableName() . ' WHERE school_id=:school_id ORDER BY name ASC';
    
        return Yii::$app->db->createCommand($sql, [':school_id' => $schoolId])->queryAll(\PDO::FETCH_KEY_PAIR);
    }
}
