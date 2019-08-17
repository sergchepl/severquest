@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <header class="col-12">
                SEVERQUEST<a href="/"><img src="/css/image/logo.jpg" alt=""></a>
                <div onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                            data-teamId='{{Auth::user()->id}}' class="team">
                    <div>{{Auth::user()->name}}</div>
                    <Score :user="{{ Auth::user() }}"></Score>
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
        </div>
        <Modal></Modal>
        @else
        <div class="rules">
            <Rules></Rules>
        </div>
        @endif
    </div>
@endsection
