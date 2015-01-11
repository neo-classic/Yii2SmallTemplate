<?php
namespace app\models\form;

use app\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $firstName;
    public $lastName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            [['firstName', 'lastName'], 'required'],
            [['firstName', 'lastName'], 'string', 'min' => 2, 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => \Yii::t('app', 'Username'),
            'email' => \Yii::t('app', 'E-mail'),
            'firstName' => \Yii::t('app', 'First Name'),
            'lastName' => \Yii::t('app', 'Last Name'),
            'password' => \Yii::t('app', 'Password'),
        ];
    }


    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->first_name = $this->firstName;
            $user->last_name = $this->lastName;
            $user->generateAuthKey();
            if ($user->save()) {
                $auth = \Yii::$app->authManager;
                $role = $auth->getRole(User::ROLE_USER);
                $auth->assign($role, $user->id);
            }
            return $user;
        }

        return null;
    }
}