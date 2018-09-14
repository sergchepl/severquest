@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <header class="col-12">
                <img src="/css/image/logo.jpg" alt="">
                <div onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                data-teamId='{{Auth::user()->id}}' class="team">{{Auth::user()->name}}</div>
                {{-- ДЛЯ ТЕСТОВ!!!!!! --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                {{-- ДЛЯ ТЕСТОВ!!!!!! --}}
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
                                <button class="btn btn-danger hide btn-lg" role="button" >Отменись!</button>
                                <button style="float:right" class="btn btn-success btn-lg" role="button" {{ Auth::user()->id != $task->user_id ? ' disabled ' : '' }}>Хочу Сдать!</button>
                            </div>
                        </div>
                    </div>
                @endforeach
           
        </div>
    </div>
@endsection
