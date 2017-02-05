<?php
namespace app\models;

use app\components\behaviors\ImageFromDbBehavior;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property resource $avatar
 * @property string $password_reset_token
 * @property string $auth_key
 * @property integer $status_id
 * @property integer $role
 * @property string $created_date
 * @property string $last_visit_date
 */

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const ROLE_USER = 'user';
    const ROLE_CLIENT = 'client';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMIN = 'admin';

    public $old_password_hash = '';
    public $role;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
            'imageFromDb' => [
                'class' => ImageFromDbBehavior::className(),
                'imageField' => 'avatar',
                'imageMimeField' => 'avatar_mime',
                'imageDir' => 'uploads',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['status_id'], 'integer'],
            [['created_date', 'last_visit_date', 'role'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['email', 'password', 'password_reset_token', 'auth_key', 'first_name', 'last_name'], 'string', 'max' => 255],

            [['avatar_mime'], 'string', 'max' => 4],
            [['avatar'], 'image', 'extensions' => 'jpg, jpeg, png, gif', 'maxWidth' => 1024, 'maxHeight' => 800],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'email' => \Yii::t('app', 'E-mail'),
            'status_id' => \Yii::t('app', 'Status'),
            'role' => \Yii::t('app', 'Role'),
            'created_date' => \Yii::t('app', 'Created Date'),
            'first_name' => \Yii::t('app', 'First Name'),
            'last_name' => \Yii::t('app', 'Last Name'),
            'password' => \Yii::t('app', 'Password'),
            'avatar' => 'Аватар',
        ];
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status_id' => self::STATUS_ACTIVE]);
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
        return static::findOne(['username' => $username, 'status_id' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email)
    {
        return static::findOne(['email' => $email, 'status_id' => self::STATUS_ACTIVE]);
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
            'status_id' => self::STATUS_ACTIVE,
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
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
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
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }
    
    public static function getPassword($password)
    {
        return \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = \Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function getRoleArray()
    {
        return [
            self::ROLE_USER => \Yii::t('app', 'User'),
            self::ROLE_CLIENT => \Yii::t('app', 'Client'),
            self::ROLE_MANAGER => \Yii::t('app', 'Manager'),
            self::ROLE_ADMIN => \Yii::t('app', 'Admin'),
        ];
    }

    public static function getStatusArray()
    {
        return [
            self::STATUS_DELETED => \Yii::t('app', 'Off'),
            self::STATUS_ACTIVE => \Yii::t('app', 'On'),
        ];
    }

    public function afterFind()
    {
        $this->old_password_hash = $this->password;
        if (isset(array_keys(\Yii::$app->authManager->getRolesByUser($this->id))[0])) {
            $this->role = array_keys(\Yii::$app->authManager->getRolesByUser($this->id))[0];
        }
        parent::afterFind();
    }

    public function getFullname($short = false)
    {
        if (empty($this->last_name) || empty($this->first_name)) {
            return $this->email;
        }
        if ($short) {
            return $this->last_name . ' ' . mb_substr($this->first_name, 0, 1).'.';
        }
        return $this->last_name . ' ' . $this->first_name;
    }

    public function beforeDelete()
    {
        \Yii::$app->authManager->revokeAll($this->id);
        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }

    public function getAvatarImg($size = '240x240')
    {
        if ($this->avatar) {
            return $this->getImage($this->getPrimaryKey(), $size);
        } else {
            return '/uploads/user/default.png';
        }
    }

    public function isTrueEmail()
    {
        $emailParts = explode('@', $this->email);
        return !in_array($emailParts[1], ['vk.com', 'twitter.com']);
    }

} 