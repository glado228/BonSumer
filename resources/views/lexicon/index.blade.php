@extends('layouts.master')

{{--
  $lexicon_categories => array of categories taken from the lexicon.categories language resource
--}}

@section('content')

@include('partials.toolbar')

<section class="main-section">
  <div class="container">
    <div class="row disable-xs">
      <div class="padded-section"></div>
      <h1>{!! LR('lexicon.article_titles.t0') !!}</h1>
      <p>&nbsp;</p>

      <section class="padded-section">
        {!! LR('lexicon.article_body.p0') !!}
        <p></p>

        <section data-ng-controller="lexiconController" class="lexicon-section">
{{--
          <p>
            <div class="pull-right lexicon-search">
              <input data-ng-model="search" type="text" placeholder='Search...'>
              {!! IMG('general.search_icon', FALSE, ['width'=> 24 ]) !!}
              <a data-ng-click="clearSearch()">
                {!! IMG('general.clear_icon', FALSE, ['width'=> 24 ]) !!}
              </a>
            </div>
          </p>
          <p class="clearfix"></p>
--}}
          <div class="row">
            <div class="pull-right lexicon-toggle-buttons">
              <div data-ng-click="toggleAll(true)">{!! LR('lexicon.buttons.open_all') !!}</div>
              <span>|</span>
              <div data-ng-click="toggleAll(false)">{!! LR('lexicon.buttons.close_all') !!}</div>
            </div>
          </div>

          <ul class="lexicon-list">
            @foreach ($lexicon_categories as $category_index => $category)
              @include('lexicon.category', ['lr_prefix' => 'lexicon.categories.'.$category_index])
            @endforeach
          </ul>
        </section>

        <p>&nbsp;</p>
        <p>{!! LR('lexicon.article_body.p1') !!}</p>
        <p>{!! LR('lexicon.article_body.p2') !!}</p>
        <p>{!! LR('lexicon.article_body.p3') !!}</p>
        <p>{!! LR('lexicon.article_body.p4') !!}</p>
        <p>{!! LR('lexicon.article_body.p5') !!}</p>
        <p>{!! LR('lexicon.article_body.p6') !!}</p>
      </section>

      <section class="padded-section">
        <div class="lined-header">
          <h2 class="section-title">{!! LR('lexicon.article_titles.t1') !!}</h2>
          <div class="section-sep"></div>
          <p>{!! LR('lexicon.article_body.p7') !!}</p>
        </div>
      </section>
    </div> <!-- row -->
  </div> <!-- main-section container -->
</section>

@endsection


