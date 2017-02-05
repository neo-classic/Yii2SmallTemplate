<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
$topicUrl = Url::to(['/helper/topic-json']);
$locationText = '';
?>

<div class="user-form">

    <?php
    $form = ActiveForm::begin(['layout' => 'horizontal']);
    echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'status_id')->dropDownList($model->getStatusArray()); ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'first_name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'role')->dropDownList($model->getRoleArray()); ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton($model->isNewRecord ? \Yii::t('app', 'Add') : \Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat']); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
