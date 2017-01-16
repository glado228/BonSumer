@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">
        <section class="padded-section">
          <h1>{!! LR('press.press') !!}</h1>
          <p>{!! LR('press.latest_news') !!}<br>
          {!! IMG('press.betz', null, ['class' => 'pull-left img-responsive', 'width' => 311, 'height' => 223, 'style' => 'margin: 20px 20px 10px 0px;']) !!}</p>
          <h3>{!! LR('press.talk') !!}</h3>
          <p>{!! LR('press.contact_person') !!}</p>
          <p><strong>Frederik Betz</strong></p>
          <p>(Co-Founder / CMO)</p>
          <p>+49 (0)&nbsp;160 8487493</p>
          <p>frederik.betz@bonsum.de</p>
          <div class="clearfix"></div>
          <div>
            <br>
            <h2>{!! LR('press.press_releases') !!}</h2>
            <!-- TODO fetch files on new site and link to them. Also, do press release title need to be localized
                 if their text isn't? -->
            <p><a title="Bonsum Launch" href="{{asset('media/files/press/DE/Bonsum-Shopping-for-a-better-world.pdf')}}" target="_blank">01.01.2015 | Bonsum – Shopping for a better world</a></p>
            <p><a title="Bonsum Launch" href="{{asset('media/files/press/DE/Bonsum-Nachhaltig-Shoppen-2.0.pdf')}}" target="_blank">27.01.2015 | Nachhaltig Shoppen 2.0: Bonsum wird Deutschlands größtes Bonusprogramm für nachhaltigen Konsum</a></p>

            <p>&nbsp;</p>
            <h2>{!! LR('press.press_review') !!}</h2>
            <p>
              {!! IMG('press.biorama', FALSE, ['class' => 'pull-left img-responsive', 'width' => 150, 'height' => 41, 'style' => 'margin: 5px 15px 10px 0px;']) !!}
            </p>
            <div class="clearfix visible-xs"></div>
            <p class="clearfix">
              <a title="Lichtblick im Öko-Dschungel" href="http://www.biorama.eu/bonsum/" rel="nofollow" target="_blank">{!! LR('press.shopping_assistance') !!}</a><br>
            </p>
            <p>
              {!! IMG('press.enorm', FALSE, ['class' => 'pull-left img-responsive', 'width' => 150, 'height' => 41, 'style' => 'margin: 5px 15px 10px 0px;']) !!}
            </p>
            <div class="clearfix visible-xs"></div>
            <p class="clearfix">
              <a title="Lichtblick im Öko-Dschungel" href="https://enorm-magazin.de/lichtblicke-im-oeko-dschungel" rel="nofollow" target="_blank">{!! LR('press.eco_jungle') !!}</a><br>
            </p>
            <p>
              {!! IMG('press.crowdfunding_berlin', FALSE, ['class' => 'pull-left img-responsive', 'width' => 150, 'height' => 41, 'style' => 'margin: 5px 15px 10px 0px;']) !!}
            </p>
            <div class="clearfix visible-xs"></div>
            <p class="clearfix">
              <a title="Wir haben es selbst in der Hand" href="http://www.crowdfunding-berlin.com/de/magazin/interviews/2014/12/10/interview-bonsum/" rel="nofollow" target="_blank">{!! LR('press.interview_frederic') !!}</a><br>
            </p>
            <p>
              {!! IMG('press.changer', FALSE, ['class' => 'pull-left img-responsive', 'width' => 150, 'height' => 41, 'style' => 'margin: 5px 15px 10px 0px;']) !!}
            </p>
            <div class="clearfix visible-xs"></div>
            <p class="clearfix">
              <a title="Michael Weber (CEO) im Interview" href="http://www.thechanger.org/blog-de/interview-mit-michael-weber-von-bonsum/?lang=de" rel="nofollow" target="_blank">{!! LR('press.interview_michael') !!}</a><br>
            </p>
            <p>&nbsp;</p>
          </div>

          <h2>{!! LR('press.downloads') !!}</h2>
          <p>{!! LR('press.logo') !!}<br>
            <a href="{{asset('/media/img/press/bonsum-logo.png')}}" target="_blank">{!! LR('press.download') !!}</a>
          </p>
          <p>{!! LR('press.logo_tagline') !!}<br>
            <a href="{{asset('/media/img/press/bonsum-logo-tagline.png')}}" target="_blank">{!! LR('press.download') !!}</a>
          </p>
          <p>{!! LR('press.team') !!}<br>
            <a href="{{asset('/media/img/press/bonsum-team.jpg')}}" target="_blank">{!! LR('press.download') !!}</a>
          </p>
          <p>{!! LR('press.keyvisual') !!}<br>
            <a href="{{asset('/media/img/press/bonsum-header.jpg')}}" target="_blank">{!! LR('press.download') !!}</a>
          </p>
        </section>

      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
