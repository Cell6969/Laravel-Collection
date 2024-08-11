<!DOCTYPE html>
<html lang="en">
<body>
    <p>
        @isset($name)
            Welcome {{$name}} in my web.
        @endisset
    </p>
    <p>
        @empty($hobbies)
            I dont have any hobbies.
        @endempty
    </p>
</body>
</html>