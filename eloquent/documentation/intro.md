# Intro

Setup database terlerbih dahulu, kemudian setup config connection ke database.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=33066
DB_DATABASE=eloquent
DB_USERNAME=root
DB_PASSWORD=root
```

test menggunakan perintah berikut untuk memastikan apakah sudah connect.
```
php artisan db:show
```