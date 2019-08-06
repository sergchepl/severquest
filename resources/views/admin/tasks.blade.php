@extends('layouts.admin')

@section('main')
  <section class="wrapper site-min-height">
    <h3><i class="fa fa-angle-right"></i> Обычные Задачи</h3>
    <div class="row mt">
      @foreach ($tasks as $task)
      @if($task->type == 1)
        <div class="col-md-4 col-sm-4 mb">
          <div class="grey-panel donut-chart">
            <div class="grey-header">
              <h5 style="text-transform:uppercase">{{ $task->name }}</h5>
            </div>
            {!! $task->description !!}
            <div class="row mb">
              <div class="col-sm-10 col-xs-10 goleft">
                <p>Количество<br/>Балов:</p>
              </div>
              <div class="col-sm-2 col-xs-2">
                <h2>{{ $task->score }}</h2>
              </div>
              <div class="col-sm-12 mb">
                  <a href="#" class="btn btn-theme">Изменить</a>
                  <a href="{{ route('task.delete', $task->id) }}" class="btn btn-theme04">Удалить</a>
              </div>  
            </div>
          </div>
        </div>
      @endif
      @endforeach
    </div>
    <h3><i class="fa fa-angle-right"></i> Общие Задачи</h3>
    <div class="row mt">
        @foreach ($tasks as $task)
        @if($task->type == 2)
          <div class="col-md-4 col-sm-4 mb">
            <div class="darkblue-panel donut-chart">
              <div class="darkblue-header">
                <h5 style="text-transform:uppercase">{{ $task->name }}</h5>
              </div>
              {!! $task->description !!}
              <div class="row mb">
                <div class="col-sm-10 col-xs-10 goleft">
                  <p style="color: white;">Количество<br/>Балов:</p>
                </div>
                <div class="col-sm-2 col-xs-2">
                  <h2 style="color: white;">{{ $task->score }}</h2>
                </div>
                <div class="col-sm-12 mb">
                    <a href="#" class="btn btn-theme">Изменить</a>
                    <a href="{{ route('task.delete', $task->id) }}" class="btn btn-theme04">Удалить</a>
                </div>  
              </div>
            </div>
          </div>
        @endif
        @endforeach
      </div>
    <!-- /row -->
  </section>
@endsection