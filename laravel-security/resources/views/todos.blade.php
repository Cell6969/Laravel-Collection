<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todos</title>
</head>
<body>
<h1>Todos</h1>
<table>
    @foreach($todos as $todo)
        <tr>
            <td>{{$todo->title}}</td>
            <td>
                @can('update', $todo)
                    Edit
                @else
                    No Edit
                @endcan
            </td>
            <td>
                @can('delete', $todo)
                    Delete
                @else
                    No Delete
                @endcan
            </td>
        </tr>
    @endforeach
</table>
</body>
</html>
