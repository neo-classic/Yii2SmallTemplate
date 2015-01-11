<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\ChangePasswordForm */

$this->title = \Yii::t('app', 'Change Password');
?>
<div class="change-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
        ]); ?>
        <?= $form->field($model, 'oldPassword')->passwordInput(); ?>
        <?= $form->field($model, 'password')->passwordInput(); ?>
        <?= $form->field($model, 'repeatPassword')->passwordInput(); ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>