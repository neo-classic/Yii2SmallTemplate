<?php
namespace app\components;

use vova07\imperavi\actions\GetAction;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CommonController extends Controller
{
    public $layout = 'layout-main';

    public $pageTitle = 'yii2-sm';
    public $pageDescription = '';
    public $pageKeywords = '';
    public $pageH1 = 'yii2-sm';

    public function actions()
    {
        return [
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => Yii::$app->params['urlImg'] . 'uploads/',
                'path' => Yii::$app->params['absImgPath'] . '/uploads',
            ],
            'files-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => Yii::$app->params['urlImg'] . 'uploads/',
                'path' => Yii::$app->params['absImgPath'] . '/uploads',
                'type' => GetAction::TYPE_IMAGES,
            ],
        ];
    }

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

    public function setSeo(array $seo)
    {
        $this->pageTitle = $seo['title'] ?? $this->pageTitle;
        $this->pageDescription = $seo['description'] ?? $this->pageDescription;
        $this->pageKeywords = $seo['keywords'] ?? $this->pageKeywords;
        $this->pageH1 = $seo['h1'] ?? $this->pageH1;
    }
} 