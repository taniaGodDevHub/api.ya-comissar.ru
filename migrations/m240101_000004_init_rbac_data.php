<?php

use yii\db\Migration;

/**
 * Инициализация базовых ролей и разрешений RBAC
 */
class m240101_000004_init_rbac_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Настройка authManager для консольного приложения
        $auth = new \yii\rbac\DbManager();
        $auth->db = Yii::$app->db;

        // Создание ролей
        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $auth->add($admin);

        $user = $auth->createRole('user');
        $user->description = 'Пользователь';
        $auth->add($user);

        // Создание разрешений
        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Управление пользователями';
        $auth->add($manageUsers);

        $viewUsers = $auth->createPermission('viewUsers');
        $viewUsers->description = 'Просмотр пользователей';
        $auth->add($viewUsers);

        $manageApi = $auth->createPermission('manageApi');
        $manageApi->description = 'Управление API';
        $auth->add($manageApi);

        // Назначение разрешений ролям
        $auth->addChild($admin, $manageUsers);
        $auth->addChild($admin, $viewUsers);
        $auth->addChild($admin, $manageApi);

        $auth->addChild($user, $viewUsers);

        // Создание администратора по умолчанию (first_name/last_name — в m240101_000006)
        $adminUser = new \app\models\User();
        $adminUser->username = 'admin';
        $adminUser->email = 'admin@example.com';
        $adminUser->setPassword('admin123');
        $adminUser->generateAuthKey();
        $adminUser->status = \app\models\User::STATUS_ACTIVE;
        if (!$adminUser->save(false)) {
            throw new \yii\db\Exception('Не удалось создать пользователя admin: ' . json_encode($adminUser->errors));
        }

        // Назначение роли администратора
        $auth->assign($admin, $adminUser->id);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Настройка authManager для консольного приложения
        $auth = new \yii\rbac\DbManager();
        $auth->db = Yii::$app->db;
        $auth->removeAll();
    }
}
