# Input Type
Pada laravel dapat dilakukan untuk konversi tipe data input pada request. Misal jika ingin diubah menjadi boolean, date atau yang lain.

Contoh konversi tipe data pada input request:
```php
public function inputType(Request $request): string
    {
        $name = $request->input("name"); // string
        $married = $request->boolean("married"); // boolean
        $birthDate = $request->date("birth_date", "Y-m-d");

        return json_encode([
            "name" => $name,
            "married" => $married,
            "birth_date" => $birthDate->format('Y-m-d')
        ]);
    }
```