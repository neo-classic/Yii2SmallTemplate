<?php

use app\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
?>
<div class="user-index">
    <p>
        <?= Html::a('<i class="fa fa-plus"></i> '.\Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'email:email',
            'first_name',
            'last_name',
            [
                'attribute' => 'role',
                'filter' => User::getRoleArray(),
                'value' => function($data) {
                    return User::getRoleArray()[$data->role];
                }
            ], [
                'attribute' => 'status_id',
                'filter' => User::getStatusArray(),
                'value' => function($data) {
                    return User::getStatusArray()[$data->status_id];
                }
            ],
            'created_date',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>