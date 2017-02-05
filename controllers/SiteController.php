<?php

namespace app\controllers;

use app\components\CommonController;
use app\models\form\ContactForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SiteController extends CommonController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->db->schema->getTableSchema('user') === null) {
            Yii::$app->response->redirect(['/install/index']);
        }
        return $this->render('index');
    }

    public function actionContact()
    {
        $model = new ContactForm();

        if (!Yii::$app->user->isGuest) {
            $model->name = Yii::$app->user->identity->fullName;
        }
        $this->setSeo([
            'h1' => 'Форма обратной связи',
            'title' => 'Обратная связь',
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
