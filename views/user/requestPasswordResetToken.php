<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\PasswordResetRequestForm */

$this->title = \Yii::t('app', 'Request Password Reset');
?>
<div class="site-request-password-reset">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-2">
            <h1><?= Html::encode($this->title) ?></h1>
            <p><?= \Yii::t('app', 'Please fill out your email. A link to reset password will be sent there.'); ?></p>
        </div>
    </div>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'request-password-reset-form',
            'layout' => 'horizontal',
        ]); ?>
        <?= $form->field($model, 'email')->textInput(); ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6 text-center">
                <?= Html::submitButton("<i class='fa fa-envelope-o'></i> ".\Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>