<?php
namespace app\components;

use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CommonController extends Controller
{
    public $layout = 'layout-main';

    public function loadModel($class, $id)
    {
        $model = call_user_func([$class, 'findOne'], [$id]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        }
    }

    public function loadModelByUrl($class, $url)
    {
        $model = call_user_func([$class, 'findOne'], ['url' => $url]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        }
    }
} 