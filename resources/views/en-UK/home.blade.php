@extends('layouts.master')

@section('content')


@include('partials.toolbar')
<section class="container search-container">
  {!! Form::open(['class' => 'search-bar row', 'action' => 'ShopController@index', 'method' => 'GET', 'role' => 'form']) !!}
  <div class="input-group col-xs-12" >
    <input name="searchString" type="text" class="form-control" placeholder="{!! trans('search.placeholder') !!}">
    <span class="input-group-btn" aria-hidden="true">
      <button type="submit" class="btn btn-default">{!! LR('search.search') !!}</button>
    </span>
  </div>
  {!! Form::close() !!}
</section>

<section class="container shop-save-redeem" ng-controller="homeController" ng-cloak>

  <tabset class="row text-center top-tabs">
    <tab ng-mouseover="toggleOnHover(0)" active="tabs[0].active">
      <tab-heading class="text-center">
        <a class="visible-on-hover" data-ng-click="tabClicked(0)">
          <div class="img-container">
            {!! IMG('home_uk.shopping_inactive', null, ['class' => 'img-responsive ', 'width' => 195]) !!}
            {!! IMG('home_uk.shopping_active', null, ['class' => 'img-responsive overlay', 'width' => 195]) !!}
          </div>
          <h3>{!! LR('home.safe_and_sustainable') !!}</h3>
        </a>
      </tab-heading>
      <div>
        <div class="item col-sm-6">
          <div class="media split-media">
            <div class="media-left">
              {!! IMG('home_uk.tabs.purchase1', null, ['class' => 'media-object img-responsive']) !!}
            </div>
            <div class="media-body">
              <h4 class="media-heading">{!! LR('home.tabs.purchase1.title') !!}</h4>
              <span>{!! LR('home.tabs.purchase1.body') !!}</span>
            </div>
          </div>
        </div>
        <div class="item col-sm-6">
          <div class="media split-media">
            <div class="media-left">
              {!! IMG('home_uk.tabs.purchase2', null, ['class' => 'media-object img-responsive']) !!}
            </div>
            <div class="media-body">
              <h4 class="media-heading">{!! LR('home.tabs.purchase2.title') !!}</h4>
              <span>{!! LR('home.tabs.purchase2.body') !!}</span>
            </div>
          </div>
        </div>
      </div>
    </tab>
    <tab ng-mouseover="toggleOnHover(1)" active="tabs[1].active">
      <tab-heading>
        <a class="visible-on-hover" data-ng-click="tabClicked(1)">
          <div class="img-container">
            {!! IMG('home_uk.save_inactive', null, ['class' => 'img-responsive ', 'width' => 195]) !!}
            {!! IMG('home_uk.save_active', null, ['class' => 'img-responsive overlay', 'width' => 195]) !!}
         </div>
          <h3>{!! LR('home.collect_bonets') !!}</h3>
        </a>
      </tab-heading>
      <div>
        <div class="item col-sm-6">
          <div class="media split-media">
            <div class="media-left">
              {!! IMG('home_uk.tabs.collect_bonets1', null, ['class' => 'media-object img-responsive']) !!}
            </div>
            <div class="media-body">
              <h4 class="media-heading">{!! LR('home.tabs.collect_bonets1.title') !!}</h4>
              <span>{!! LR('home.tabs.collect_bonets1.body') !!}</span>
            </div>
          </div>
        </div>
        <div class="item col-sm-6">
          <div class="media split-media">
            <div class="media-left">
              {!! IMG('home_uk.tabs.collect_bonets2', null, ['class' => 'media-object img-responsive']) !!}
            </div>
            <div class="media-body">
              <h4 class="media-heading">{!! LR('home.tabs.collect_bonets2.title') !!}</h4>
              <span>{!! LR('home.tabs.collect_bonets2.body') !!}</span>
            </div>
          </div>
        </div>
      </div>
    </tab>
    <tab ng-mouseover="toggleOnHover(2)" active="tabs[2].active">
      <tab-heading class="poop">
        <a class="visible-on-hover" data-ng-click="tabClicked(2)">
          <div class="img-container">
            {!! IMG('home_uk.redeem_inactive', null, ['class' => 'img-responsive ', 'width' => 195]) !!}
            {!! IMG('home_uk.redeem_active', null, ['class' => 'img-responsive overlay', 'width' => 195]) !!}
          </div>
          <h3>{!! LR('home.redeem_bonets') !!}</h3>
        </a>
      </tab-heading>
      <div>
        <div class="item col-sm-6">
          <div class="media split-media">
            <div class="media-left">
              {!! IMG('home_uk.tabs.redeem_bonets1', null, ['class' => 'media-object img-responsive']) !!}
            </div>
            <div class="media-body">
              <h4 class="media-heading">{!! LR('home.tabs.redeem_bonets1.title') !!}</h4>
              <span>{!! LR('home.tabs.redeem_bonets1.body') !!}</span>
            </div>
          </div>
        </div>
        <div class="item col-sm-6">
          <div class="media split-media">
            <div class="media-left">
              {!! IMG('home_uk.tabs.redeem_bonets2', null, ['class' => 'media-object img-responsive']) !!}
            </div>
            <div class="media-body">
              <h4 class="media-heading">{!! LR('home.tabs.redeem_bonets2.title') !!}</h4>
              <span>{!! LR('home.tabs.redeem_bonets2.body') !!}</span>
            </div>
          </div>
        </div>
      </div>
    </tab>
  </tabset>
  <div class="row seals">
    <a href="{{ action('ShopController@index') }}">
      <div  popover="{{ trans('criteria.fair_text') }}" popover-trigger="mouseenter">
        {!! IMG('home_uk.seals.fair') !!}
        {!! IMG('home_uk.seals.fair_d') !!}
        <span>{!! LR('criteria.fair_title') !!}</span>
      </div>
    </a>
    <a href="{{ action('ShopController@index') }}">
      <div  popover="{{ trans('criteria.economic_text') }}" popover-trigger="mouseenter">
        {!! IMG('home_uk.seals.economic') !!}
        {!! IMG('home_uk.seals.economic_d') !!}
        <span>{!! LR('criteria.economic_title') !!}</span>
      </div>
    </a>
    <a href="{{ action('ShopController@index') }}">
      <div  popover="{{ trans('criteria.recycle_text') }}" popover-trigger="mouseenter">
        {!! IMG('home_uk.seals.recycle') !!}
        {!! IMG('home_uk.seals.recycle_d') !!}
        <span>{!! LR('criteria.recycle_title') !!}</span>
      </div>
    </a>
    <a href="{{ action('ShopController@index') }}">
      <div  popover="{{ trans('criteria.detox_text') }}" popover-trigger="mouseenter">
        {!! IMG('home_uk.seals.detox') !!}
        {!! IMG('home_uk.seals.detox_d') !!}
        <span>{!! LR('criteria.detox_title') !!}</span>
      </div>
    </a>
    <a href="{{ action('ShopController@index') }}">
      <div  popover="{{ trans('criteria.renewable_text') }}" popover-trigger="mouseenter">
        {!! IMG('home_uk.seals.renewable') !!}
        {!! IMG('home_uk.seals.renewable_d') !!}
        <span>{!! LR('criteria.renewable_title') !!}</span>
      </div>
    </a>
  </div>
