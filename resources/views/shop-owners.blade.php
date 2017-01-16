@extends('layouts.master')

@section('content')

@include('partials.toolbar')
	<div class="container">
		<div class="row disable-xs">
			<section class="padded-section">
				{!! IMG('shop-owners.partner_header', null, ['class' => 'img-responsive']) !!}

				<hr>

				<h1 class="text-center">{!! LR('shop-owners.have_web_store') !!}</h1>
				<p></p>
				<p>&nbsp;</p>
				<div class="text-center">
					<h3>{!! LR('shop-owners.bonsum_means') !!}</h3>
					<p><strong>{!! LR('shop-owners.conversion_rate') !!}</strong></p>
					<p><strong>{!! LR('shop-owners.acquisition') !!}</strong></p>
					<p><strong>{!! LR('shop-owners.brand_awareness') !!}</strong></p>
				</div>
				<p>&nbsp;</p>

				<hr>

				<h2 class="text-center">{!! LR('shop-owners.looking_for_innovation') !!}</h2>
				<p>{!! LR('shop-owners.sustainability_criteria') !!}</p>


				<p>
					{!! IMG('shop-owners.seals.fair', null, [ 'class' => 'img-responsive pull-right', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}
					<strong><em>{!! LR('criteria.fair_title') !!}</em></strong>
				</p>
				<p>
					{!! LR('howto.fair_text') !!}
				</p>
				<p>&nbsp;</p>
				<p>
					{!! IMG('shop-owners.seals.economic', null, [ 'class' => 'img-responsive pull-left', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}
					<strong><em>{!! LR('criteria.economic_title') !!}</em></strong>
				</p>
				<p>
					{!! LR('criteria.economic_text') !!}
				</p>
				<p>&nbsp;</p>
				<p>
					{!! IMG('shop-owners.seals.recycle', null, [ 'class' => 'img-responsive pull-right', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}
					<em><strong>{!! LR('criteria.recycle_title') !!}</strong></em>
				</p>
				<p>
					{!! LR('criteria.recycle_text') !!}
				</p>
				<p>&nbsp;</p>
				<p>
					{!! IMG('shop-owners.seals.detox', null, [ 'class' => 'img-responsive pull-left', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}
					<strong><em>{!! LR('criteria.detox_title') !!}</em></strong>
				</p>
				<p>
					{!! LR('criteria.detox_text') !!}
				</p>
				<p>&nbsp;</p>
				<p>
					{!! IMG('shop-owners.seals.renewable', null, [ 'class' => 'img-responsive pull-right', 'style' => 'margin: 15px 15px 15px 15px;', 'width' => 100, 'height' => 100]) !!}
					<strong><em>{!! LR('criteria.renewable_title') !!}</em></strong>
				</p>
				<p>
					{!! LR('criteria.renewable_text') !!}
				</p>

				<p>&nbsp;</p>

				<section class="padded-section">
					<div class="text-center">
						<a href="mailto:partner@bonsum.de"><br>
							<button class="btn btn-bonsum btn-white">{!! LR('shop-owners.become_partner') !!}</button><br>
						</a>
					</div>
				</section>
			</section> <!-- end padded section -->
			<hr>
			<p><strong>{!! LR('shop-owners.get_in_touch') !!}</strong></p>
			<p><a href="mailto:partner@bonsum.de">partner@bonsum.de</a> | Tel: +49 (0) 30 64312659</p>
			<p>&nbsp;</p>
		</div>
	</div> <!-- main container -->
@endsection
