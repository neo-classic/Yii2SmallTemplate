<?php
namespace app\modules\admin\controllers;

use app\models\User;
use app\models\search\UserSearch;
use app\modules\admin\components\AdminController;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class UserController extends AdminController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new User();

        if ($model->load(\Yii::$app->request->post())) {
            $model->setPassword($model->password);
            $model->generateAuthKey();
            if ($model->save()) {
                if (isset($model->role)) {
                    $auth = \Yii::$app->authManager;
                    $role = $auth->getRole($model->role);
                    $auth->assign($role, $model->id);
                }
                \Yii::$app->session->setFlash('success', 'Пользователь ' . $model->username . ' успешно добавлен');
                return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->password != $model->old_password_hash) {
                $model->setPassword($model->password);
            }
            if ($model->save()) {
                if (isset($model->role)) {
                    $auth = \Yii::$app->authManager;
                    $role = $auth->getRole($model->role);
                    $auth->revokeAll($model->id);
                    $auth->assign($role, $model->id);
                }
                \Yii::$app->session->setFlash('success', 'Пользователь ' . $model->username . ' успешно обновлен');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}