@extends('layouts.admin')

@section('main')
  <section class="wrapper site-min-height">
      <div class="row mt">
          <div class="col-md-12">
            <div class="content-panel">
              <table class="table table-striped table-advance table-hover">
                <h4><i class="fa fa-angle-right"></i> Список Команд</h4>
                <hr>
                <thead>
                  <tr>
                    <th><i class="fa fa-bullhorn"></i> Команда</th>
                    <th><i class="fa fa-bookmark"></i> Балы</th>
                    <th><i class=" fa fa-edit"></i> Статус</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $user)
                    <tr>
                      <td>
                        <a href="#">{{ $user->name }}</a>
                      </td>
                      <td>{{ $user->score }} </td>
                      <td>
                        @if($user->read_rules)
                          <span class="label label-success label-mini">Активна</span></td>
                        @else
                          <span class="label label-warning label-mini">Не прочли правила</span></td>
                        @endif
                      <td>
                        @if(!$user->read_rules)
                          <a href="{{ route('user.activate', $user->id) }}" class="btn btn-success btn-xs"><i class="fa fa-check"></i></a>
                        @endif
                        <a href="{{ route('user.clear', $user->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i></a>
                        <a href="{{ route('user.delete', $user->id) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
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