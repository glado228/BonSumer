@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">
        <h1>
          {!! LR('privacy.article_titles.t0') !!}
        </h1>
        <p>
          {!! IMG('general.lock_image', FALSE, ['width'=> 375, 'height' =>250 ]) !!}
        <br /><br />
        </p>
        <p>{!! LR('privacy.article_body.p0') !!}</p>
        <h2>{!! LR('privacy.article_titles.t1') !!}</h2>
        <p>{!! LR('privacy.article_body.p1') !!}</p>
        <h2>{!! LR('privacy.article_titles.t2') !!}</h2>
        <p><strong>{!! LR('privacy.article_body.p2') !!}</strong></p>
        <p></p>
        <h2>{!! LR('privacy.article_titles.t3') !!}</h2>
        <p>{!! LR('privacy.article_body.p3') !!}</p>
        <h2>{!! LR('privacy.article_titles.t4') !!}</h2>
        <p>{!! LR('privacy.article_body.p4') !!}</p>
        <h2>{!! LR('privacy.article_titles.t5') !!}</h2>
        <p>{!! LR('privacy.article_body.p5_0') !!}</p>
        <p>{!! LR('privacy.article_body.p5_1') !!}</p>
        <h2>{!! LR('privacy.article_titles.t6') !!}</h2>
        <p>{!! LR('privacy.article_body.p6_0') !!}</p>
        <p>{!! LR('privacy.article_body.p6_1') !!}</p>
        <p>{!! LR('privacy.article_body.p6_2') !!}</p>
        <p>&nbsp;</p>
        <h2>{!! LR('privacy.article_titles.t7') !!}</h2>
        <p>{!! LR('privacy.article_body.p7_0') !!}</p>
        <p>{!! LR('privacy.article_body.p7_1') !!}</p>
        <p><a style="margin: 0px; padding: 0px; border: 0px; outline: 0px; font-size: 13.333333969116211px; vertical-align: baseline; color: black; text-decoration: none; cursor: pointer; font-family: Verdana, sans-serif; line-height: 19.21111297607422px; text-align: start; background-image: initial; background-attachment: initial; background-size: initial; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: initial;" title="Hier klicken um das Webtracking zu unterbinden" href="http://www.netstatz.de/index.php?module=UsersManager&amp;action=setIgnoreCookie&amp;idSite=421&amp;period=day&amp;date=yesterday&amp;token_auth=7df72f8b488dd75fb9acd6d527437415#excludeCookie" target="_blank">{!! LR('privacy.article_body.p7_2') !!}</a></p>
        <p>{!! LR('privacy.article_body.p7_3') !!}</p>
        <p>{!! LR('privacy.article_body.p7_4') !!}</p>
        <p>{!! LR('privacy.article_body.p7_5') !!}</p>
        <p>{!! LR('privacy.article_body.p7_6') !!}</p>
        <h2>{!! LR('privacy.article_titles.t8') !!}</h2>
        <p>{!! LR('privacy.article_body.p8_0') !!}</p>
        <p>{!! LR('privacy.article_body.p8_1') !!}</p>
        <div class="clearfix"></div>
      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
