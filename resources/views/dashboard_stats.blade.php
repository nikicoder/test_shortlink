<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Статистика переходов по ссылке</title>

        <!-- Bootstrap --> 
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
                rel="stylesheet" 
                integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" 
                crossorigin="anonymous">
    </head>
    <body class="text-center">
        <main role="main" class="container">

        <div class="starter-template">
            <h1>Статистика сервиса</h1>
            <table class="table" style="margin-top: 20px">
                <thead>
                    <tr>
                        <th scope="col">Целевая ссылка</th>
                        <th scope="col">Сокращенная ссылка</th>
                        <th scope="col">Уникальных клиентов</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($stats as $s)
                    <tr>
                        <td>{{ $s['link']->destinationLink }}</td>
                        <td>{{ $s['link']->sourceLink }}</td>
                        <td>{{ $s['clients'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        </main>
</html>