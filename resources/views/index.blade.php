@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <header class="col-12">
                <a href="/"><img src="/css/image/logo.jpg" alt=""></a>
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
                                <p>
                                    {!!$task->description!!}
                                </p>
                                <button class="btn btn-coral btn-lg" role="button" {{ (Auth::user()->id != $task->user_id) && $task->user_id ? ' disabled ' : '' }}>За дело!</button>
                                <button class="btn btn-danger hide btn-lg" role="button" >Отменись!</button>
                                <button style="float:right" class="btn btn-success btn-lg" role="button" {{ Auth::user()->id != $task->user_id ? ' disabled ' : '' }}>Хочу Сдать!</button>
                            </div>
                        </div>
                    </div>
                @endforeach
           
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
                <div class="form-group ">
                    <input type="file" name="files[]" style="font-size: 1rem" multiple>
                </div>
                <div class="form-group mr-3 mb-2">
                    <input type="text" class="form-control" name="text" id="inputPassword2" placeholder="Доп.текст">
                </div>
                <button type="submit" style="font-size: 1rem" class="btn btn-primary  mb-2">Submit</button>
            </form>
        </div>
    </div>
@endsection
