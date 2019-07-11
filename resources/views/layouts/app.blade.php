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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main id="app">
        @yield('content')
    </main>

    <script src="{{ mix('js/app.js') }}"></script>
    <script> //TODO: rewrite this to Vue
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
        const setScore = () => {
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
        const slideRules = () => {
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
    </script>
</body>
</html>
