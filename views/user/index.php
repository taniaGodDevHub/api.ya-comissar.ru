<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1>Управление пользователями</h1>

    <p>
        <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-primary" id="generate-token-btn">
            Выдать токен регистрации
        </button>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '№',
            ],

            [
                'attribute' => 'id',
                'header' => 'ID',
            ],
            [
                'attribute' => 'username',
                'header' => 'Имя пользователя',
            ],
            [
                'attribute' => 'full_name',
                'header' => 'Полное имя',
                'value' => function ($model) {
                    return $model->getFullName();
                },
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'header' => 'Email',
            ],
            [
                'attribute' => 'status',
                'header' => 'Статус',
                'value' => function ($model) {
                    return $model->status == \app\models\User::STATUS_ACTIVE ? 'Активен' : 'Заблокирован';
                },
            ],
            [
                'attribute' => 'roles',
                'header' => 'Роли',
                'value' => function ($model) {
                    return implode(', ', array_keys($model->getRoles()));
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'header' => 'Дата создания',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'header' => 'Действия',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return \yii\helpers\Html::a('<i class="fas fa-eye"></i>', $url, [
                            'title' => 'Просмотр',
                            'class' => 'btn btn-sm btn-outline-primary'
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return \yii\helpers\Html::a('<i class="fas fa-edit"></i>', $url, [
                            'title' => 'Редактировать',
                            'class' => 'btn btn-sm btn-outline-warning'
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return \yii\helpers\Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => 'Удалить',
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data-confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                            'data-method' => 'post',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <!-- Modal для отображения результата генерации токена -->
    <div class="modal fade" id="tokenModal" tabindex="-1" role="dialog" aria-labelledby="tokenModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tokenModalLabel">Токен успешно сгенерирован!</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Ссылка для регистрации:</strong></p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="registration-url-modal" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="copy-url-modal-btn">Копировать</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик для кнопки "Выдать токен регистрации"
    document.getElementById('generate-token-btn').addEventListener('click', function() {
        const originalText = this.textContent;
        
        // Показываем загрузку
        this.disabled = true;
        this.textContent = 'Генерируем...';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Content-Type': 'application/json'
        };
        
        if (csrfToken) {
            headers['X-CSRF-Token'] = csrfToken.getAttribute('content');
        }
        
        fetch('/web/index.php?r=user/generate-registration-token', {
            method: 'POST',
            headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Заполняем модальное окно
                document.getElementById('registration-url-modal').value = data.registration_url;
                // Показываем модальное окно
                $('#tokenModal').modal('show');
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при генерации токена');
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = originalText;
        });
    });
    
    // Обработчик для кнопки копирования в модальном окне
    document.getElementById('copy-url-modal-btn').addEventListener('click', function() {
        const urlInput = document.getElementById('registration-url-modal');
        urlInput.select();
        document.execCommand('copy');
        this.textContent = 'Скопировано!';
        setTimeout(() => {
            this.textContent = 'Копировать';
        }, 2000);
    });
});
</script>
