<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $updateTitle . ' ' . ($titleField ? $model->$titleField : '');
$this->params['breadcrumbs'][] = ['label' => $indexTitle, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['formController' => $formController, 'model' => $model]) ?>
</div>