<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\form\SignupForm */

$this->title = \Yii::t('app', 'Signup');
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'form-signup',
            'layout' => 'horizontal',
        ]);
        ?>
        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'firstName') ?>
        <?= $form->field($model, 'lastName') ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::submitButton(\Yii::t('app', 'Send'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>