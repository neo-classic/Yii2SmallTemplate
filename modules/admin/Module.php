<?php
namespace app\modules\admin;

use yii\web\ForbiddenHttpException;

class Module extends \yii\base\Module
{

    public function init()
    {
        parent::init();
        if (!\Yii::$app->user->can('adminDashboard')) {
            throw new ForbiddenHttpException('Access denied');
        }
    }
} 