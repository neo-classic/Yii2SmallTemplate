<?php
namespace app\modules\user;

class Module extends \yii\base\Module
{
    public $userTable = 'user';
    public $sendActivationEmail = true;

    public function init()
    {
        parent::init();
    }
} 