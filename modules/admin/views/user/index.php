<?php

use app\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'role',
                'filter' => User::getRoleArray(),
                'value' => function($model) {
                    return User::getRoleArray()[$model->role];
                }
            ], [
                'attribute' => 'status_id',
                'filter' => User::getStatusArray(),
                'value' => function($model) {
                    return User::getStatusArray()[$model->status_id];
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

<style type="text/css">
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(1) {
        width: 40px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(5) {
        width: 65px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(6) {
        width: 150px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(7) {
        width: 60px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(4) {
        width: 130px;
    }
</style>