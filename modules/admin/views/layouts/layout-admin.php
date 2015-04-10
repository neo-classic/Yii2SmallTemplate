<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php $this->registerJsFile('/js/admin.main.js', ['position' => \yii\web\View::POS_END]); ?>
</head>
<body>

<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Админка',
        'brandUrl' => Yii::$app->homeUrl,
        'innerContainerOptions' => [
            'class' => 'container-fluid',
        ],
    ]);
    echo Nav::widget([
        'encodeLabels' => false,
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => '<i class="fa fa-comment"></i> Home', 'url' => ['/site/index']],
            [
                'label' => 'Dropdown',
                'items' => [
                    ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Dropdown Header</li>',
                    ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
                ],
            ],
        ],
    ]);
    echo Nav::widget([
        'encodeLabels' => false,
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => '<i class="fa fa-angellist text-primary"></i> Тест', 'url' => ['/admin/test']],
            ['label' => '<i class="fa fa-users text-success"></i> Пользователи', 'url' => ['/admin/user']],
            ['label' => '<i class="fa fa-share text-primary"></i> На сайт', 'url' => \Yii::$app->homeUrl],
            Yii::$app->user->isGuest ?
                ['label' => 'Login', 'url' => ['/user/login']] :
                ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/user/logout'],
                    'linkOptions' => ['data-method' => 'post']],
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container-fluid">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <div class="row">
            <div class="<?php if (!empty($this->menu)): ?>col-lg-2 col-sm-4 col-md-3 col-xs-4<?php else:?>hide<?php endif; ?>" id="sidebar">
                <?php
                if (isset($this->menu)) {
                    echo Nav::widget([
                        'items' => $this->menu,
                        'encodeLabel' => false,
                    ]);
                }
                ?>
            </div>
            <div class="<?php if (!empty($this->menu)): ?>col-lg-10 col-sm-8 col-md-9 col-xs-8<?php else:?>col-sm-12<?php endif; ?>">
                <?php
                echo $this->render('@app/views/common/_flashMessage');
                echo $content;
                ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
