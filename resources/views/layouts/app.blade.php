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
        font-size: 1.5rem;
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
        padding: 1rem 2rem !important; 
    }
    .team {
        float: right;
    }
    .badge {
        float: right;
    }
    form {
        margin: 2rem;
        font-size: 1.5rem;
    }
    .type-1, .type-2 {
        margin-bottom: 3rem;
    }
    .card {
        /* font-family: 'Amatic SC', cursive;
        font-weight: 700;
        font-size: 2rem; */
        background-color:darkcyan;
        /* opacity: .9; */
        margin: 0 .9rem;
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
    .card.sharing {
        background-color:darkgray;
    }
    .card-header h5 {
        width: 83vw;
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
    .status {
        font-size: 1.4rem;
        color: white;
        font-weight: 700;
        margin: 0;
        text-align: center;
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
    left: 5vw;
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
    h2{
        font-size: 2rem;
        display: inline-flex;
        
    }
    h3::before, h3::after, h2::before, h2::after {
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
    .jumbotron {
        margin: 0 1rem;
        background-color: inherit;
        /* opacity: .9; */
    }
    </style>
</head>
<body>
    @yield('content')

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script>
        var isTaskTaken = false,
            timestamp = 0,
            bannedTasks = 0,
            pointsFromRules = 0;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // RULES
        var setScore = () => {
            return fetch('/set-score',{
                method: "PUT",
                body: JSON.stringify({score: pointsFromRules}),
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type' : 'application/json'
                    },
                credentials: 'same-origin'
            })
            .then((response) => {
                if(response.ok) {
                    return response.text();
                }
                throw new Error(response.text());
            })
            .catch((error) => {
                console.log(error);
            }); 
        }
        var slideRules = () => {
            let prevActiveRuleId = $('.rules').data('active'),
                nextRule = $("#" + prevActiveRuleId).slideUp('slow').next('div');
            if (nextRule.length == 0) {
                setScore()
                .then((res) => {
                    let html = '<h2>Вы успешно прошли обучение и заработали свои первые '+res+' баллов!</h2>';
                        html += '<p style="text-align:center">Ожидайте автоматического перенаправления к списку заданий.</p>';
                    $('#endRules').show().html(html);
                    // redirect
                    setTimeout(() => window.location.replace("/home"),3000);
                });    
            } else {
                nextRule.slideDown('slow');
                $('.rules').data('active', nextRule.attr('id'));
            }
        }
        $('.rules button.btn-success').on('click', () => {
            pointsFromRules += 5;
            slideRules();
            var obj = document.getElementById('timer');
            obj.innerHTML = 15;
            $('.rules button.btn-success').attr('disabled', true);
            
            setTimeout(timer,1000);
        });
        $('.rules button.btn-danger').on('click', () => {
            slideRules();
            var obj = document.getElementById('timer');
            obj.innerHTML = 15;
            $('.rules button.btn-success').attr('disabled', true);
        });
        function timer() {
            var obj = document.getElementById('timer');
            if(!obj) return;
            obj.innerHTML--;
            
            if (obj.innerHTML == 0) {
                $('.rules button.btn-success').attr('disabled', false);
                setTimeout(function(){},1000);
            }
            else{
                setTimeout(timer,1000);
            }
        }
        setTimeout(timer,1000);
        // END RULES

        // SEND to server info for changind status
        var sendTask = (task, takeTaskBool) => {
            // console.log(that);
            let taskId = $(task).parents('.card').data('task'),
                teamName = $('.team div').html(),
                titleName = $(task).parents('.card').find('.card-header a').html();
            $.ajax({
                type: "PUT",
                data: ({
                    task: taskId,
                    team: teamName,
                    title: titleName,
                    team_bool: takeTaskBool
                }),
                url: '/take-task',
                success: (data) => {
                    console.log(data);
                },
                error: (error) => {
                    console.log(error);
                    if(error.status == 409) alert('А ну не жульничать, а то забаним!');
                }
            });
        }
        $('.card').on('click', 'button.btn-coral', function () {
            if (isTaskTaken) {
                alert('Вы можете выполнять только 1 задание одновременно!');
                return 0;
            }
            sendTask(this, "true");
        });
        $('.card[data-type="1"] button.btn-danger').on('click', function () {
            sendTask(this, "false");
            isTaskTaken = false;
        });
        // functions for interactions with tasks
        var setDataInInput = (task) => {
            $('input[name="task"]').val($(task).parents('.card').find('a').html());
            $('input[name="task_text"]').val($(task).parents('.card').find('p').html());
            $('input[name="task_id"]').val($(task).parents('.card').data('task'));
            $('input[name="task_type"]').val($(task).parents('.card').data('type'));
        }
        $('.card[data-type="1"] button.btn-success, button.btn-info').on('click', function () {
            $('.answer').show("slow");
            setDataInInput(this);
        });
        $('.form-inline').on('submit', function (e) {
            let progressBar = $('#progressbar'),
                $that = $(this),
                formData = new FormData($that.get(0)),
                thatTaskId = $('input[name="task_id"]').val();

            console.log(thatTaskId);

            $('button[type="submit"]').attr('disabled', true);
            if (thatTaskId != 2)
                $('.card[data-task=' + thatTaskId + '] button.btn-danger').removeClass('hide').attr('disabled', true);

            e.preventDefault();

            $.ajax({
                url: $that.attr('action'),
                type: $that.attr('method'),
                contentType: false,
                processData: false,
                data: formData,
                xhr: function() {
                    var xhr = $.ajaxSettings.xhr();
                    xhr.upload.addEventListener('progress', function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
                            progressBar.val(percentComplete).text('Загружено ' + percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: () => {
                    $('button[type="submit"]').attr('disabled', false);
                    $('input').val('');
                    $('.answer').hide("slow");
                },
                error: error => {
                    if (thatTaskId != 2) $('.card[data-task=' + thatTaskId + '] button.btn-danger').removeClass('hide').attr('disabled', false);
                    $('button[type="submit"]').attr('disabled', false);
                    console.log(error);
                }
            });
        });
        $('.close').on('click', () => {
            $('.answer').hide("slow");
        });

        // Sync from server per 1sec
        $(window).on('load', () => {
            var readingtimer = setInterval(() => {
                httpSync();
            }, 1000);

            function httpSync() {
                let options = {
                    method: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    credentials: 'same-origin'
                }
                return fetch("/check-tasks?timestamp=" + timestamp + "&banned_tasks=" + bannedTasks + "", options)
                    .then(response => {
                        if (response.redirected) {
                            clearInterval(readingtimer);
                            throw new Error('Unatorizated!');   
                        }                            
                        if(response.ok && response.status != 204) {
                            return response.json();
                        } 
                        throw new Error('Data is empty!');
                    }).then(data => {
                        console.log(data);
                        let userCount = 0;
                        data.forEach(element => {
                            if (typeof element === "number") timestamp = element;
                            if (element.user_id == $('.team').data('teamid') && element.status == "3") {
                                $('.card[data-task=' + element.id + '] button.btn-coral').attr('disabled', true).parents('.card').addClass('disabled done').removeClass('inwork check banned');
                                $('.card[data-task=' + element.id + '] button').hide();
                                $('.card[data-task=' + element.id + '] div.status').show().html('Успешно выполнено!');
                                $('.card[data-task=' + element.id + ']').attr('data-took',0);
                            }
                            if (element.user_id == $('.team').data('teamid') && element.status == "2") {
                                $('.card[data-task=' + element.id + '] button').hide();
                                $('.card[data-task=' + element.id + ']').addClass('check');
                                $('.card[data-task=' + element.id + '] div.status').show().html('Находится на проверке, ожидайте!');
                                $('.card[data-task=' + element.id + ']').attr('data-took',1);
                            }
                            if (element.user_id == $('.team').data('teamid') && element.status == "1") {
                                $('.card[data-task=' + element.id + '] button.btn-coral').hide();
                                $('.card[data-task=' + element.id + '] button.btn-danger').show().attr('disabled', false);
                                $('.card[data-task=' + element.id + '] button.btn-success').show().attr('disabled', false);
                                $('.card[data-task=' + element.id + ']').removeClass('check disabled').addClass('inwork');
                                $('.card[data-task=' + element.id + '] div.status').hide();
                                $('.card[data-task=' + element.id + ']').attr('data-took',1);
                            }
                            if (element.user_id != $('.team').data('teamid') && element.type != 2) {
                                $('.card[data-task=' + element.id + '] button.btn-coral').attr('disabled', true).parents('.card').addClass('disabled');
                                $('.card[data-task=' + element.id + '] div.status').show().html('Уже занято другой командой!');
                                $('.card[data-task=' + element.id + '] button').hide();
                                $('.card[data-task=' + element.id + ']').attr('data-took',0);
                            }
                            if (element.status == 0) {
                                $('.card[data-task=' + element.id + '] button.btn-coral').show().attr('disabled', false);
                                $('.card[data-task=' + element.id + '] button.btn-danger').hide();
                                $('.card[data-task=' + element.id + '] button.btn-success').show().attr('disabled', true);
                                $('.card[data-task=' + element.id + ']').removeClass('inwork disabled done banned check');
                                $('.card[data-task=' + element.id + '] div.status').hide();
                                $('.card[data-task=' + element.id + ']').attr('data-took',0);
                            }
                        });
                        if ($('.card[data-took="1"]').length > 0) {
                            isTaskTaken = true;
                        } else {
                            isTaskTaken = false;
                        }
                        let lastElement = data.length;
                        if (typeof data[lastElement - 1] !== 'number') {
                            return data[lastElement - 1];
                        }
                        throw new Error('Data for Ban is empty!');
                    }).then(bannedData => {
                        bannedTasks = 0;
                        bannedData.forEach(element => {
                            bannedTasks++;
                            if ($('.card[data-task=' + element.task_id + ']').data('type') == 2) {
                                $('.card[data-task=' + element.task_id + ']').addClass('disabled check').removeClass('inwork banned sharing');
                                $('.card[data-task=' + element.task_id + '] button').hide();
                                $('.card[data-task=' + element.task_id + '] div.status').show().html('Ваш ответ принят!');
                            } else {
                                $('.card[data-task=' + element.task_id + ']').addClass('disabled banned').removeClass('inwork check sharing');
                                $('.card[data-task=' + element.task_id + '] button').hide();
                                $('.card[data-task=' + element.task_id + '] div.status').show().html('Вы не можете больше выполнять это задание!');
                                $('.card[data-task=' + element.task_id + ']').attr('data-took',0);
                            }
                        });
                    }).catch(error => {
                        console.log(error.message);
                    });
            }
        });
    </script>                   
</body>
</html>
