@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row m-0">
        <header class="w-100 d-flex justify-content-center">
            <a href="/">
                <h1>SEVER<span style="color: #ffad60">QUEST</span></h1>
            </a>
        </header>
        @guest
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-header">Зарегистрируйся и играй!</div>
    
                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
    
                            <div class="form-group row">
                                <label for="name" class="col-sm-4 col-form-label text-md-right">Имя команды (без пробелов)</label>
    
                                <div class="col-md-6">
                                    <input id="name" type="name" class="form-control{{ Session::has('errors') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>
    
                                    @if (Session::has('errors'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>Неправильное Имя или Пароль</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>
    
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control{{ Session::has('errors') ? ' is-invalid' : '' }}" name="password" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Пароль (подтверждение)</label>
                
                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>
    
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-success">
                                        Погнали!
                                    </button>
                                    <a style="float:right" href="/login" role="button" class="btn btn-coral">
                                        Уже в Тимe!
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
        <h1 class="rules-title">Правила</h1>
        <div class="rules">
            <h3>Цель квеста</h3>
            <p>Набрать максимальное количество очков, выполняя задания, размещенные на сайте.</p>
            <h3>Участники</h3>
            <p>Участвовать в квесте возможно только в составе команды. Команда должна состоять из ___ человек, среди которых обязательные должности: Капитан, корреспондент, местный…. .</p>
            <h3>Выполнение заданий</h3>
            <p>После регистрации и входа на сайт, команде предоставляется список заданий, которые она может выполнять на свой выбор. За каждое выполненное задание команда получает соответствующее количество очков, в зависимости от его сложности (количество очков указано на главной странице рядом с названием задания). Команда не может выполнять одновременно больше одного задания. При выборе какого либо, другие становятся неактивными. Список заданий для всех команд один. Если задание в процессе выполнения одной из команд, другая не может выполнять его параллельно. Выполнение этого же задания возможно лишь в том случае, если команда, которая первой взялась за его выполнение, не справилась и решила отказаться от него, нажав кнопку отказа в меню задания. После этого оно снова станет активным в общем списке.
                <br> Все задания делятся на 2 типа: уникальные и общие. Уникальное выполняется только одной командой за соответствующее количество очков. Общее задание одинаково для всех и выполняется всеми командами (по желанию). Очки распределяются между командами в соответствии с результатами.</p>
            <h3>Порядок действий для выполнения задания </h3>
            <p>
                <ul>
                    <li>выбрать из общего списка одно доступное задание</li>
                    <li>ознакомиться с требованиями конкретного задания (после клика на названии раскрывается описание)
                        </li>
                    <li>для того чтобы приступить к его выполнению нажать кнопку “За дело!”
                        </li>
                    <li>после выполнения всех требований указанных в задании нажать на кнопку “Хочу сдать!” и загрузить необходимые материалы (фото, видео итд)
                        </li>
                </ul>
            </p>
            <h3>Порядок проверки задания и возможные варианты развития событий</h3>
            <p>После вышеуказанных действий информация отправляется администратору на проверку. При проверке задания возможны следующие случаи:
                <br> а) Если все выполнено корректно, администратор подтверждает правильность выполнения, после чего на ваш баланс зачисляются соответствующие баллы и вы можете приступать к следующему (этот процесс может занять некоторое время, которое вы можете использовать для отдыха или ознакомления с требованиями других заданий). 
                <br> б) Если задание выполнено неточно или по каким либо причинам загружен несоответствующий контент (фото, видео или текст подтверждающие выполнение задания), администратор отправит задание на доработку. 
                <br> в) Если задание выполнено кардинально неправильно или отправлено на проверку более трех раз подряд, администратор имеет право не засчитать его выполнение вашей команде, и изменить его статус на “доступно всем”. После чего задание будет доступно для выполнения всеми командами в том числе и вам. 
                <br><i style="text-decoration: underline">При всех вариантах развития событий в общем телеграмм-канале будет появляться соответствующая информация. Внимательно следите за ней, пока не убедитесь в том, что задание сдано и вы получили соответствующие баллы. </i>
            </p>
            <h3>Передвижение по городу</h3>
            <p>Во время выполнения заданий команда обязана передвигаться вместе, нельзя разделяться. 
                Каждый участник обязан проявлять максимальную осторожность при езде по дорогам, на перекрестках 
                и других оживленных местах, соблюдая все правила безопасности!
            </p>
            <h3>Жульничество</h3>
            <p>При выполнении заданий запрещается жульничать. В тех случаях где нет возможности проверить честность выполнения задания 
                со стороны администратора, фальсификация результатов остается на вашей христианской совести. Если вы обнаружили явный недочет со стороны организаторов, 
                при котором вы можете кардинально улучшить свои позиции, просьба также поступить по совести уважая усилия других участников, 
                и сообщить о выявленном недочете администратору.</p>
            <h3>Необходимое техническое оснащение</h3>
            <p>Велосипед для каждого члена команды в исправном состоянии. Телефон с камерой и вспышкой, с подключением к мобильному интернету. Power bank.</p>
            <h3>Окончание квеста</h3>
            <p>Квест может закончится в двух случаях: 1й - истечение отведенного времени, 2й - выполнение и подтверждение администратором всех заданий.</p>
            <h3>Подсчет очков</h3>
            <p>Подсчет очков и оглашение победителя осуществляется на общей встрече по завершении квеста.</p>
            <p> Команда организаторов оставляет за собой право определения степени сложности заданий и соответствующее 
                количество очков за него. Если вам кажется что задание чрезмерно сложно, а количество балов за его выполнение 
                несправедливо мало (или наоборот), данная тема не подлежит оспариванию. Также все спорные моменты возникающие 
                в непредвиденных ситуациях разрешаются администратором и не подлежат оспариванию. 
                <br> Все команды соревнуются в одинаковых условиях.
            </p>
        </div>
        <div class="d-flex justify-content-center w-100 p-5">
            <a href="/game" role="button" class="btn btn-lg btn-coral">
                Вернуться в бой!
            </a>
        </div>
        @endguest
    </div>
</div>
@endsection
