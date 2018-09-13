@extends('layouts.app')

@section('content')
    <!-- Если зашел как гость то видим правила и регистрацию -->
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
    @else
    <div class="container-fluid">
        <div></div>
        <div class="row">
            <header class="col-12">
                <img src="/css/image/logo.jpg" alt="">
                <div data-teamId='{{Auth::user()->id}}' class="team">{{Auth::user()->name}}</div>
            </header>
            <h1>Общие</h1>
            
                @foreach ($tasks as $k => $task)
                    <div class="card mt-2 {{ (Auth::user()->id != $task->user_id) && $task->user_id ? ' disabled ' : '' }}" data-task="{{$task->id}}">
                        <div class="card-header" id="heading-{{$k}}">
                            <h5 class="mb-0">
                                <a data-toggle="collapse" href="#collapse-{{$k}}" aria-expanded="false">
                                    {{$task->name}}
                                </a>
                            </h5>
                        </div>
                    
                        <div id="collapse-{{$k}}" class="collapse" aria-labelledby="heading-{{$k}}">
                            <div class="card-body">
                                {!!$task->description!!}
                                <button class="btn btn-coral btn-lg" role="button" {{ (Auth::user()->id != $task->user_id) && $task->user_id ? ' disabled ' : '' }}>За дело!</button>
                                <button style="float:right" class="btn btn-success btn-lg" role="button" {{ Auth::user()->id != $task->user_id ? ' disabled ' : '' }}>Хочу Сдать!</button>
                            </div>
                        </div>
                    </div>
                @endforeach
           
        </div>
    </div>
    @endguest
@endsection
