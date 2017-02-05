<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\form\LoginForm */

$this->title = \Yii::t('app', 'Login');
?>
<div class="site-login">
    <div class="row">
        <div class="col-sm-9 col-sm-offset-3">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
        ]);
        echo $form->errorSummary($model);
        ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rememberMe')->checkbox(); ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= \Yii::t('app', 'If you forgot your password you can') .' '. Html::a(\Yii::t('app', 'reset it'), ['/user/request-password-reset']) ?>.
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 text-center">
                <?= Html::submitButton('<i class="fa fa-sign-in"></i> ' . \Yii::t('app', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
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