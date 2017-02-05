<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\form\ResetPasswordForm */

$this->title = 'Восстановление пароля - Opinioner.ru';
?>
<div class="site-reset-password">
    <h1 class="text-center">Восстановление пароля</h1>

    <p class="text-center">Пожалуйста введите Ваш новый пароль:</p>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'reset-password-form',
            'layout' => 'horizontal'
        ]);
        ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>