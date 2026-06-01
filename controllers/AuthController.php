<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use app\models\forms\LoginForm;
use app\models\User;
use app\models\TempToken;

/**
 * Контроллер для аутентификации
 */
class AuthController extends Controller
{
    public $modelClass = 'app\models\User';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Настройка CORS
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:5173', 'http://localhost:3000', 'http://127.0.0.1:5173'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];

        // Настройка формата ответа
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Обработка preflight OPTIONS запросов
        if (Yii::$app->request->isOptions) {
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->send();
            return false;
        }
        
        return parent::beforeAction($action);
    }

    /**
     * Аутентификация пользователя
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            $token = $model->generateApiToken();
            if ($token) {
                return [
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'id' => $model->getUser()->id,
                        'username' => $model->getUser()->username,
                        'email' => $model->getUser()->email,
                        'roles' => array_keys($model->getUser()->getRoles()),
                    ]
                ];
            }
        }

        return [
            'success' => false,
            'errors' => $model->getErrors()
        ];
    }

    /**
     * Выход из системы (удаление токена)
     */
    public function actionLogout()
    {
        $token = $this->getBearerToken();
        if ($token) {
            $tempToken = TempToken::findOne(['token' => $token]);
            if ($tempToken) {
                $tempToken->delete();
            }
        }

        return ['success' => true, 'message' => 'Successfully logged out'];
    }

    /**
     * Получение информации о текущем пользователе
     */
    public function actionMe()
    {
        $user = Yii::$app->user->identity;
        if ($user) {
            return [
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'roles' => array_keys($user->getRoles()),
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'User not authenticated'
        ];
    }

    /**
     * Регистрация нового пользователя
     */
    public function actionRegister()
    {
        $token = Yii::$app->request->get('token');
        $postData = Yii::$app->request->post();
        
        // Проверяем токен
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Токен регистрации не предоставлен'
            ];
        }
        
        $tempToken = \app\models\TempToken::findOne(['token' => $token]);
        if (!$tempToken) {
            return [
                'success' => false,
                'message' => 'Недействительный токен регистрации'
            ];
        }
        
        // Создаем нового пользователя
        $user = new \app\models\User();
        $user->username = $postData['username'] ?? '';
        $user->email = $postData['email'] ?? '';
        $user->setPassword($postData['password'] ?? '');
        $user->generateAuthKey();
        $user->status = \app\models\User::STATUS_ACTIVE;
        
        if ($user->save()) {
            // Удаляем использованный токен
            $tempToken->delete();
            
            // Назначаем роль пользователя
            $authManager = Yii::$app->authManager;
            $userRole = $authManager->getRole('user');
            if ($userRole) {
                $authManager->assign($userRole, $user->id);
            }
            
            return [
                'success' => true,
                'message' => 'Пользователь успешно зарегистрирован',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Ошибка при регистрации',
            'errors' => $user->getErrors()
        ];
    }

    /**
     * Извлечение Bearer токена из заголовков
     */
    protected function getBearerToken()
    {
        $headers = Yii::$app->request->headers;
        $authHeader = $headers->get('Authorization');
        
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}
