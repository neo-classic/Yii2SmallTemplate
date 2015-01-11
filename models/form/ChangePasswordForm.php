<?php
namespace app\models\form;

use app\models\User;
use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    private $_user = false;

    public $oldPassword;
    public $password;
    public $repeatPassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oldPassword', 'password', 'repeatPassword'], 'required'],
            ['oldPassword', 'checkOldPasswords'],
            ['password', 'string', 'min' => 6],
            ['repeatPassword', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldPassword' => \Yii::t('app', 'Old Password'),
            'password' => \Yii::t('app', 'New Password'),
            'repeatPassword' => \Yii::t('app', 'Repeat New Password'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function checkOldPasswords($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, \Yii::t('app', 'Incorrect old password.'));
            }
        }
    }

    /**
     * Finds user by [[id]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(\Yii::$app->user->id);
        }

        return $this->_user;
    }

    public function saveNewPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);

        return $user->save();
    }
}