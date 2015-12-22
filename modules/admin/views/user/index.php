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
            'username',
            'email:email',
            'first_name',
            'last_name',
            [
                'attribute' => 'role',
                'filter' => User::getRoleArray(),
                'value' => function($data) {
                    $roles = [];
                    foreach ($data->role as $k => $role) {
                        $roles[] = $role->description;
                    }
                    return implode(', ', $roles);
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

<style type="text/css">
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(1) {
        width: 40px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(7) {
        width: 65px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(8) {
        width: 150px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(9) {
        width: 60px;
    }
    #w0 > table > thead > tr:nth-child(1) > th:nth-child(6) {
        width: 130px;
    }
</style>