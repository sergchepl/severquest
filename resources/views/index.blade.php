@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <header class="col-12">
                <div class="header">
                    <a href="/">Sever<span style="color: #ffad60">QUEST</span></a>
                    <div class="team">
                        <p class="status">{{Auth::user()->name}}</p>
                        <Score :user="{{ Auth::user() }}"></Score>
                        <button onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();" 
                            class="btn btn-cancel btn-sm">Выйти</button>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </header>
            @if(Auth::user()->read_rules)
                <h1>Уникальные</h1>
                <div class="type-1">
                    @foreach ($tasks as $k => $task)
                        @if($task->type == 1)
                            <Task :task-prop="{{ $task }}"  :user="{{ Auth::user() }}" :is-banned-prop="{{ $task->ban()->whereUserId(Auth()->user()->id)->first() ? 'true' : 'false' }}"></Task>
                        @endif
                    @endforeach
                </div>
                <h1>Общие</h1>
                <div class="type-2">
                    @foreach ($tasks as $k => $task)
                        @if($task->type == 2)
                            <Task :task-prop="{{ $task }}"  :user="{{ Auth::user() }}" :is-banned-prop="{{ $task->ban()->whereUserId(Auth()->user()->id)->first() ? 'true' : 'false' }}"></Task>
                        @endif
                    @endforeach
                </div>
                <Modal></Modal>
            @else
                <div class="rules">
                    <Rules></Rules>
                </div>
            @endif
        </div>
    </div>
@endsection
