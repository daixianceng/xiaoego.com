<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%order_address}}".
 *
 * @property string $id
 * @property string $order_id
 * @property string $consignee
 * @property string $cellphone
 * @property string $gender
 * @property string $school
 * @property string $building
 * @property string $room
 * @property string $addressMsg read-only $addressMsg
 * @property string $genderMsg read-only $genderMsg
 */
class OrderAddress extends \yii\db\ActiveRecord
{
    const GENDER_MALE = 'male';
    const GENDER_WOMAN = 'woman';
    
    private static $_genderList;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_address}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单',
            'consignee' => '收货人',
            'cellphone' => '手机',
            'gender' => '性别',
            'school' => '学校',
            'building' => '宿舍喽',
            'room' => '宿舍号'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    
    public function getAddressMsg()
    {
        return "{$this->school} {$this->building} {$this->room}";
    }
    
    public static function createDuplicate($addressId)
    {
        $address = Address::findOne($addressId);
        
        $model = new self();
        $model->consignee = $address->consignee;
        $model->cellphone = $address->cellphone;
        $model->gender = $address->gender;
        $model->school = $address->school->name;
        $model->building = $address->building->name;
        $model->room = $address->room;
        
        return $model;
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
    
    public function __toString()
    {
        return $this->consignee . ' (' . $this->genderMsg . ') ' . $this->cellphone . ' ' . $this->addressMsg;
    }
}
