<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $username
 * @property string $real_name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $access_token
 * @property string $gender
 * @property string $email
 * @property string $mobile
 * @property string $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $statusMsg read-only $statusMsg
 * @property string $genderMsg read-only $genderMsg
 * @property string $password write-only password
 */
class Member extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';
    
    const GENDER_MALE = 'male';
    const GENDER_WOMAN = 'woman';
    const GENDER_OTHER = 'other';

    private static $_statusList;
    private static $_genderList;
    
    public $password;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['store_id', 'required'],
            ['store_id', 'integer'],
            ['store_id', 'exist', 'targetClass' => Store::className(), 'targetAttribute' => 'id'],

            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 4, 'max' => 20],
            ['username', 'match', 'pattern' => '/^[A-Za-z_-][A-Za-z0-9_-]+$/'],
            ['username', 'unique', 'message' => '该用户名已被使用'],

            ['real_name', 'filter', 'filter' => 'trim'],
            ['real_name', 'required'],
            ['real_name', 'string', 'min' => 2, 'max' => 20],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],

            ['password', 'required', 'on' => 'insert'],
            ['password', 'string', 'min' => 6, 'max' => 24],
            ['password', 'match', 'pattern' => '/^\S+$/'],

            ['gender', 'default', 'value' => self::GENDER_MALE],
            ['gender', 'in', 'range' => [self::GENDER_MALE, self::GENDER_WOMAN, self::GENDER_OTHER]],

            ['mobile', 'required'],
            ['mobile', 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '营业点',
            'username' => '用户名',
            'password' => '密码',
            'real_name' => '真实姓名',
            'gender' => '性别',
            'email' => '邮箱',
            'mobile' => '手机',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    public static function getGenderList()
    {
        if (self::$_genderList === null) {
            self::$_genderList = [
                self::GENDER_MALE => '男',
                self::GENDER_WOMAN => '女',
                self::GENDER_OTHER => '保密'
            ];
        }
    
        return self::$_genderList;
    }
    
    public function getGenderMsg()
    {
        $list = self::getGenderList();
    
        return isset($list[$this->gender]) ? $list[$this->gender] : null;
    }
    
    public static function getStatusList()
    {
        if (self::$_statusList === null) {
            self::$_statusList = [
                self::STATUS_ACTIVE => '正常',
                self::STATUS_BLOCKED => '禁用'
            ];
        }
    
        return self::$_statusList;
    }
    
    public function getStatusMsg()
    {
        $list = self::getStatusList();
    
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }
}
