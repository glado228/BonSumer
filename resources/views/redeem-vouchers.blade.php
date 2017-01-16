@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">

				<div class="lined-header">
					<h1 class="section-title">{!! LR('redeem-vouchers.title') !!}</h1>
				</div>
				<p>{!! LR('redeem-vouchers.body.p0') !!}</p>
				<p>{!! LR('redeem-vouchers.body.p1') !!}</p>
				<p>{!! LR('redeem-vouchers.body.p2') !!}</p>
				<p>&nbsp;</p>
				<div class="text-center row">
					<div class="col-xs-4">
						{!! IMG('redeem-vouchers.bild-online-kaufen', null, ['class' => 'img-responsive', 'style' => 'margin: 10px auto', 'width' => 280, 'height' => 280]) !!}
					</div>
					<div class="col-xs-4">
						{!! IMG('redeem-vouchers.bild-sammeln', null, ['class' => 'img-responsive', 'style' => 'margin: 10px auto', 'width' => 280, 'height' => 280]) !!}
					</div>
					<div class="col-xs-4">
						{!! IMG('redeem-vouchers.bild-bonsum-schein', null, ['class' => 'img-responsive', 'style' => 'margin: 10px auto', 'width' => 310, 'height' => 310]) !!}
					</div>
				</div>
				<p>&nbsp;</p>
				<p>{!! LR('redeem-vouchers.body.p3') !!}</p>
				<p>{!! LR('redeem-vouchers.body.p4') !!}</p>
				<p>{!! LR('redeem-vouchers.body.p5') !!}</p>
				<p>{!! LR('redeem-vouchers.body.p6') !!}</p>
				<p>&nbsp;</p>

      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
