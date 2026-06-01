<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Форма для создания/редактирования пользователей
 */
class UserForm extends Model
{
    public $id;
    public $username;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $password_confirm;
    public $status;
    public $roles = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'first_name', 'last_name'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 100],
            [['first_name', 'last_name'], 'trim'],
            ['email', 'email'],
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username', 'when' => function($model) {
                return $model->id === null || User::findOne($model->id)->username !== $model->username;
            }],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email', 'when' => function($model) {
                return $model->id === null || User::findOne($model->id)->email !== $model->email;
            }],
            ['password', 'string', 'min' => 6],
            ['password_confirm', 'compare', 'compareAttribute' => 'password'],
            ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_DELETED]],
            ['roles', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'Email',
            'password' => 'Пароль',
            'password_confirm' => 'Подтверждение пароля',
            'status' => 'Статус',
            'roles' => 'Роли',
        ];
    }

    /**
     * Загружает данные пользователя в форму
     */
    public function loadUser(User $user)
    {
        $this->id = $user->id;
        $this->username = $user->username;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->status = $user->status;
        $this->roles = array_keys($user->getRoles());
    }

    /**
     * Сохраняет пользователя
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->id ? User::findOne($this->id) : new User();
        
        if (!$user) {
            $this->addError('id', 'Пользователь не найден');
            return false;
        }

        $user->username = $this->username;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->status = $this->status;

        if ($this->password) {
            $user->setPassword($this->password);
        }

        if ($user->isNewRecord) {
            $user->generateAuthKey();
        }

        if ($user->save()) {
            $this->assignRoles($user);
            return true;
        }

        return false;
    }

    /**
     * Назначает роли пользователю
     */
    protected function assignRoles(User $user)
    {
        $authManager = Yii::$app->authManager;
        
        // Удаляем все роли пользователя
        $authManager->revokeAll($user->id);
        
        // Назначаем новые роли
        foreach ($this->roles as $roleName) {
            $role = $authManager->getRole($roleName);
            if ($role) {
                $authManager->assign($role, $user->id);
            }
        }
    }
}






