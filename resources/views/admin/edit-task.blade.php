@extends('layouts.admin')

@section('main')
  <section class="wrapper site-min-height">
      <h3><i class="fa fa-angle-right"></i> Новая Задача</h3>
        <div class="row">
          <div class="col-lg-10">
            <form action="{{ route('task.update', $task->id) }}" method="POST" class="form-horizontal style-form">
              <input type="hidden" name="task_id" value="{{ $task->id }}">
              @csrf
              <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">Название</label>
                <div class="col-sm-10">
                  <input type="text" name="name" value="{{ $task->name }}" class="form-control round-form">
                </div>
              </div>
              <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Тип Задачи</label>
                  <div class="col-sm-10">
                    <div class="radio">
                      <label>
                        <input type="radio" name="type" value="1" @if($task->type == 1) checked @endif>
                          Обычные задачи
                        </label>
                    </div>
                    <div class="radio">
                      <label>
                        <input type="radio" name="type" value="2" @if($task->type == 2) checked @endif>
                          Общие задачи
                        </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Количество балов</label>
                  <div class="col-sm-2">
                    <input type="text" name="score" value="{{ $task->score }}" class="form-control round-form">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Описание</label>
                  <div class="col-sm-10">
                    <textarea id="description" type="text" name="description" class="form-control round-form">{!! $task->description !!}</textarea>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-lg-offset-2 col-lg-10">
                    <button class="btn btn-theme" type="submit">Обновить</button>
                  </div>
                </div>
            </form>
        </div>
  </section>
@endsection