<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\forms\UserForm $model */

$this->title = 'Редактировать пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true])->label('Имя пользователя') ?>

        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true])->label('Имя') ?>

        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true])->label('Фамилия') ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Email') ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->label('Пароль') ?>
        <div class="form-text">Оставьте пустым, если не хотите менять пароль</div>

        <?= $form->field($model, 'password_confirm')->passwordInput(['maxlength' => true])->label('Подтверждение пароля') ?>

        <?= $form->field($model, 'status')->dropDownList([
            \app\models\User::STATUS_ACTIVE => 'Активен',
            \app\models\User::STATUS_DELETED => 'Заблокирован',
        ])->label('Статус') ?>

        <?= $form->field($model, 'roles')->checkboxList(
            ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description')
        )->label('Роли') ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
