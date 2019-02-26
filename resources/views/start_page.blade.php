<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Уменьшитель ссылок</title>

        <!-- Bootstrap --> 
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
                rel="stylesheet" 
                integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" 
                crossorigin="anonymous">

        <!-- jQuery -->
        <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous">
        </script>

        <!-- Styles -->
        <style>
            html, body {
                height: 100%;
            }

            body {
                display: -ms-flexbox;
                display: flex;
                -ms-flex-align: stretch;
                align-items: stretch;
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }

            .short-link-add {
                width: 100%;
                max-width: 450px;
                padding: 15px;
                margin: 5% auto auto;
            }

            .form-short-link-add .checkbox {
                margin-top: 15px;
                font-weight: 400;
                text-align: left;
            }

            .form-short-link-add .form-control {
                position: relative;
                box-sizing: border-box;
                height: auto;
                padding: 10px;
                font-size: 16px;
            }

            .form-short-link-add .form-control:focus {
                z-index: 2;
            }

            #extended-options {
                display: none;
            }
            #link-prew {
                display: none;
                text-align: left;
            }
            #inputExpire {
                margin-top: 5px;
            }
            #error-block {
                display: none;
                color: #f00;
            }
            #success-block {
                display: none;
            }
            .success-block-info{
                text-align: left;
            }
        </style>
    </head>
    <body class="text-center">
        <div class="short-link-add">
            <img class="mb-4" src="/images/link-solid.svg" alt="" width="72" height="72">
            <h1 class="h3 mb-3 font-weight-normal">Уменьшитель ссылок</h1>
            <form class="form-short-link-add" id="add-link-data">
                <label for="inputLink" class="sr-only">Скопируйте ссылку</label>
                <input type="text" 
                        id="inputLink" 
                        name="link"
                        class="form-control" 
                        placeholder="Скопируйте ссылку сюда" 
                        value='' 
                        required 
                        autofocus>
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" 
                            class="unchecked" 
                            onchange="viewExtendedOpts(this.checked)"
                            checked=''> Дополнительно
                    </label>
                </div>
                <div id="extended-options">
                    <div id="link-prew">
                        Полная ссылка:<br>
                        http://<?=$_SERVER['SERVER_NAME']?>/<span id="self-made-uri-prew"></span>
                    </div>
                    <label for="inputCustomURI" class="sr-only">Задать свой вариант ссылки</label>
                    <input type="text" 
                        id="inputCustomURI" 
                        class="form-control novalue" 
                        name="self_uri"
                        placeholder="Задать свой вариант ссылки" 
                        value=""
                        onkeyup="renderPrewLink(this.value)">
                    <label for="inputExpire" class="sr-only">Время жизни ссылки (дней)</label>
                    <input type="number" 
                        id="inputExpire" 
                        class="form-control novalue" 
                        name="expire_days"
                        placeholder="Время жизни ссылки (дней)">
                    <div class="checkbox mb-3">
                        <label>
                            <input type="checkbox" 
                                name="is_commerce" 
                                value="yes"> Показывать рекламу при переходе
                        </label>
                    </div>
                </div>
                <div id="error-block"></div>
                <button class="btn btn-lg btn-primary btn-block" onclick="processLink()">Уменьшить</button>
            </form>
            <div id="success-block">
                <div class="alert alert-success" role="alert">
                    Короткая ссылка успешно создана!
                </div>
                <div class="success-block-info">
                    Целевая ссылка:<br><span id="result-target-link"></span><br>
                    Сокращенная ссылка:<br><span id="result-short-link"></span><br>
                    Cтатистика:<br><span id="result-stats-link"></span><br>
                    Действует до: <span id="result-expire"></span><br>
                </div> 
            </div>
        </div>
    </body>

    <script>
        $(document).ready(function() {
            // clear browser inputs
            $('input.unchecked').prop('checked', false)
            $('input.novalue').val('')
            // disable submit by browser
            $("#add-link-data").on('submit', function (e) {   
                e.preventDefault()
            })
        })

        function viewExtendedOpts(checked) {
            checked ? $('#extended-options').show() : $('#extended-options').hide()
        }

        function renderPrewLink(text) {
            if(text.length < 1) {
                $('#link-prew').hide()
            } else {
                $('#link-prew').show()
                $('#self-made-uri-prew').empty()
                $('#self-made-uri-prew').append(text)
            }
        }

        function processLink() {

            $('#error-block').empty();
            $('#error-block').hide();

            let data = $('#add-link-data').serializeArray()
            $.ajax({
                type: "POST",
                url: "/api/add_link",
                data: data,
                success: successResult,
                error: errorResult,
                dataType: 'json'
            });
        }

        function successResult(response) {
            $('#result-target-link').append(response.dstLink)
            $('#result-short-link').append(response.shortLink)
            $('#result-stats-link').append(response.statsLink)
            $('#result-expire').append(response.expire)

            $('#add-link-data').hide()
            $('#success-block').show()
        }

        function errorResult(error) {

            if(error.status == 400) {
                $('#error-block').append('Ошибка! ' + error.responseJSON.message)
            } else {
                $('#error-block').append('Произошла внутренняя ошибка')
            }
            
            $('#error-block').show()
        }
    </script>
</html>
