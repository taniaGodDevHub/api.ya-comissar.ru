<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\forms\UserForm;
use app\models\forms\LoginForm;

/**
 * Контроллер для управления пользователями через веб-интерфейс
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'generate-registration-token'],
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Список пользователей
     */
    public function actionIndex()
    {
        $searchModel = new \yii\data\ActiveDataProvider([
            'query' => User::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $searchModel,
        ]);
    }

    /**
     * Просмотр пользователя
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание пользователя
     */
    public function actionCreate()
    {
        $form = new UserForm();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', 'Пользователь успешно создан.');
            return $this->redirect(['view', 'id' => $form->id]);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Редактирование пользователя
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $form = new UserForm();
        $form->loadUser($user);

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', 'Пользователь успешно обновлен.');
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $form,
        ]);
    }

    /**
     * Удаление пользователя
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Пользователь успешно удален.');
        return $this->redirect(['index']);
    }

    /**
     * Вход в систему
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Выход из системы
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Генерация токена для регистрации нового пользователя
     */
    public function actionGenerateRegistrationToken()
    {
        // Проверяем, что пользователь авторизован и имеет роль администратора
        if (Yii::$app->user->isGuest || !Yii::$app->user->can('admin')) {
            throw new \yii\web\ForbiddenHttpException('Доступ запрещен');
        }

        // Генерируем токен регистрации
        $token = Yii::$app->security->generateRandomString(32);
        $tempToken = new \app\models\TempToken();
        $tempToken->user_id = null; // Токен не привязан к конкретному пользователю
        $tempToken->token = $token;
        
        if ($tempToken->save()) {
            $registrationUrl = 'https://112avarkom.ru/register?token='.$token;
            
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'success' => true,
                'token' => $token,
                'registration_url' => $registrationUrl,
                'message' => 'Токен успешно сгенерирован'
            ];
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'success' => false,
            'message' => 'Ошибка при генерации токена'
        ];
    }



    /**
     * Поиск модели пользователя
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
    }
}
