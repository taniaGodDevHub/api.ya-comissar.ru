# Пример API запроса для регистрации с новыми полями

## Endpoint
```
POST /api/auth/register?token=YOUR_REGISTRATION_TOKEN
```

## Headers
```
Content-Type: application/json
```

## Request Body
```json
{
    "username": "newuser123",
    "first_name": "Иван",
    "last_name": "Петров",
    "email": "ivan.petrov@example.com",
    "password": "securepassword123"
}
```

## Success Response (200)
```json
{
    "success": true,
    "message": "Пользователь успешно зарегистрирован",
    "token": "generated_api_token_here",
    "user": {
        "id": 123,
        "username": "newuser123",
        "first_name": "Иван",
        "last_name": "Петров",
        "full_name": "Иван Петров",
        "email": "ivan.petrov@example.com",
        "roles": ["user"]
    }
}
```

## Error Response (400)
```json
{
    "success": false,
    "message": "Ошибка при регистрации",
    "errors": {
        "username": ["Значение «newuser123» для «Username» уже занято."],
        "email": ["Значение «ivan.petrov@example.com» для «Email» уже занято."],
        "first_name": ["Имя не может быть пустым."],
        "last_name": ["Фамилия не может быть пустым."]
    }
}
```

## Обязательные поля
- `username` - имя пользователя (уникальное)
- `first_name` - имя (2-100 символов)
- `last_name` - фамилия (2-100 символов)
- `email` - email адрес (уникальный, валидный email)
- `password` - пароль (минимум 6 символов)

## Валидация
- Все поля обязательны для заполнения
- `first_name` и `last_name` должны содержать от 2 до 100 символов
- `username` и `email` должны быть уникальными
- `email` должен быть валидным email адресом
- `password` должен содержать минимум 6 символов
