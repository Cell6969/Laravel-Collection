<!DOCTYPE html>
<html lang="en">

<body>
    <ul>
        @forelse ($hobbies as $hobby)
            <li>{{$hobby}}</li>
        @empty
            <li>Tidak ada Hobi</li>
        @endforelse
    </ul>
</body>
</html>