# Encryption

Di laravel mendukung melakukan encryption. Untuk enkripsi , laravel membutuhkan key dimana key tersebut disimpan di *config/app.php*. Secara default Laravel akan mengambil key tersebut dari environment APP_KEY. Untuk men-generate APP_KEY bisa dicommand seperti ini:
```
php artisan key:generate
```
Contoh penggunaaan:
```php
public function testEncryption()
{
    $encrypt = Crypt::encrypt('jonathan');
    var_dump($encrypt);
    $decrypt = Crypt::decrypt($encrypt);
    self::assertEquals('jonathan', $decrypt);
}
```