</section>
<section class="home-section gray-section">
  <div class="container">
    <h2>{!! LR('home.customers_say') !!}</h2>
    <carousel interval="5000">
    <slide>
    <div class="row">
      <div class="col-sm-6">
        <div class="media split-media">
          <div class="media-left">
          {!! IMG('home_uk.profiles0', null, ['class' => 'img-responsive profile-img', 'width' => 164]) !!}
          </div>
          <div class="media-body">
            <h4>{!! LR('home.customers.c0_name') !!}</h4>
            <span>{!! LR('home.customers.c0_review') !!}</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="media split-media">
          <div class="media-left">
          {!! IMG('home_uk.profile1', null, ['class' => 'img-responsive profile-img', 'width' => 164]) !!}
          </div>
          <div class="media-body">
            <h4>{!! LR('home.customers.c1_name') !!}</h4>
            <span>{!! LR('home.customers.c1_review') !!}</span>
          </div>
        </div>
      </div>
    </div>
    </slide>
    <slide>
    <div class="row">
      <div class="col-sm-6">
        <div class="media split-media">
          <div class="media-left">
          {!! IMG('home_uk.profile2', null, ['class' => 'img-responsive profile-img', 'width' => 164]) !!}
          </div>
          <div class="media-body">
            <h4>{!! LR('home.customers.c2_name') !!}</h4>
            <span>{!! LR('home.customers.c2_review') !!}</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="media split-media">
          <div class="media-left">
          {!! IMG('home_uk.profile3', null, ['class' => 'img-responsive profile-img', 'width' => 164]) !!}
          </div>
          <div class="media-body">
            <h4>{!! LR('home.customers.c3_name') !!}</h4>
            <span>{!! LR('home.customers.c3_review') !!}</span>
          </div>
        </div>
      </div>
    </div>
    </slide>
    </carousel>
  </div>
