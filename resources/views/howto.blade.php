@extends('layouts.master')

@section('content')

@include('partials.toolbar')

	<section class="howto-video container-fluid">
		<div class="lined-header">
			<h1 class="section-title">{!! LR('howto.how_it_works') !!}</h1>
		</div>
		<div class="video-container text-center">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/TZu4-AxLg-s" frameborder="0" allowfullscreen></iframe>
		</div>
	</section>
    <div class="container">
      <div class="row disable-xs">
				<section class="padded-section howto-specifications">
					<p class="text-xs-center">{!! IMG('howto.safe_sustainable', null, [ 'class' => 'img-responsive pull-left', 'style' => 'margin: 15px 15px 15px 0px;', 'width' => 200, 'height' => 200]) !!}</p>
					<h1 class="text-left">{!! LR('howto.safe_sustainable') !!}</h1>
					<p>{!! LR('howto.safe_sustainable_text') !!}</p>
					<div class="clearfix"></div>
					<p class="text-xs-center">{!! IMG('howto.collect', null, [ 'class' => 'img-responsive pull-right', 'style' => 'margin: 0 15px 15px 15px;', 'width' => 200, 'height' => 200]) !!}</p>
					<div class="text-left">
						<h2>{!! LR('howto.collect_bonets') !!}</h2>
						<p>{!! LR('howto.collect_bonets_text') !!}</p>
						<div class="clearfix"></div>
					</div>
					<p class="text-xs-center">{!! IMG('howto.redeem', null, [ 'class' => 'img-responsive pull-left', 'style' => 'margin: 15px 15px 15px 0px;', 'width' => 200, 'height' => 200]) !!}</p>
					<div class="text-left">
						<h2>{!! LR('howto.redeem_bonets') !!}</h2>
						<p>{!! LR('howto.redeem_bonets_text') !!}</p>
						<div class="clearfix"></div>
					</div>
					<div class="text-center">
						<a href="{{action('Auth\AuthController@getSignup')}}"><br>
							<button class="btn btn-bonsum btn-white">{!! LR('about.register_for_free') !!}</button><br>
						</a>
					</div>
				</section>
				<section class="padded-section">
					<div class="lined-header">
						<h2 class="section-title">{!! LR('howto.our_specifications') !!}</h2>
					</div>
					<h3>{!! IMG('howto.seals.fair', null, [ 'class' => 'img-responsive pull-right', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}<strong><em>{!! LR('criteria.fair_title') !!}</em></strong></h3>
					<p>{!! LR('criteria.fair_text') !!}</p>
					<p>&nbsp;</p>
					<h3>{!! IMG('howto.seals.economic', null, [ 'class' => 'img-responsive pull-left', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}<strong><em>{!! LR('criteria.economic_title') !!}</em></strong></h3>
					<p>{!! LR('criteria.economic_text') !!}</p>
					<p>&nbsp;</p>
					<h3>{!! IMG('howto.seals.recycle', null, [ 'class' => 'img-responsive pull-right', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}<em><strong>{!! LR('criteria.recycle_title') !!}</strong></em></h3>
					<p>{!! LR('criteria.recycle_text') !!}</p>
					<p>&nbsp;</p>
					<h3>{!! IMG('howto.seals.detox', null, [ 'class' => 'img-responsive pull-left', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}<strong><em>{!! LR('criteria.detox_title') !!}</em></strong></h3>
					<p>{!! LR('criteria.detox_text') !!}</p>
					<p>&nbsp;</p>
					<h3>{!! IMG('howto.seals.renewable', null, [ 'class' => 'img-responsive pull-right', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}<strong><em>{!! LR('criteria.renewable_title') !!}</em></strong></h3>
					<p>{!! LR('criteria.renewable_text') !!}</p>
					<p>&nbsp;</p>
				</section>

				<section class="customers-say">
					<div class="lined-header">
						<h2 class="section-title">{!! LR('howto.customers_say') !!}</h2>
					</div>
					<div>
						<div class="col-sm-4">
							<p>{!! IMG('howto.Mimi', null, [ 'class' => 'img-responsive img-circle', 'width' => 195, 'height' => 195]) !!}</p>
							<h3 class="text-medium">Mimi</h3>
							<p>{!! LR('howto.customers.mimi') !!}</p>
							<p>&nbsp;</p>
						</div>
						<div class="col-sm-4">
							<p>{!! IMG('howto.Carl_Christian', null, [ 'class' => 'img-responsive img-circle', 'width' => 195, 'height' => 195]) !!}</p>
							<h3 class="text-medium">Carl-Christian</h3>
							<p>{!! LR('howto.customers.carl_christian') !!}</p>
							<p>&nbsp;</p>
						</div>
						<div class="col-sm-4">
							<p>{!! IMG('howto.Sabrina', null, [ 'class' => 'img-responsive img-circle', 'width' => 195, 'height' => 195]) !!}</p>
							<h3 class="text-medium">Sabrina</h3>
							<p>{!! LR('howto.customers.sabrina') !!}</p>
							<p>&nbsp;</p>
						</div>
					</div>
				</section>
      </div>
    </div> <!-- container -->
@endsection
