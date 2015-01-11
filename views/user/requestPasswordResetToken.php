<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\PasswordResetRequestForm */

$this->title = \Yii::t('app', 'Request Password Reset');
?>
<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= \Yii::t('app', 'Please fill out your email. A link to reset password will be sent there.'); ?></p>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'request-password-reset-form',
            'layout' => 'horizontal',
        ]); ?>
        <?= $form->field($model, 'email')->textInput(); ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::submitButton(\Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>