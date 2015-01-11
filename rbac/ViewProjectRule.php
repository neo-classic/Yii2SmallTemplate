<?php
namespace app\rbac;

use yii\helpers\ArrayHelper;
use yii\rbac\Rule;

class ViewProjectRule extends Rule
{
    public $name = 'isCreator';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['project'])
            ? ($params['project']->creator_id == $user
                || $params['project']->is_public == 1
                || \Yii::$app->authManager->checkAccess($user, 'adminDashboard')
                || in_array($user, ArrayHelper::getColumn($params['project']->members, 'user_id'))
            )
            : false;
    }
}