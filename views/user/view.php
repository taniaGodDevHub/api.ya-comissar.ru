<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1>Просмотр пользователя</h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'username',
                'label' => 'Имя пользователя',
            ],
            [
                'attribute' => 'first_name',
                'label' => 'Имя',
            ],
            [
                'attribute' => 'last_name',
                'label' => 'Фамилия',
            ],
            [
                'attribute' => 'full_name',
                'label' => 'Полное имя',
                'value' => function ($model) {
                    return $model->getFullName();
                },
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'label' => 'Email',
            ],
            [
                'attribute' => 'status',
                'label' => 'Статус',
                'value' => function ($model) {
                    return $model->status == \app\models\User::STATUS_ACTIVE ? 'Активен' : 'Заблокирован';
                },
            ],
            [
                'attribute' => 'roles',
                'label' => 'Роли',
                'value' => function ($model) {
                    return implode(', ', array_keys($model->getRoles()));
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'label' => 'Дата создания',
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'label' => 'Дата обновления',
            ],
        ],
    ]) ?>

</div>
