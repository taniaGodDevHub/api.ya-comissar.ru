<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'School Avarcom Backend';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добро пожаловать!</h1>

        <p class="lead">Это административная панель School ya-comissar Backend</p>

        <?php if (Yii::$app->user->isGuest): ?>
            <p><?= Html::a('Войти в систему', ['/user/login'], ['class' => 'btn btn-lg btn-success']) ?></p>
        <?php else: ?>
            <p>Вы вошли как: <strong><?= Html::encode(Yii::$app->user->identity->username) ?></strong></p>
            <p>
                <?= Html::a('Управление пользователями', ['/user/index'], ['class' => 'btn btn-lg btn-primary']) ?>
                <?= Html::a('API Документация', ['/site/api-docs'], ['class' => 'btn btn-lg btn-info']) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>REST API</h2>

                <p>Полнофункциональное REST API с аутентификацией через Bearer токены.</p>

                <p><a class="btn btn-outline-secondary" href="<?= Url::to(['/api/user'])?>">API Endpoints &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>RBAC</h2>

                <p>Система ролей и разрешений с хранением в базе данных.</p>

                <p><a class="btn btn-outline-secondary" href="<?= Url::to(['/user/index'])?>">Управление пользователями &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Безопасность</h2>

                <p>Временные токены длиной 32 символа для безопасной аутентификации.</p>

                <p><a class="btn btn-outline-secondary" href="<?= Url::to(['/auth/login'])?>">Аутентификация &raquo;</a></p>
            </div>
        </div>

    </div>
</div>






