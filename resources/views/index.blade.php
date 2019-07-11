@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <header class="col-12">
                <a href="/"><img src="/css/image/logo.jpg" alt=""></a>
                <div onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                            data-teamId='{{Auth::user()->id}}' class="team">
                    <div>{{Auth::user()->name}}</div>
                    <Score :user="{{ Auth::user() }}"></Score>
                    {{-- <span class="badge badge-light">{{Auth::user()->score}}</span> --}}
                </div>s
                {{-- ДЛЯ ТЕСТОВ!!!!!! --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                {{-- ДЛЯ ТЕСТОВ!!!!!! --}}
            </header>
            @if(Auth::user()->read_rules)
            <h1>Уникальные</h1>
                <div class="type-1">
                    @foreach ($tasks as $k => $task)
                        @if($task->type == 1)
                            <Task :task-prop="{{ $task }}"  :user="{{ Auth::user() }}"></Task>
                        @endif
                    @endforeach
                </div>
            <h1>Общие</h1>
                <div class="type-2">
                    @foreach ($tasks as $k => $task)
                        @if($task->type == 2)
                            <Task :task-prop="{{ $task }}"  :user="{{ Auth::user() }}"></Task>
                        @endif
                    @endforeach
                </div>
        </div>
        <Modal></Modal>
        <div class="answer" style="display:none">
            <button type="button" class="close">
                <span aria-hidden="true">&times;</span>
            </button>
            <form class="form-inline" action="{{ url('/send-answer') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <progress id="progressbar" value="0" max="100"></progress>
                <input type="hidden" name="team" value="{{Auth::user()->name}}">
                <input type="hidden" name="task" value="">
                <input type="hidden" name="task_text" value="">
                <input type="hidden" name="task_id" value="">
                <input type="hidden" name="task_type" value="">
                <div class="form-group ">
                    <input type="file" name="files[]" style="font-size: 1rem" multiple>
                </div>
                <div class="form-group mr-3 mb-2">
                    <input type="text" class="form-control" name="text" id="inputPassword2" placeholder="Доп.текст">
                </div>
                <button type="submit" style="font-size: 1rem" class="btn btn-primary  mb-2">Submit</button>
            </form>
        </div>
        @else
        <div class="rules"  data-active="rules-1">
            <div class="jumbotron jumbotron-fluid">
                <div id="endRules" style="display:none"></div>
                <div id="rules-1" class="container">
                    <h2>Цель квеста</h2>
                    <p>Набрать максимальное количество очков, выполняя задания, размещенные на сайте.</p>
                    <h2>Участники</h2>
                    <p>Участвовать в квесте возможно только в составе команды. Команда должна состоять из ___ человек, среди которых обязательные должности: Капитан, корреспондент, местный…. .</p>
                </div>
                <div id="rules-2" style="display:none" class="container">
                    <h2>Выполнение заданий</h2>
                    <p>После регистрации и входа на сайт, команде предоставляется список заданий, которые она может выполнять на свой выбор. За каждое выполненное задание команда получает соответствующее количество очков, в зависимости от его сложности (количество очков указано на главной странице рядом с названием задания). Команда не может выполнять одновременно больше одного задания. При выборе какого либо, другие становятся неактивными. Список заданий для всех команд один. Если задание в процессе выполнения одной из команд, другая не может выполнять его параллельно. Выполнение этого же задания возможно лишь в том случае, если команда, которая первой взялась за его выполнение, не справилась и решила отказаться от него, нажав кнопку отказа в меню задания. После этого оно снова станет активным в общем списке.
                            <br> Все задания делятся на 2 типа: уникальные и общие. Уникальное выполняется только одной командой за соответствующее количество очков. Общее задание одинаково для всех и выполняется всеми командами (по желанию). Очки распределяются между командами в соответствии с результатами.</p>
                </div>
                <div id="rules-3" style="display:none" class="container">
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
                </div>
                <div id="rules-4" style="display:none" class="container">
                    <h3>Порядок проверки задания и возможные варианты развития событий</h3>
                        <p>После вышеуказанных действий информация отправляется администратору на проверку. При проверке задания возможны следующие случаи:
                            <br> а) Если все выполнено корректно, администратор подтверждает правильность выполнения, после чего на ваш баланс зачисляются соответствующие баллы и вы можете приступать к следующему (этот процесс может занять некоторое время, которое вы можете использовать для отдыха или ознакомления с требованиями других заданий). 
                            <br> б) Если задание выполнено неточно или по каким либо причинам загружен несоответствующий контент (фото, видео или текст подтверждающие выполнение задания), администратор отправит задание на доработку. 
                            <br> в) Если задание выполнено кардинально неправильно или отправлено на проверку более трех раз подряд, администратор имеет право не засчитать его выполнение вашей команде, и изменить его статус на “доступно всем”. После чего задание будет доступно для выполнения всеми командами в том числе и вам. 
                            <br><i style="text-decoration: underline">При всех вариантах развития событий в общем телеграмм-канале будет появляться соответствующая информация. Внимательно следите за ней, пока не убедитесь в том, что задание сдано и вы получили соответствующие баллы. </i>
                        </p>
                </div>
                <div id="rules-5" style="display:none" class="container">
                    <h3>Передвижение по городу</h3>
                    <p>Во время выполнения заданий команда обязана передвигаться вместе, нельзя разделяться. 
                        Каждый участник обязан проявлять максимальную осторожность при езде по дорогам, на перекрестках 
                        и других оживленных местах, соблюдая все правила безопасности!
                    </p>
                </div>
                <div id="rules-6" style="display:none" class="container">
                    <h3>Жульничество</h3>
                    <p>При выполнении заданий запрещается жульничать. В тех случаях где нет возможности проверить честность выполнения задания со стороны администратора, фальсификация результатов остается на вашей христианской совести. Если вы обнаружили явный недочет со стороны организаторов, при котором вы можете кардинально улучшить свои позиции, просьба также поступить по совести уважая усилия других участников, и сообщить о выявленном недочете администратору.</p>
                </div>
                <div id="rules-7" style="display:none" class="container">
                    <h3>Необходимое техническое оснащение</h3>
                    <p>Велосипед для каждого члена команды в исправном состоянии. Телефон с камерой и вспышкой, с подключением к мобильному интернету. Power bank.</p>
                    <h3>Окончание квеста</h3>
                    <p>Квест может закончится в двух случаях: 1й - истечение отведенного времени, 2й - выполнение и подтверждение администратором всех заданий.</p>            
                </div>
                <div id="rules-8" style="display:none" class="container">
                    <h3>Подсчет очков</h3>
                    <p>Подсчет очков и оглашение победителя осуществляется на общей встрече по завершении квеста.</p>
                    <p> Команда организаторов оставляет за собой право определения степени сложности заданий и соответствующее 
                        количество очков за него. Если вам кажется что задание чрезмерно сложно а количество балов за его выполнение 
                        несправедливо мало (или наоборот), данная тема не подлежит оспариванию. Также все спорные моменты возникающие 
                        в непредвиденных ситуациях разрешаются администратором и не подлежат оспариванию. 
                        <br> Все команды соревнуются в одинаковых условиях.
                    </p>
                </div>
                <hr class="my-4">
                <div class="col-md-6 offset-md-4">
                    <button style="font-size:1rem" role="button" class="btn btn-success" disabled>
                        Понял-принял! (<span id="timer">15</span>)
                    </button>
                    <button style="font-size:1rem" role="button" class="btn btn-danger">
                        Я все знаю!
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
