<!--sidebar start-->
<aside>
  <div id="sidebar" class="nav-collapse ">
    <!-- sidebar menu start-->
    <ul class="sidebar-menu">
      <li class="mt">
      <a class="@if(strcmp(\URL::current(), route('dashboard')) == 0 )) active @endif" href="{{ route('dashboard')}}">
          <i class="fa fa-dashboard"></i>
          <span>Панель Администратора</span>
          </a>
      </li>
      <li class="sub-menu">
        <a class="@if(strcmp(\URL::current(), route('tasks')) == 0 || strcmp(\URL::current(), route('task.new')) == 0 || strcmp(\URL::current(), route('bans')) == 0 )) active @endif" href="#">
          <i class="fa fa-desktop"></i>
          <span>Задачи</span>
        </a>
        <ul class="sub">
          <li class="@if(strcmp(\URL::current(), route('tasks')) == 0 )) active @endif"><a href="{{ route('tasks')}}">Список Задач</a></li>
          <li class="@if(strcmp(\URL::current(), route('bans')) == 0 )) active @endif"><a href="{{ route('bans')}}">Список Банов</a></li>
          <li class="@if(strcmp(\URL::current(), route('task.new')) == 0 )) active @endif"><a href="{{ route('task.new')}}">Добавить новую задачу</a></li>
        </ul>
      </li>
      <li>
        <a class="@if(strcmp(\URL::current(), route('users')) == 0 )) active @endif" href="{{ route('users')}}">
          <i class="fa fa-users"></i>
          <span>Пользователи</span>
        </a>
      </li>
    </ul>
    <!-- sidebar menu end-->
  </div>
</aside>
<!--sidebar end-->