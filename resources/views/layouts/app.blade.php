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
        height: 200vw;
        z-index: -10;
        background: url(css/image/back.jpg) no-repeat center center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
    header {
        padding: 1rem 2rem;
    }
    .team {
        float: right;
    }
    .btn-danger.hide, .btn-coral.hide, .btn-success.hide {
        display: none;
    }
    .badge {
        float: right;
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
        content: '◖';
        color:coral;
        position: absolute;
        left: -14px;
        z-index: -1;
    }
    .card::after {
        content: '◗';
        color: coral;
        position: absolute;
        right: -14px;
        z-index: -1;
    }
    .card.disabled {
        background-color:grey;
        opacity: .7;
    }
    .card.inwork {
        background-color:steelblue;
    }
    .card.done {
        background-color:darkgreen;
    }
    .card.banned {
        background-color:brown;
    }
    .card.check {
        background-color: chocolate;
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
    .rules {
        margin: 0 2rem;
        text-align: center;
        
    }
    .rules:before {
    content: ' ';
    display: block;
    position: fixed;
    left: 1.5rem;
    top: 0;
    width: 90%;
    height: 100vh;
    opacity: 0.9;
    z-index: -1;
    background-color: darkcyan;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
    h3{
        font-size: 1.2rem;
        display: inline-flex;
        
    }
    h3::before, h3::after {
        content: '▬';
        margin: 0 1rem;
        font-size: 1rem;
        vertical-align: 10%;
        color:coral;
    }
    .rules-title {
        text-align:center;
        /* color: coral; */
    }
    h1.rules-title::after, h1.rules-title::before {
        content: '⬥';
        font-size: 2.5rem;
        vertical-align: 0%;
        color:coral;
    }
    p {
        font-size: 1rem;
        text-align:justify;
        z-index: 2;
        display: block;
        text-indent: 1rem;
    }
    ul, li {
        font-size: initial;
        text-align: left;
    }
    .answer {
        position: fixed;
        bottom: 0px;
        background-color: 
    }
    .answer:before {
    content: ' ';
    display: block;
    position: fixed;
    left: -20px;
    width: 120%;
    height: 100%;
    opacity: 0.9;
    z-index: -1;
    background-color: coral;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    }
    .close {
        margin: 1rem 1rem 0 0;
    }
    </style>
</head>
<body>
    @yield('content')

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script>
        var isTaskTaken = false, timestamp = 0, bannedTasks = 0;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var sendTask = function(taskId, teamName, titleName, takeTaskBool) {
            $.ajax({
                type: "PUT",
                data: ({
                    task: taskId,
                    team: teamName,
                    title: titleName,
                    team_bool: takeTaskBool
                }),
                url: '/take-task',
                success: function (data) {
                    console.log(data);
                    if(takeTaskBool === "true") {
                        $('.card[data-task='+taskId+'] button.btn-coral').addClass('hide');
                        $('.card[data-task='+taskId+'] button.btn-danger').removeClass('hide');
                        $('.card[data-task='+taskId+'] button.btn-success').attr('disabled', false);
                        $('.card[data-task='+taskId+']').addClass('inwork');
                    } else {
                        $('.card[data-task='+taskId+'] button.btn-coral').removeClass('hide');
                        $('.card[data-task='+taskId+'] button.btn-danger').addClass('hide');
                        $('.card[data-task='+taskId+'] button.btn-success').attr('disabled', true);
                        $('.card[data-task='+taskId+']').removeClass('inwork');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
        $('button.btn-success').click(function(){
            $('.answer').show( "slow" );
            $('input[name="task"]').val($(this).parents('.card').find('a').html());
            $('input[name="task_text"]').val($(this).parents('.card').find('p').html());
            $('input[name="task_id"]').val($(this).parents('.card').data('task'));
        });
        $('.form-inline').submit(function(e) {
            var progressBar = $('#progressbar');
            var $that = $(this),
                formData = new FormData($that.get(0)),
                thatTaskId = $('input[name="task_id"]').val();
            console.log(thatTaskId);
            $('button[type="submit"]').attr('disabled', true);
            $('.card[data-task='+thatTaskId+'] button.btn-danger').removeClass('hide').attr('disabled', true);
            e.preventDefault();
            
            $.ajax({
            url: $that.attr('action'),
            type: $that.attr('method'),
            contentType: false,
            processData: false,
            data: formData,
            xhr: function(){
                var xhr = $.ajaxSettings.xhr(); // получаем объект XMLHttpRequest
                xhr.upload.addEventListener('progress', function(evt){ // добавляем обработчик события progress (onprogress)
                    if(evt.lengthComputable) { // если известно количество байт
                        // высчитываем процент загруженного
                        var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
                        // устанавливаем значение в атрибут value тега <progress>
                        // и это же значение альтернативным текстом для браузеров, не поддерживающих <progress>
                        progressBar.val(percentComplete).text('Загружено ' + percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function() {
                $('button[type="submit"]').attr('disabled', false);
                $('.answer').hide( "slow" );
            },
            error: function(error) {
                $('.card[data-task='+thatTaskId+'] button.btn-danger').removeClass('hide').attr('disabled', false);
                $('button[type="submit"]').attr('disabled', false);
                console.log(error);
            }
            });
        });
        $('.close').click(function(){
            $('.answer').hide( "slow" );
        });
        $('button.btn-coral').click(function() {
            if(isTaskTaken) {alert('Вы можете выполнять только 1 задание одновременно!'); return 0;}
            let task = $(this).parents('.card').data('task');
            let team = $('.team p').html();
            let title =  $(this).parents('.card').find('.card-header a').html();
            sendTask(task, team, title, "true");
        });
        
        $('button.btn-danger').click(function() {
            let task = $(this).parents('.card').data('task');
            let team = $('.team p').html();
            let title =  $(this).parents('.card').find('.card-header a').html();
            sendTask(task, team, title, "false");
            isTaskTaken = false;
        });


        var timer = function () {
            var readingtimer = setInterval(function () {
                $.ajax({
                    type: "GET",
                    url: '/check-tasks',
                    data: {
                        'timestamp' : timestamp,
                        'banned_tasks' : bannedTasks
                    },
                    success: function (data) {
                        let userCount = 0;

                        console.log(data);
                        
                        if(data != false) {                     
                            data.forEach(element => {                            
                                if(typeof element === "number") timestamp = element;
                                if(element.status == "3") {
                                    $('.card[data-task='+element.id+'] button.btn-coral').attr('disabled', true).parents('.card').addClass('disabled done').removeClass('inwork check');
                                    $('.card[data-task='+element.id+'] button.btn-coral').addClass('hide');
                                    $('.card[data-task='+element.id+'] button.btn-danger').addClass('hide');
                                    $('.card[data-task='+element.id+'] button.btn-success').addClass('hide');
                                }
                                if(element.user_id == $('.team').data('teamid') && element.status == "2") {
                                    $('.card[data-task='+element.id+'] button.btn-coral').addClass('hide');
                                    $('.card[data-task='+element.id+'] button.btn-danger').removeClass('hide').attr('disabled', true);
                                    $('.card[data-task='+element.id+'] button.btn-success').attr('disabled', true);
                                    $('.card[data-task='+element.id+']').addClass('check');
                                    userCount++;
                                }
                                if(element.user_id == $('.team').data('teamid') && element.status == "1") {
                                    $('.card[data-task='+element.id+'] button.btn-coral').addClass('hide');
                                    $('.card[data-task='+element.id+'] button.btn-danger').removeClass('hide').attr('disabled', false);
                                    $('.card[data-task='+element.id+'] button.btn-success').attr('disabled', false);
                                    $('.card[data-task='+element.id+']').removeClass('check disabled').addClass('inwork');
                                    userCount++;
                                }
                                if(element.user_id != $('.team').data('teamid') && element.status == "1") {
                                    $('.card[data-task='+element.id+'] button.btn-coral').attr('disabled', true).parents('.card').addClass('disabled');
                                }
                                if(element.status == 0) {
                                    $('.card[data-task='+element.id+'] button.btn-coral').removeClass('hide').attr('disabled', false);
                                    $('.card[data-task='+element.id+'] button.btn-danger').addClass('hide');
                                    $('.card[data-task='+element.id+'] button.btn-success').removeClass('hide').attr('disabled', true);
                                    $('.card[data-task='+element.id+']').removeClass('inwork disabled done');
                                }                            
                            });
                            let lastElement = data.length;
                            if(typeof data[lastElement-1] !== 'number') {
                                data[lastElement-1].forEach(element => {
                                    $('.card[data-task='+element.task_id+'] button.btn-coral').attr('disabled', true).parents('.card').addClass('disabled banned').removeClass('inwork check');
                                    $('.card[data-task='+element.task_id+'] button.btn-coral').addClass('hide');
                                    $('.card[data-task='+element.task_id+'] button.btn-danger').addClass('hide');
                                    $('.card[data-task='+element.task_id+'] button.btn-success').addClass('hide');            
                                });
                            }
                            if(userCount > 0) {
                                isTaskTaken = true;
                            } else {
                                isTaskTaken = false;
                            }
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }, 1000);
        }
        timer();
    </script>                   
</body>
</html>
