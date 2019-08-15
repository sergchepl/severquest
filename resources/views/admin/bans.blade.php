@extends('layouts.admin')

@section('main')
  <section class="wrapper site-min-height">
      <div class="row mt">
          <div class="col-md-12">
            <div class="content-panel">
              <table class="table table-striped table-advance table-hover">
                <h4><i class="fa fa-angle-right"></i> Список Банов</h4>
                <hr>
                <thead>
                  <tr>
                    <th><i class="fa fa-bullhorn"></i> Задание</th>
                    <th><i class="fa fa-bookmark"></i> Команда</th>
                    <th><i class=" fa fa-edit"></i> Действие</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($bans as $ban)
                    <tr>
                      <td>
                        <a href="{{ route('task.edit', $ban->task->id) }}">{{ $ban->task->name }}</a>
                      </td>
                      <td>{{ $ban->user->name }} </td>
                      <td>
                        <a href="{{ route('ban.delete', $ban->id) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /content-panel -->
          </div>
          <!-- /col-md-12 -->
        </div>
  </section>
@endsection