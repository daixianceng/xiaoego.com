<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%address}}".
 *
 * @property string $id
 * @property string $consignee
 * @property string $cellphone
 * @property string $gender
 * @property string $school_id
 * @property string $building_id
 * @property string $room
 * @property string $user_id
 * @property string $is_default
 * @property string $created_at
 * @property string $updated_at
 * @property string $addressMsg read-only $addressMsg
 * @property string $genderMsg read-only $genderMsg
 */
class Address extends \yii\db\ActiveRecord
{
    const GENDER_MALE = 'male';
    const GENDER_WOMAN = 'woman';
    
    const BOOL_TRUE = 1;
    const BOOL_FALSE = 0;
    
    private static $_genderList;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address}}';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->isNewRecord) {
            $this->gender = self::GENDER_MALE;
        }
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
            [['consignee', 'cellphone', 'room'], 'trim'],
            [['consignee', 'cellphone', 'school_id', 'building_id', 'room'], 'required'],
            [['consignee'], 'string', 'max' => 12],
    
            [['cellphone'], 'match', 'pattern' => '/^(\+86\s?)?1[3|5|7|8|][0-9]{9}$/'],
            
            [['gender'], 'required'],
            [['gender'], 'default', 'value' => self::GENDER_MALE],
            [['gender'], 'in', 'range' => [self::GENDER_MALE, self::GENDER_WOMAN]],
    
            [['school_id', 'building_id'], 'integer'],
            [['school_id'], 'exist', 'targetClass' => School::className(), 'targetAttribute' => 'id'],
            [['building_id'], 'exist', 'targetClass' => Building::className(), 'targetAttribute' => 'id', 'filter' => function ($query) {
                return $query->andWhere(['school_id' => $this->school_id]);
            }],
    
            [['room'], 'string', 'length' => [2,12]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'consignee' => '收货人',
            'cellphone' => '手机',
            'gender' => '性别',
            'school_id' => '学校',
            'building_id' => '建筑',
            'room' => '房间',
            'user_id' => '用户',
            'is_default' => '是否默认',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    public function getAddressMsg()
    {
        $school = $this->school ? $this->school->name : '';
        $building = $this->building ? $this->building->name : '';
        
        return "$school $building {$this->room}";
    }
    
    public function getIsDefault()
    {
        return $this->is_default === self::BOOL_TRUE;
    }

    public function __toString()
    {
        return $this->consignee . ' (' . $this->genderMsg . ') ' . $this->cellphone . ' ' . $this->addressMsg;
    }
    
    public static function getGenderList()
    {
        if (self::$_genderList === null) {
            self::$_genderList = [
                self::GENDER_MALE => '帅哥',
                self::GENDER_WOMAN => '美女'
            ];
        }
    
        return self::$_genderList;
    }
    
    public function getGenderMsg()
    {
        $list = self::getGenderList();
    
        return isset($list[$this->gender]) ? $list[$this->gender] : null;
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            
            if ($this->is_default == self::BOOL_TRUE) {
                // Update all adresses to non-default.
                static::updateAll(['is_default' => self::BOOL_FALSE], ['user_id' => $this->user_id]);
            }
            
            return true;
        } else {
            return false;
        }
    }
}
