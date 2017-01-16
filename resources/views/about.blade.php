@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">
				<section class="padded-section">
					<h2 class="about-bonsum-team-header text-center">{!! LR('about.bonsum_team') !!}</h2>
				</section>
				<div class="about-profile-card-section row">
					<div class="col-sm-4 text-left text-xs-center">
						<div class="about-profile-card card-left">
							{!! IMG('about.michael', null, ['class' => 'img-responsive']) !!}
							<p class="vspace-above-15">
							  <span class="text-medium">{!! LR('about.michael_title') !!}</span><br>
								{!! LR('about.michael_linkedin') !!}
							</p>
							<p>{!! LR('about.michael_goals') !!}</p>
							<p>{!! LR('about.michael_interests') !!}</p>
							<p>&nbsp;</p>
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="about-profile-card">
							{!! IMG('about.frederic', null, ['class' => 'img-responsive']) !!}
							<p class="vspace-above-15">
							  <span class="text-medium">{!! LR('about.frederik_title') !!}</span><br>
								{!! LR('about.frederik_linkedin') !!}
							</p>
							<p>{!! LR('about.frederik_goals') !!}</p>
							<p>{!! LR('about.frederik_interests') !!}</p>
							<p>&nbsp;</p>
						</div>
					</div>
					<div class="col-sm-4 text-right text-xs-center">
						<div class="about-profile-card card-right">
							{!! IMG('about.max', null, ['class' => 'img-responsive']) !!}
							<p class="vspace-above-15">
							  <span class="text-medium">{!! LR('about.max_title') !!}</span><br>
								{!! LR('about.max_linkedin') !!}
							</p>
							<p>{!! LR('about.max_goals') !!}</p>
							<p>{!! LR('about.max_interests') !!}</p>
							<p>&nbsp;</p>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<section class="padded-section">
					<h1 class="text-center">{!! LR('about.about_bonsum') !!}</h1>
					<p>&nbsp;</p>
					<p>{!! LR('about.about_bonsum_text') !!}</p>
					<p>&nbsp;</p>
					<h2 class="text-center">{!! LR('about.our_vision') !!}</h2>
					<p>&nbsp;</p>
					<p>{!! LR('about.our_vision_text') !!}</p>
					<p>&nbsp;</p>
					<h2 class="text-center">{!! LR('about.who_we_are') !!}</h2>
					<p>{!! LR('about.who_we_are_text') !!}</p>
					<div class="text-center">
						<a href="{{action('Auth\AuthController@getSignup')}}"><br>
							<button class="btn btn-bonsum btn-white">{!! LR('about.register_for_free') !!}</button><br>
						</a>
					</div>
					<p>&nbsp;</p>
					<hr>
					<h2 class="text-center">{!! LR('about.bonsum_sustainability') !!}</h2>
					<p>&nbsp;</p>
					<p>{!! LR('about.bonsum_sustainability_text') !!}</p>

					<p>
						<a id="Greenwill" href="/media/img/greenwill/greenwill_bonsum.png" target="_blank">
							{!! IMG('about.greenwill', null, ['class' => 'pull-left', 'style' => 'margin: 5px 15px 10px 0px;', 'width' => 164, 'height' => 164]) !!}
						</a>
					</p>
					<p>
						<a title="Bonsum - Green Policy" href="/media/img/greenwill/green_policy.png" target="_blank">
							{!! IMG('about.green_policy', null, ['class' => '', 'width' => 133, 'height' => 188]) !!}
						</a>
						<a href="/media/img/greenwill/green_strategy.png" target="_blank">
							{!! IMG('about.green_strategy', null, ['class' => '', 'width' => 137, 'height' => 194]) !!}
						</a>
					</p>

					<p><strong>{!! LR('about.our_green_strategy') !!}</strong>{!! LR('about.our_green_strategy_text.p0') !!}</p>
					<p>{!! LR('about.our_green_strategy_text.p1') !!}</p>
					<p>&nbsp;</p>
					<div class="text-center">
						<a href="{{action('Auth\AuthController@getSignup')}}">
							<button class="btn btn-bonsum btn-white">{!! LR('about.register_for_free') !!}</button><br>
						</a>
					</div>
				</section>
					<p>
						<strong>{!! LR('about.have_questions') !!}</strong> {!! LR('about.contact_us_at') !!} <strong> <a title="info@bonsum.de" href="mailto:info@bonsum.de" target="_blank">info@bonsum.de</a>.</strong>
					</p>
      </div>
    </div> <!-- main-section container -->
  </section>
@endsection
