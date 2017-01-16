@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">

				<div class="lined-header">
					<h1 class="section-title">{!! LR('donate-bonets.titles.donate_bonets') !!}</h1>
				</div>
				<p>{!! LR('donate-bonets.body.p0') !!}</p>
				<p>{!! LR('donate-bonets.body.p1') !!}</p>
				<div class="text-center row">
					<div class="col-xs-4">
						{!! IMG('donate-bonets.gutes-tun-rund', null, ['class' => 'img-responsive', 'style' => 'margin: 10px auto', 'width' => 250, 'height' => 250]) !!}
					</div>
					<div class="col-xs-4">
						{!! IMG('donate-bonets.phnx_05', null, ['class' => 'img-responsive', 'style' => 'margin: 10px auto', 'width' => 250, 'height' => 250]) !!}
					</div>
					<div class="col-xs-4">
						{!! IMG('donate-bonets.spendenpartner', null, ['class' => 'img-responsive', 'style' => 'margin: 10px auto', 'width' => 250, 'height' => 250]) !!}
					</div>
				</div>
				<p><br></p>
				<p>{!! LR('donate-bonets.body.p2') !!}</p>
				<p>{!! LR('donate-bonets.body.p3') !!}</p>
				<p>{!! LR('donate-bonets.body.p4') !!}</p>
				<p></p>
				<h3>{!! LR('donate-bonets.titles.other_social_projects') !!}</h3>
				<p>{!! LR('donate-bonets.body.p5') !!}</p>
      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
