@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <header class="col-12">
            <img src="/css/image/logo.jpg" alt="">
        </header>
        
            <h1 class="rules-title">Правила</h1>
        <div class="rules">
            <h3>Цель квеста</h3>
            <p>набрать максимальное количество очков, выполняя задания, размещенные на сайте.</p>
            <h3>Участники</h3>
            <p>участвовать в квесте возможно только в составе команды. 
                Команда должна состоять из ___ человек, среди которых обязательные должности: Капитан, корреспондент, местный…. . 
                Команда должна иметь название а также придумать логин и пароль для регистрации и входа на сайте.</p>
            <h3>Выполнение заданий</h3>
            <p>После регистрации и входа на сайт, команде предоставляется список заданий, которые она может выполнять на свой выбор. 
                За каждое выполненное задание команда получает соответствующее количество очков, в зависимости от его сложности (количество очков 
                указано на главной странице рядом с названием задания). Команда не может выполнять одновременно больше одного задания. 
                При выборе какого либо, другие становятся неактивными. Список заданий для всех команд один. Если задание в процессе выполнения 
                одной из команд, другая не может выполнять его параллельно. Выполнение этого же задания возможно лишь в том случае, если команда, 
                которая первой взялась за его выполнение, не справилась и решила отказаться от него, нажав кнопку отказа в меню задания. 
                После этого оно снова станет активным в общем списке.
                <br>Все задания делятся на <strong>2 типа:</strong>  уникальные и общие. Уникальное выполняется только одной командой за соответствующее количество очков.
                Общее задание одинаково для всех и выполняется всеми командами (по желанию). Очки распределяются 
                между командами в соответствии с результатами.</p>
            <h3>Цель квес123та</h3>
            <p>набрать макс123123имальное количество очков, выполняя задания, размещенные на сайте.</p>
        </div>
        @guest
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group row">
                <label for="name" class="col-md-4 col-form-label text-md-right">Название команды</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" required autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="login" class="col-md-4 col-form-label text-md-right">Логин</label>

                <div class="col-md-6">
                    <input id="login" type="login" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="login" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Пароль(подтверждение)</label>

                <div class="col-md-6">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                </div>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-coral">
                        Погнали!
                    </button>
                    <a style="float:right" href="/login" role="button" class="btn btn-success">
                            Уже в Тимe!
                    </a>
                </div>
            </div>
        </form>
        @endguest
    </div>
</div>
@endsection
