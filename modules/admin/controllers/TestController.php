<?php
namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminController;
use app\modules\admin\models\Test;
use app\modules\admin\models\TestImage;
use yii\data\ActiveDataProvider;

class TestController extends AdminController
{

    public function actionIndex()
    {
        $models = new ActiveDataProvider([
            'query' => Test::find(),
        ]);

        return $this->render('index', ['models' => $models]);
    }

    public function actionCreate()
    {
        $model = new Test();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel('app\modules\admin\models\Test', $id);
        $this->massImageDelete();
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    private function massImageDelete() {
        if (\Yii::$app->request->post('massImageDelete')) {
            foreach (\Yii::$app->request->post('massImageDelete') as $imgId) {
                TestImage::findOne($imgId)->delete();
            }
        }
    }

    public function actionDelete($id)
    {
        $this->loadModel('app\modules\admin\models\Test', $id)->delete();
        return $this->redirect(['index']);
    }

    public function actionDeleteImage($id)
    {
        if (\Yii::$app->request->isAjax) {
            $this->loadModel('app\modules\admin\models\TestImage', $id)->delete();
        }
        \Yii::$app->end();
    }
}