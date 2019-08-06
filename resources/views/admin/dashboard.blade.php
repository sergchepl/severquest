@extends('layouts.admin')

@section('main')
    <!--main content start-->
      <section class="wrapper">
        <div class="row">
          <div class="col-lg-9 main-chart">
            <Main-chart :users="{{ $users }}"></Main-chart>
            <div class="row">
              <!-- WEATHER PANEL -->
              <div class="col-md-4 mb">
                  <div class="weather pn">
                    <img src="{{ $icon_url }}" alt="">
                    <h2>{{ round($temperature, 1) }}º C</h2>
                    <h4>СЕВЕРОДОНЕЦК</h4>
                  </div>
                </div>
              <!-- /col-md-4 -->
              <div class="col-md-4 mb">
                <!-- WHITE PANEL - TOP USER -->
                <div class="white-panel pn">
                  <div class="white-header">
                    <h5>ЛУЧШАЯ КОМАНДА</h5>
                  </div>
                  <h4><b>{{ $best_user->name }}</b></h4>
                  <div class="row">
                    <div class="col-md-6">
                      <p class="small mt">ВСЕГО ЗАРАБОТАНО ОЧКОВ</p>
                      <p>{{ $best_user->score }}</p>
                    </div>
                    <div class="col-md-6">
                      <p class="small mt">ВЫПОЛНЕНО ЗАДАЧ</p>
                      <p>{{ $best_user->completed_tasks->count() }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /col-md-4 -->
            </div>
            <!-- /row -->
          </div>
          <!-- /col-lg-9 END SECTION MIDDLE -->
          <!-- **********************************************************************************************************************************************************
              RIGHT SIDEBAR CONTENT
              *********************************************************************************************************************************************************** -->
          <div class="col-lg-3 ds site-min-height">
            <!-- RECENT ACTIVITIES SECTION -->
            <h4 class="centered mt">RECENT ACTIVITY</h4>
            <!-- First Activity -->
            <div class="desc">
              <div class="thumb">
                <span class="badge bg-theme"><i class="fa fa-clock-o"></i></span>
              </div>
              <div class="details">
                <p>
                  <muted>Just Now</muted>
                  <br/>
                  <a href="#">Paul Rudd</a> purchased an item.<br/>
                </p>
              </div>
            </div>
            <!-- Second Activity -->
            <div class="desc">
              <div class="thumb">
                <span class="badge bg-theme"><i class="fa fa-clock-o"></i></span>
              </div>
              <div class="details">
                <p>
                  <muted>2 Minutes Ago</muted>
                  <br/>
                  <a href="#">James Brown</a> subscribed to your newsletter.<br/>
                </p>
              </div>
            </div>
            <!-- Third Activity -->
            <div class="desc">
              <div class="thumb">
                <span class="badge bg-theme"><i class="fa fa-clock-o"></i></span>
              </div>
              <div class="details">
                <p>
                  <muted>3 Hours Ago</muted>
                  <br/>
                  <a href="#">Diana Kennedy</a> purchased a year subscription.<br/>
                </p>
              </div>
            </div>
            <!-- Fourth Activity -->
            <div class="desc">
              <div class="thumb">
                <span class="badge bg-theme"><i class="fa fa-clock-o"></i></span>
              </div>
              <div class="details">
                <p>
                  <muted>7 Hours Ago</muted>
                  <br/>
                  <a href="#">Brando Page</a> purchased a year subscription.<br/>
                </p>
              </div>
            </div>
          </div>
          <!-- /col-lg-3 -->
        </div>
        <!-- /row -->
      </section>
    <!--main content end-->
@endsection
