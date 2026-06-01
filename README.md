# School Avarcom Backend

REST API на Yii2 с аутентификацией через Bearer токены, RBAC и веб-интерфейсом управления пользователями.

## Возможности

- ✅ REST API с Bearer токенами
- ✅ RBAC (Role-Based Access Control) с хранением в БД
- ✅ Таблица временных токенов длиной 32 символа
- ✅ Веб-интерфейс управления пользователями
- ✅ Аутентификация и авторизация
- ✅ CORS поддержка

## Установка

1. Установите зависимости:
```bash
composer install
```

2. Настройте базу данных в файле `config/db.php`:
```php
'dsn' => 'mysql:host=localhost;dbname=school_avarcom',
'username' => 'sky',
'password' => 'Sky557555',
```

3. Создайте базу данных:
```sql
CREATE DATABASE school_avarcom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Выполните миграции:
```bash
./yii migrate
```

## API Endpoints

### Аутентификация
- `POST /api/auth/login` - Вход в систему
- `POST /api/auth/logout` - Выход из системы
- `GET /api/auth/me` - Информация о текущем пользователе

### Пользователи
- `GET /api/user` - Список пользователей
- `GET /api/user/{id}` - Просмотр пользователя
- `POST /api/user` - Создание пользователя
- `PUT /api/user/{id}` - Обновление пользователя
- `DELETE /api/user/{id}` - Удаление пользователя
- `GET /api/user/roles` - Список ролей

## Использование API

### Аутентификация
```bash
# Вход
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "admin123"}'

# Ответ
{
  "success": true,
  "token": "abc123...",
  "user": {
    "id": 1,
    "username": "admin",
    "email": "admin@example.com",
    "roles": ["admin"]
  }
}
```

### Использование токена
```bash
# Получение списка пользователей
curl -X GET http://localhost/api/user \
  -H "Authorization: Bearer abc123..."
```

## Веб-интерфейс

- `/` - Главная страница
- `/login` - Вход в систему
- `/users` - Управление пользователями (требует роль admin)

## Роли и разрешения

### Роли:
- `admin` - Администратор (полный доступ)
- `user` - Пользователь (ограниченный доступ)

### Разрешения:
- `manageUsers` - Управление пользователями
- `viewUsers` - Просмотр пользователей
- `manageApi` - Управление API

## Структура базы данных

### Таблица `user`
- `id` - Первичный ключ
- `username` - Имя пользователя (уникальное)
- `email` - Email (уникальный)
- `password_hash` - Хеш пароля
- `auth_key` - Ключ аутентификации
- `status` - Статус (10 - активен, 0 - заблокирован)
- `created_at`, `updated_at` - Временные метки

### Таблица `temp_token`
- `id` - Первичный ключ
- `user_id` - ID пользователя
- `token` - Токен (32 символа)
- `created_at`, `updated_at` - Временные метки

### RBAC таблицы
- `auth_rule` - Правила
- `auth_item` - Роли и разрешения
- `auth_item_child` - Иерархия ролей
- `auth_assignment` - Назначения ролей пользователям

## Администратор по умолчанию

После выполнения миграций создается администратор:
- **Логин:** admin
- **Пароль:** admin123
- **Email:** admin@example.com

## Безопасность

- Пароли хешируются с помощью `Yii::$app->security->generatePasswordHash()`
- Токены генерируются случайно длиной 32 символа
- Поддержка CORS для API
- RBAC для контроля доступа

## Разработка

Для разработки включены:
- Gii для генерации кода
- Debug панель
- Faker для тестовых данных

Доступ к Gii: `/gii` (только в dev режиме)
Доступ к Debug: `/debug` (только в dev режиме)






