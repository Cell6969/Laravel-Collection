<!DOCTYPE html>
<html lang="en">

<body>
    <p>
        @if (count($hobbies) == 1)
            I just only have one hobbies.
        @elseif(count($hobbies) > 1)
            I have many hobbies
        @else
            I dont have any hobbies
        @endif
    </p>
</body>

</html>