</section>
<section class="home-section">
  <div class="container">
    <h2>{!! LR('home.brands.title') !!}</h2>
    <div class="row">
      <div class="col-sm-6">
        <div class="media split-media">
          <div class="media-left">
            {!! IMG('home_uk.brand0', null, ['class' => 'img-responsive profile-img', 'width' => 164]) !!}
          </div>
          <div class="media-body">
            <h4>{!! LR('home.brands.b0_title') !!}</h4>
            <span>{!! LR('home.brands.b0_name') !!}</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="media split-media">
          <div class="media-left">
            {!! IMG('home_uk.brand1', null, ['class' => 'img-responsive profile-img', 'width' => 164]) !!}
          </div>
          <div class="media-body">
            <h4>{!! LR('home.brands.b1_title') !!}</h4>
            <span>{!! LR('home.brands.b1_name') !!}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="home-section gray-section">
  <div class="container">
    <h2> {!! LR('home.mission.title') !!} </h2>
    <div class="row">
      <div class="col-sm-6 text-center">
        {!! IMG('home_uk.team', null, ['class' => 'img-responsive']) !!}
      </div>
      <div class="col-sm-6 text-center">
        <h4>&nbsp</h4>
        <p class="larger"> {!! LR('home.mission.text') !!} </p>
      </div>
    </div>
  </div>
</section>
<section class="home-section about-section">
  <div class="container">
    <h2>{!! LR('home.about.title') !!}</h2>

    <h3>{!! LR('home.about.t0') !!}</h3>
    <p>{!! LR('home.about.p0') !!}</p>
    <h3>{!! LR('home.about.t1') !!}</h3>
    <p>{!! LR('home.about.p1') !!}</p>
    <h3>{!! LR('home.about.t2') !!}</h3>
    <p>{!! LR('home.about.p2') !!}</p>
    <h3>{!! LR('home.about.t3') !!}</h3>
    <p>{!! LR('home.about.p3') !!}</p>
    <h3>{!! LR('home.about.t4') !!}</h3>
    <p>{!! LR('home.about.p4') !!}</p>
    <h3>{!! LR('home.about.t5') !!}</h3>
    <p>{!! LR('home.about.p5') !!}</p>
    <h3>{!! LR('home.about.t6') !!}</h3>
    <p>{!! LR('home.about.p6') !!}</p>
    <h3>{!! LR('home.about.t7') !!}</h3>
    <p>{!! LR('home.about.p7') !!}</p>

  </div>
</section>
@endsection
