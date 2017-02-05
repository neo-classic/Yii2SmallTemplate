<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\form\ContactForm */

$this->title = $this->context->pageTitle;
?>
<div class="site-contact">
    <h1 class="text-center"><?= Html::encode($this->context->pageH1) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Спасибо за Ваше сообщение! Мы ответим Вам в ближайшее время.
        </div>

    <?php else: ?>

        <p class="text-center">
            Если у Вас возникли какие-нибудь вопросы или предложения, пожалуйста заполните форму ниже. Спасибо!
        </p>

        <div class="row">
            <?php $form = ActiveForm::begin([
                'id' => 'contact-form',
                'layout' => 'horizontal',
            ]); ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'subject') ?>
            <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
            ]) ?>
            <div class="form-group">
                <div class="col-sm-12 text-center">
                    <?= Html::submitButton('<i class="fa fa-check"></i> Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']); ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

    <?php endif; ?>
</div>