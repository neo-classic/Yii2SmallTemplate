<?php
use yii\grid\GridView;
use yii\helpers\Html;

$images = new \yii\data\ActiveDataProvider([
    'query' => \app\modules\admin\models\TestImage::find()->where(['test_id' => $model->id]),
]);

echo Html::submitButton('Удалить фото', ['class' => 'btn btn-danger btn-xs pull-right']);

\yii\widgets\Pjax::begin(['id' => 'image-grid']);
echo GridView::widget([
    'dataProvider' => $images,
    'layout' => '{items} {pager}',
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'name' => 'massImageDelete',
        ], [
            'header' => 'Изображение',
            'format' => 'raw',
            'value' => function ($data) use($model) {
                return Html::img($model->getMultiFileUrl() . $data->file, ['class' => 'img-polaroid', 'height' => '100']);
            }
        ],
        'file',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    $url = \yii\helpers\Url::to(['delete-image', 'id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['class' => 'confirmLink', 'data-grid' => 'image-grid']);
                },
            ],
        ],
    ],
]);
\yii\widgets\Pjax::end();

echo Html::submitButton('Удалить фото', ['class' => 'btn btn-danger btn-xs pull-right']);