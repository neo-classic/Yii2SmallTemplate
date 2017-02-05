<?php
namespace app\models\form;

use app\models\User;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status_id' => User::STATUS_ACTIVE],
                'message' => \Yii::t('app', 'There is no user with such email.')
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('app', 'E-mail'),
        ];
    }


    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status_id' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                $resetLink = \Yii::$app->urlManager->createAbsoluteUrl(['/user/reset-password', 'token' => $user->password_reset_token]);
                return \Yii::$app->mailer->compose()
                    ->setTo($this->email)
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setSubject('Запрос на восстановление пароля')
                    ->setHtmlBody("<p>Добрый день, {$user->fullName}</p>
                        <p>Для восстановления пароля, перейдите по ссылке ниже:</p>
                        <p>" . Html::a(Html::encode($resetLink), $resetLink) . '</p>')
                    ->send();
            }
        }

        return false;
    }
}