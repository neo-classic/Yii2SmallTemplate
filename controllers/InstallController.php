<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\User;
use app\rbac\ViewProjectRule;
use Yii;

class InstallController extends Controller
{
    public function actionIndex()
    {
        if (Yii::$app->db->schema->getTableSchema('user') !== null) {
            Yii::$app->response->redirect(['/site/index']);
        }

        $db = \Yii::$app->db;
        $db->createCommand(
            "CREATE TABLE `user` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `first_name` varchar(255) NULL,
              `last_name` varchar(255) NULL,
              `email` varchar(255) NOT NULL,
              `password` varchar(255) NOT NULL,
              `avatar` longblob DEFAULT NULL,
              `avatar_mime` varchar(4) DEFAULT NULL,
              `password_reset_token` varchar(255) DEFAULT NULL,
              `auth_key` varchar(255) NOT NULL,
              `status_id` tinyint(1) NOT NULL DEFAULT '1',
              `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `last_visit_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;"
        )->execute();
        $db->createCommand("
            CREATE TABLE auth (
                id int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id int(11) unsigned NOT NULL,
                source varchar(255) NOT NULL,
                source_id varchar(255) NOT NULL
            );
        ")->execute();
        $db->createCommand("
            ALTER TABLE auth ADD FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;
        ")->execute();
        echo "user table created...<br />";
        $this->userInit();
        echo "wiki table created...<br />";

        return $this->redirect(['/site/index']);
    }

    /**
     * @return User
     */
    private function userInit()
    {
        Yii::$app->db->createCommand('INSERT INTO user(email, password, first_name, last_name, auth_key) VALUES (:email, :pass, :fn, :ln, :ak)', [
            ':email' => 'admin@admin.com',
            ':pass' => User::getPassword('admin'),
            ':fn' => 'admin',
            ':ln' => 'admin',
            ':ak' => \Yii::$app->security->generateRandomString(),
        ])->execute();
        $this->rbacInit(Yii::$app->db->createCommand('SELECT id FROM user WHERE email="admin@admin.com"')->queryScalar());
    }

    private function rbacInit($adminId)
    {
        $auth = \Yii::$app->authManager;
        $auth->removeAll();
        // Доступ к админке
        $adminDashboard = $auth->getPermission('adminDashboard');
        if (!$adminDashboard) {
            $adminDashboard = $auth->createPermission('adminDashboard');
        }
        $auth->add($adminDashboard);

        // Добавляем роли
        $user = $auth->createRole(User::ROLE_USER);
        $user->description = User::getRoleArray()[User::ROLE_USER];
        $auth->add($user);

        $client = $auth->createRole(User::ROLE_CLIENT);
        $client->description = User::getRoleArray()[User::ROLE_CLIENT];
        $auth->add($client);

        $manager = $auth->createRole(User::ROLE_MANAGER);
        $manager->description = User::getRoleArray()[User::ROLE_MANAGER];;
        $auth->add($manager);

        $admin = $auth->createRole(User::ROLE_ADMIN);
        $admin->description = User::getRoleArray()[User::ROLE_ADMIN];
        $auth->add($admin);

        // Связываем роли
        $auth->addChild($client, $user);
        $auth->addChild($manager, $client);
        $auth->addChild($admin, $manager);

        // Назначаем доступы
        $auth->addChild($admin, $adminDashboard);

        $role = $auth->getRole(User::ROLE_ADMIN);
        $auth->assign($role, $adminId);
    }
}
