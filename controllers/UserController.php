<?php
namespace app\controllers;

use app\components\CommonController;
use app\models\form\ChangePasswordForm;
use app\models\form\LoginForm;
use app\models\form\PasswordResetRequestForm;
use app\models\form\ResetPasswordForm;
use app\models\form\SignupForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

class UserController extends CommonController
{
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