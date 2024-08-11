<!DOCTYPE html>
<html lang="en">

<body>
    <ul>
        @for ($i = 0; $i < $limit; $i++)
            <li>{{$i}}</li>
        @endfor
    </ul>
</body>
</html>