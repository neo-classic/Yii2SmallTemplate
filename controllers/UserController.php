<?php
namespace app\controllers;

use app\components\CommonController;
use app\models\Auth;
use app\models\form\ChangePasswordForm;
use app\models\form\LoginForm;
use app\models\form\PasswordResetRequestForm;
use app\models\form\ResetPasswordForm;
use app\models\form\SignupForm;
use app\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

class UserController extends CommonController
{
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'oAuthSuccess'],
            ],
        ];
    }

    /**
     * This function will be triggered when user is successfuly authenticated using some oAuth client.
     *
     * @param yii\authclient\ClientInterface $client
     * @return boolean|yii\web\Response
     */
    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();
        /* @var $auth Auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // авторизация
                $user = $auth->user;
                Yii::$app->user->login($user);
            } else { // регистрация
                if (isset($attributes['email']) && User::find()->where(['email' => $attributes['email']])->exists()) {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', "Пользователь с такой электронной почтой как в {client} уже существует, но с ним не связан. Для начала войдите на сайт использую электронную почту, для того, что бы связать её.", ['client' => $client->getTitle()]),
                    ]);
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
                    $userAttrs = [
                        'email' => $attributes['email'] ?? $attributes['emails'][0]['value'] ?? $attributes['default_email'] ?? '',
                        'password' => $password,
                    ];

                    if ($client->getId() == 'facebook') {
                        $userAttrs['first_name'] = explode(' ', $attributes['name'])[0];
                        $userAttrs['last_name'] = explode(' ', $attributes['name'])[1];
                        $userAttrs['avatar'] = file_get_contents("https://graph.facebook.com/{$attributes['id']}/picture?type=large");
                        $userAttrs['avatar_mime'] = 'jpg';
                    }
                    if ($client->getId() == 'google') {
                        $userAttrs['first_name'] = $attributes['name']['givenName'];
                        $userAttrs['last_name'] = $attributes['name']['familyName'];
                        $userAttrs['avatar'] = file_get_contents(str_replace('sz=50', 'sz=300', $attributes['image']['url']));
                        $userAttrs['avatar_mime'] = 'jpg';
                    }
                    if ($client->getId() == 'yandex') {
                        $userAttrs['first_name'] = $attributes['first_name'];
                        $userAttrs['last_name'] = $attributes['last_name'];
                    }
                    if ($client->getId() == 'twitter') {
                        $userAttrs['first_name'] = explode(' ', $attributes['name'])[1];
                        $userAttrs['last_name'] = explode(' ', $attributes['name'])[0];
                        $userAttrs['email'] = $attributes['screen_name'] . '@twitter.com';
                        $userAttrs['avatar'] = file_get_contents($attributes['profile_image_url_https']);
                        $au = explode('.', $attributes['profile_image_url_https']);
                        $fileMime = array_pop($au);
                        $userAttrs['avatar_mime'] = $fileMime;
                    }
                    if ($client->getId() == 'vkontakte') {
                        $userAttrs['first_name'] = $attributes['first_name'];
                        $userAttrs['last_name'] = $attributes['last_name'];
                        $userAttrs['email'] = $attributes['screen_name'] . '@vk.com';
                        $userAttrs['avatar'] = file_get_contents($attributes['photo']);
                        $au = explode('.', $attributes['photo']);
                        $fileMime = array_pop($au);
                        $userAttrs['avatar_mime'] = $fileMime;
                    }


                    $user = new User($userAttrs);
                    $user->generateAuthKey();
                    $user->generatePasswordResetToken();
                    $transaction = $user->getDb()->beginTransaction();
                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $client->getId(),
                            'source_id' => (string)$attributes['id'],
                        ]);
                        if ($auth->save()) {
                            $auth = \Yii::$app->authManager;
                            $role = $auth->getRole(User::ROLE_USER);
                            $auth->assign($role, $user->id);
                            $transaction->commit();
                            Yii::$app->user->login($user);
                        } else {
                            print_r($auth->getErrors());
                        }
                    } else {
                        print_r($user->getErrors());
                    }
                }
            }
        } else { // Пользователь уже зарегистрирован
            if (!$auth) { // добавляем внешний сервис аутентификации
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $client->getId(),
                    'source_id' => $attributes['id'],
                ]);
                $auth->save();
            }
        }
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(\Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (\Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'Check your email for further instructions.'));

                return $this->goHome();
            } else {
                \Yii::$app->getSession()->setFlash('error', \Yii::t('app', 'Sorry, we are unable to reset password for email provided.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(\Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'New password was saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionProfile()
    {
        $model = $this->loadModel('app\models\User', \Yii::$app->user->id);
        return $this->render('profile', ['model' => $model]);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate() && $model->saveNewPassword()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'New password was saved.'));

            return $this->redirect(['/user/profile']);
        }

        return $this->render('changePassword', ['model' => $model]);
    }
}