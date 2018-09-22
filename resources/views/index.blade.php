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
                    <span class="badge badge-light">{{Auth::user()->score}}</span>
                </div>
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
                        <div class="card mt-2 {{ (Auth::user()->id != $task->user_id) && $task->user_id ? ' disabled ' : '' }}" data-task="{{$task->id}}" data-type="1">
                            <div class="card-header" id="heading-{{$k}}">
                                <h5 class="mb-0">
                                    <a data-toggle="collapse" href="#collapse-{{$k}}" aria-expanded="false">
                                        {{$task->name}}
                                    </a>
                                    <span class="badge badge-light">{{$task->score}}</span>
                                </h5>
                                
                            </div>
                        
                            <div id="collapse-{{$k}}" class="collapse" aria-labelledby="heading-{{$k}}">
                                <div class="card-body">
                                    <p>
                                        {!!$task->description!!}
                                    </p>
                                    <button class="btn btn-coral btn-lg" role="button" {{ (Auth::user()->id != $task->user_id) && $task->user_id ? ' disabled ' : '' }}>За дело!</button>
                                    <button class="btn btn-danger hide btn-lg" role="button" >Отменись!</button>
                                    <button style="float:right" class="btn btn-success btn-lg" role="button" {{ Auth::user()->id != $task->user_id ? ' disabled ' : '' }}>Хочу Сдать!</button>
                                    <div class="status" style="display:none"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            <h1>Общие</h1>
                <div class="type-2">
                    @foreach ($tasks as $k => $task)
                        @if($task->type == 2)
                        <div class="card sharing mt-2" data-task="{{$task->id}}" data-type="2">
                            <div class="card-header" id="heading-{{$k}}">
                                <h5 class="mb-0">
                                    <a data-toggle="collapse" href="#collapse-{{$k}}" aria-expanded="false">
                                        {{$task->name}}
                                    </a>
                                    <span class="badge badge-light">{{$task->score}}</span>
                                </h5>
                                
                            </div>
                        
                            <div id="collapse-{{$k}}" class="collapse" aria-labelledby="heading-{{$k}}">
                                <div class="card-body">
                                    <p>
                                        {!!$task->description!!}
                                    </p>
                                    <button class="btn btn-info btn-lg" role="button">Хочу Сдать!</button>
                                    <div class="status" style="display:none"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
        </div>
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
        <div class="rules">
            <div class="jumbotron jumbotron-fluid">
                <div id="rules-1" class="container">
                    <h2>Цель квеста</h2>
                    <p>Набрать максимальное количество очков, выполняя задания, размещенные на сайте.</p>
                    <h2>Участники</h2>
                    <p>Участвовать в квесте возможно только в составе команды. Команда должна состоять из ___ человек, среди которых обязательные должности: Капитан, корреспондент, местный…. .</p>
                    <h2>Выполнение заданий</h2>
                    <p>После регистрации и входа на сайт, команде предоставляется список заданий, которые она может выполнять на свой выбор. За каждое выполненное задание команда получает соответствующее количество очков, в зависимости от его сложности (количество очков указано на главной странице рядом с названием задания). Команда не может выполнять одновременно больше одного задания. При выборе какого либо, другие становятся неактивными. Список заданий для всех команд один. Если задание в процессе выполнения одной из команд, другая не может выполнять его параллельно. Выполнение этого же задания возможно лишь в том случае, если команда, которая первой взялась за его выполнение, не справилась и решила отказаться от него, нажав кнопку отказа в меню задания. После этого оно снова станет активным в общем списке.
                            <br> Все задания делятся на 2 типа: уникальные и общие. Уникальное выполняется только одной командой за соответствующее количество очков. Общее задание одинаково для всех и выполняется всеми командами (по желанию). Очки распределяются между командами в соответствии с результатами.</p>
                </div>
                <div id="rules-2" style="display:none" class="container">
                        <h2>Цель квеста</h2>
                        <p>Набрать максимальное количество очков, выполняя задания, размещенные на сайте.</p>
                        <h2>Участники</h2>
                        <p>Участвовать в квесте возможно только в составе команды. Команда должна состоять из ___ человек, среди которых обязательные должности: Капитан, корреспондент, местный…. .</p>
                        <h2>Выполнение заданий</h2>
                        <p>После регистрации и входа на сайт, команде предоставляется список заданий, которые она может выполнять на свой выбор. За каждое выполненное задание команда получает соответствующее количество очков, в зависимости от его сложности (количество очков указано на главной странице рядом с названием задания). Команда не может выполнять одновременно больше одного задания. При выборе какого либо, другие становятся неактивными. Список заданий для всех команд один. Если задание в процессе выполнения одной из команд, другая не может выполнять его параллельно. Выполнение этого же задания возможно лишь в том случае, если команда, которая первой взялась за его выполнение, не справилась и решила отказаться от него, нажав кнопку отказа в меню задания. После этого оно снова станет активным в общем списке.
                                <br> Все задания делятся на 2 типа: уникальные и общие. Уникальное выполняется только одной командой за соответствующее количество очков. Общее задание одинаково для всех и выполняется всеми командами (по желанию). Очки распределяются между командами в соответствии с результатами.</p>
                    </div>
                <hr class="my-4">
                <div class="col-md-6 offset-md-4">
                    <button style="font-size:1rem" role="button" class="btn btn-success">
                        Понял-принял! <i></i>
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
