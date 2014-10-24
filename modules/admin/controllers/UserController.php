<?php
namespace app\modules\admin\controllers;

use app\models\User;
use app\modules\admin\components\AdminController;
use yii\data\ActiveDataProvider;

class UserController extends AdminController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}