<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $mobile
 * @property string $nickname
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token
 * @property string $gender
 * @property string $email
 * @property string $auth_key
 * @property string $status
 * @property string $has_new_down
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $name read-only $name
 * @property string $symbolMobile read-only $symbolMobile
 * @property string $statusMsg read-only $statusMsg
 * @property string $genderMsg read-only $genderMsg
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';

    const GENDER_MALE = 'male';
    const GENDER_WOMAN = 'woman';
    const GENDER_OTHER = 'other';
    
    private static $_statusList;
    private static $_genderList;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['nickname'], 'trim'],
            [['nickname'], 'required'],
            [['nickname'], 'string', 'length' => [2, 20]],

            [['gender'], 'required'],
            [['gender'], 'in', 'range' => [self::GENDER_MALE, self::GENDER_OTHER, self::GENDER_WOMAN]]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机',
            'nickname' => '昵称',
            'gender' => '性别',
            'email' => '邮箱',
            'status' => '状态',
            'has_new_down' => '是否还有新用户立减优惠资格',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['user_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress($schoolId = null)
    {
        $query = $this->hasMany(Address::className(), ['user_id' => 'id']);
        
        if ($schoolId !== null) {
            $query->where(['school_id' => $schoolId]);
        }
        
        return $query->orderBy('school_id ASC, building_id ASC, is_default DESC');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCartGoods($storeId = null)
    {
        $query = $this->hasMany(CartGoods::className(), ['user_id' => 'id']);
        
        if ($storeId !== null) {
            $query->where(['store_id' => $storeId]);
        }
        
        return $query;
    }
    
    /**
     * 获取购物车中商品种类数量
     * 
     * @param string $storeId
     * @return integer
     */
    public function getCartGoodsCount($storeId = null)
    {
        return (int) $this->getCartGoods($storeId)->count();
    }
    
    /**
     * 获取购物车中商品数量
     * 
     * @param string $storeId
     * @return integer
     */
    public function getCartGoodsLength($storeId = null)
    {
        return (int) $this->getCartGoods($storeId)->sum('count');
    }
    
    /**
     * 获取购物车中商品总价
     * 
     * @param string $storeId
     * @return string
     */
    public function getCartGoodsVolume($storeId = null)
    {
        return bcadd($this->getCartGoods($storeId)->sum('count * price'), '0', 2);
    }
    
    /**
     * 获取购物车中某个商品的数量
     * 
     * @param string $goodsId
     * @return integer
     */
    public function getCartGoodsQuantity($goodsId)
    {
        $quantity = (int) CartGoods::find()->select('count')->where([
            'goods_id' => $goodsId,
            'user_id' => $this->id
        ])->scalar();
        
        return $quantity;
    }
    
    /**
     * 获取购物车中有效商品种类数量
     *
     * @param string $storeId
     * @return integer
     */
    public function getCartGoodsRealCount($storeId)
    {
        $sql = 'SELECT count(*) FROM ' . CartGoods::tableName() . ' AS t0 LEFT JOIN ' . Goods::tableName() .
               ' AS t1 ON t0.goods_id = t1.id WHERE t0.user_id=:user_id AND t0.store_id=:store_id AND t1.status=:status;';
        
        $count = Yii::$app->db->createCommand($sql, [
            ':user_id' => $this->id,
            ':store_id' => $storeId,
            ':status' => Goods::STATUS_NORMAL
        ])->queryScalar();
        
        return (int) $count;
    }
    
    /**
     * 获取购物车中有效商品数量
     *
     * @param string $storeId
     * @return integer
     */
    public function getCartGoodsRealLength($storeId)
    {
        $sql = 'SELECT sum(t0.count) FROM ' . CartGoods::tableName() . ' AS t0 LEFT JOIN ' . Goods::tableName() .
               ' AS t1 ON t0.goods_id = t1.id WHERE t0.user_id=:user_id AND t0.store_id=:store_id AND t1.status=:status;';
        
        $length = Yii::$app->db->createCommand($sql, [
            ':user_id' => $this->id,
            ':store_id' => $storeId,
            ':status' => Goods::STATUS_NORMAL
        ])->queryScalar();
        
        return (int) $length;
    }
    
    /**
     * 获取购物车中有效商品总价
     * 
     * @param string $storeId
     * @return string
     */
    public function getCartGoodsRealVolume($storeId)
    {
        $sql = 'SELECT sum(t0.count * t1.price) FROM ' . CartGoods::tableName() . ' AS t0 LEFT JOIN ' . Goods::tableName() .
                ' AS t1 ON t0.goods_id = t1.id WHERE t0.user_id=:user_id AND t0.store_id=:store_id AND t1.status=:status;';
        $volume = Yii::$app->db->createCommand($sql, [
            ':user_id' => $this->id,
            ':store_id' => $storeId,
            ':status' => Goods::STATUS_NORMAL,
        ])->queryScalar();
        
        return bcadd($volume, '0', 2);
    }
    
    /**
     * 获取用户某个时间段购买某个商品的数量
     * 
     * @param string $goodsId
     * @param string $timeStart
     * @param string $timeEnd
     * @return integer
     */
    public function getOrderGoodsCount($goodsId, $timeStart = null, $timeEnd = null)
    {
        $sql = 'SELECT sum(t0.count) FROM ' . OrderGoods::tableName() . ' AS t0 LEFT JOIN ' . Order::tableName() .
               ' AS t1 ON t0.order_id = t1.id WHERE t1.user_id=:user_id AND t0.goods_id=:goods_id';
        $params = [
            ':user_id' => $this->id,
            ':goods_id' => $goodsId
        ];
        
        if ($timeStart !== null) {
            $sql .= ' AND t1.created_at >= :time_start';
            $params[':time_start'] = $timeStart;
        }
        if ($timeEnd !== null) {
            $sql .= ' AND t1.created_at < :time_end';
            $params[':time_end'] = $timeEnd;
        }
        $count = Yii::$app->db->createCommand($sql, $params)->queryScalar();
        
        return (int) $count;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(UserAccount::className(), ['id' => 'id']);
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->nickname === null ? $this->mobile : $this->nickname;
    }
    
    /**
     * @return string
     */
    public function getSymbolMobile()
    {
        return substr_replace($this->mobile, '****', 3, 4);
    }
    
    /**
     * 清空购物车商品
     * 
     * @param string $storeId
     * @return integer the number of rows deleted
     */
    public function clearCartGoods($storeId = null)
    {
        $condition = ['user_id' => $this->id];
        if ($storeId !== null) {
            $condition['store_id'] = $storeId;
        }
        
        return CartGoods::deleteAll($condition);
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
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by mobile
     *
     * @param string $mobile
     * @return static|null
     */
    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
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

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    
    /**
     * Generates access token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * Removes access token
     */
    public function removeAccessToken()
    {
        $this->access_token = null;
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
        $list = static::getGenderList();
    
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
        $list = static::getStatusList();
    
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }
}