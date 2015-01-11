<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = \Yii::t('app', 'User Profile') . ' ' . $model->username;

echo Html::a(\Yii::t('app', 'Change Password'), ['/user/change-password'], ['class' => 'pull-right btn btn-info m-b-10']);
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'username',
        'email',
        'first_name',
        'last_name',
    ],
]);