<html lang="en">
<body>
    @while ($i < 10)
        Current Value is {{$i}}
        @php
            $i++;
        @endphp
    @endwhile
</body>
</html>