<html lang="en">

<body>
    @includeWhen($user['admin']== 'benar', 'header-admin')
    <p>Selamat datang {{$user['name']}}</p>
</body>

</html>