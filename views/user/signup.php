<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\form\SignupForm */

$this->title = \Yii::t('app', 'Signup');
?>
<div class="site-signup">
    <div class="row">
        <div class="col-sm-9 col-sm-offset-3">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'form-signup',
            'layout' => 'horizontal',
        ]);
        echo $form->errorSummary($model);
        ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'firstName') ?>
        <?= $form->field($model, 'lastName') ?>
        <div class="form-group">
            <div class="col-sm-12 text-center">
                <?= Html::submitButton("<i class='fa fa-check'></i> Зарегистрироваться", ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="row">
        <div class="col-sm-9 col-sm-offset-3">
            <?= yii\authclient\widgets\AuthChoice::widget([
                'baseAuthUrl' => ['user/auth']
            ]) ?>
        </div>
    </div>
</div>