<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <title>SeverQuest | Идеальное развлечение для круга друзей</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    @import url('https://fonts.googleapis.com/css?family=Neucha&subset=cyrillic');
    @import url('https://fonts.googleapis.com/css?family=Amatic+SC:400,700&subset=cyrillic');

    body {
        font-family: 'Neucha', cursive;
        font-weight: 400;
        font-size: 2rem;
        color: white;
    }
    body:before {
        content: "";
        display: block;
        position: fixed;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        z-index: -10;
        background: url(css/image/back.jpg) no-repeat center center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
    .team {
        position: fixed;
        right: 30px;
        top: 20px;
        z-index: 10;
    }
    form {
        margin: 2rem;
        font-size: 1.5rem;
    }
    .card {
        /* font-family: 'Amatic SC', cursive;
        font-weight: 700;
        font-size: 2rem; */
        background-color:darkcyan;
        opacity: .9;
        margin: 0 1.7rem;
    }
    .card::before {
        content: '◀';
        color:coral;
        position: absolute;
        left: -25px;
        z-index: -1;
    }
    .card::after {
        content: '▶';
        color: coral;
        position: absolute;
        right: -25px;
        z-index: -1;
    }
    .card.disabled {
        background-color:grey;
        opacity: .7;
    }
    .card.inwork {
        background-color:steelblue;
    }
    .card-header h5 {
        width: 60vw;
        font-size: 1.3rem;
        font-weight: 700;
    }
    .card-header a {
        text-decoration: none;
        color: white;
    }
    .card-body {
        font-size: 1rem;
        text-align: 1rem;
    }
    .btn {
        border: none;
        font: inherit;
    }
    .btn-coral {
        background-color: coral;
        color: white;
    }
    h1 {
        width: 100%;
    }
    h1::before, h1::after {
        content: '▬';
        margin: 0 1rem;
        font-size: 1.5rem;
        vertical-align: 15%;
        color:darkcyan;
    }

    </style>
</head>
<body>
    @yield('content')

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('button.btn-coral').click(function() {
            console.log($(this).parents('.card').data('task'));
            let taskId = $(this).parents('.card').data('task');
            let teamName = $('.team').html();
            let titleName =  $(this).parents('.card').find('.card-header a').html();
            $.ajax({
                type: "PUT",
                data: ({
                    task: taskId,
                    team: teamName,
                    title: titleName 
                }),
                url: '/take-task',
                success: function (data) {
                    console.log(data);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        var timer = function () {
            var readingtimer = setInterval(function () {
                $.ajax({
                    type: "GET",
                    url: '/check-tasks',
                    success: function (data) {
                        data.forEach(element => {
                            let k = +Object.getOwnPropertyNames(element);
                            if(element[k] != $('.team').data('teamid')) $('.card[data-task='+k+'] button.btn-coral').attr('disabled', true).parents('.card').addClass('disabled');
                            else {
                                $('.card[data-task='+k+'] button.btn-coral').attr('disabled', true);
                                $('.card[data-task='+k+'] button.btn-success').attr('disabled', false);
                                $('.card[data-task='+k+']').addClass('inwork');
                            }
                            k++;
                        });
                    }
                });
            }, 500);
        }
        timer();

        var refresh = function () {
            $.ajax({
                type: "POST",
                dataType: "json",
                data: ({
                    action: 'read'
                }),
                url: 'read-write.php',
                success: function (data) {
                    JSON_data = JSON.parse(data);
                    let html = '';
                    for (var key in JSON_data) {
                        html += '<div class="col-xs-12"><input type="radio" name="questions" id="' +
                            key +
                            '" value="' + key + '">';
                        html += '<label for="' + key + '" class="btn btn-secondary"><i class="number">' +
                            key + '</i>' +
                            JSON_data[key].description + '';
                        html += '<div class="company">Задание у команды : <i>' + JSON_data[key].team +
                            '</i></div></label></div>';

                    }
                    $('#ajax').html(html);
                }
            });
        }
    </script>                   
</body>
</html>
