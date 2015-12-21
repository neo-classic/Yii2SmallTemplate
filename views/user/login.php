<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\LoginForm */

$this->title = \Yii::t('app', 'Login');
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
        ]); ?>
        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rememberMe')->checkbox(); ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= \Yii::t('app', 'If you forgot your password you can') .' '. Html::a(\Yii::t('app', 'reset it'), ['/user/request-password-reset']) ?>.
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::submitButton(\Yii::t('app', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="row">
        <?= yii\authclient\widgets\AuthChoice::widget([
            'baseAuthUrl' => ['user/auth']
        ]) ?>
    </div>
</div>