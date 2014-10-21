<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\rbac\UserRoleRule;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные

        // Доступ к админке
        $adminDashboard = $auth->createPermission('adminDashboard');
        $auth->add($adminDashboard);

        // Обработчик ролей
        $rule = new UserRoleRule();
        $auth->add($rule);

        // Добавляем роли
        $user = $auth->createRole('user');
        $user->description = 'Пользователь';
        $user->ruleName = $rule->name;
        $auth->add($user);

        $client = $auth->createRole('client');
        $client->description = 'Клиент';
        $client->ruleName = $rule->name;
        $auth->add($client);

        $manager = $auth->createRole('manager');
        $manager->description = 'Менеджер';
        $manager->ruleName = $rule->name;
        $auth->add($manager);

        $admin = $auth->createRole('admin');
        $admin->description = 'Админ';
        $admin->ruleName = $rule->name;
        $auth->add($admin);

        // Связываем роли
        $auth->addChild($client, $user);
        $auth->addChild($manager, $client);
        $auth->addChild($admin, $manager);

        // Назначаем доступы
        $auth->addChild($manager, $adminDashboard);
    }
}