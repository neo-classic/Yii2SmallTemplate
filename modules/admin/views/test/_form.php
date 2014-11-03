<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Test */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="test-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => 50]) ?>

    <?php
    echo $form->field($model, 'logo')->fileInput();
    if ($model->logo) {
        echo '<div class="form-group">';
        echo Html::img($model->getImageUrl(\app\modules\admin\models\Test::LOGO_FIELD), ['class' => 'img-thumbnail', 'style' => 'width: 200px;']);
        echo "</div>";
    }
    ?>

    <?php
    echo $form->field($model, 'image')->fileInput();
    if ($model->image) {
        echo '<div class="form-group">';
        echo Html::img($model->getImageUrl(\app\modules\admin\models\Test::IMAGE_FIELD), ['class' => 'img-thumbnail', 'style' => 'width: 200px;']);
        echo "</div>";
    }
    ?>

    <?php
    echo $form->field($model, 'imageArray[]')->fileInput(['multiple' => '']);

    $images = $model->getImages();
    if (!empty($images)) {
        echo $this->render('_imageGrid', ['model' => $model]);
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>