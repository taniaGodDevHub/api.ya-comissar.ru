# Инструкция по развертыванию API

## Проблема
API endpoint `https://api.112avarkom.ru/api/auth/login` возвращает 404 ошибку, потому что веб-сервер не перенаправляет запросы на `web/index.php`.

## Решение

### 1. Загрузить файлы на сервер
Убедитесь, что все файлы проекта загружены в корневую папку сервера.

### 2. Настроить веб-сервер

#### Для Apache (.htaccess)
Файл `.htaccess` уже создан в корне проекта. Он:
- Перенаправляет все запросы на `web/index.php`
- Добавляет CORS заголовки для API
- Защищает конфиденциальные файлы

#### Для Nginx
Используйте конфигурацию из файла `nginx.conf`:
```bash
# Скопировать конфигурацию в nginx
sudo cp nginx.conf /etc/nginx/sites-available/api.112avarkom.ru
sudo ln -s /etc/nginx/sites-available/api.112avarkom.ru /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 3. Настроить права доступа
```bash
# Установить правильные права
chmod 755 /path/to/project
chmod 755 /path/to/project/web
chmod 644 /path/to/project/web/index.php
chmod 644 /path/to/project/.htaccess
chmod 644 /path/to/project/web/.htaccess

# Установить владельца (замените www-data на пользователя веб-сервера)
chown -R www-data:www-data /path/to/project
```

### 4. Очистить кеш
```bash
# Очистить кеш Yii
rm -rf /path/to/project/runtime/cache/*
```

### 5. Проверить работу
```bash
# Тест OPTIONS запроса
curl -X OPTIONS -H "Origin: https://112avarkom.ru" -v https://api.112avarkom.ru/api/auth/login

# Тест POST запроса
curl -X POST -H "Origin: https://112avarkom.ru" -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' \
  https://api.112avarkom.ru/api/auth/login
```

## Ожидаемый результат

### OPTIONS запрос должен вернуть:
```
HTTP/2 200
Access-Control-Allow-Origin: https://112avarkom.ru
Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With
Access-Control-Allow-Credentials: true
Access-Control-Max-Age: 86400
```

### POST запрос должен вернуть:
```json
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

## Разрешенные домены для CORS
- `https://112avarkom.ru`
- `https://www.112avarkom.ru`
- `http://localhost:5173` (разработка)
- `http://localhost:3000` (разработка)
- `http://127.0.0.1:5173` (разработка)

## Безопасность
- Запрещен доступ к конфиденциальным файлам (composer.json, config/, migrations/, etc.)
- CORS настроен только для разрешенных доменов
- API требует аутентификации для защищенных endpoints
