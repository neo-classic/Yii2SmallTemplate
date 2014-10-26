<?php
use yii\bootstrap\Alert;

if(\Yii::$app->session->hasFlash('success')) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success',
        ],
        'body' => \Yii::$app->session->getFlash('success'),
    ]);
}
?>

<?php
if(\Yii::$app->session->hasFlash('error')) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-error',
        ],
        'body' => \Yii::$app->session->getFlash('error'),
    ]);
}
?>