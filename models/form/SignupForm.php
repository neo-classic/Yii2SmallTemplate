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
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот E-mail уже используется.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            [['firstName', 'lastName'], 'required'],
            [['firstName', 'lastName'], 'string', 'min' => 2, 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
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