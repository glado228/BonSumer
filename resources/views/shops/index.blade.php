@extends('layouts.master')

{{--
	$visible
--}}

@section('content')

@include('partials.toolbar')


<div class="container shop-container" data-ng-cloak data-ng-controller="ShopController">

	@if ($adminMode)
		<div class="parent-top-right btn-group">
			<a href="{{ action('ShopController@create', ['visible' => $visible]) }}" type="button" class="btn btn-default" title="{{ trans('shop.new') }}">
			  <span class="glyphicon glyphicon-plus"></span>
			</a>
			<a href="{{ $visible ? action('ShopController@indexInvisible') : action('ShopController@index') }}" type="button" class="btn btn-default" title="{{ $visible ? trans('shop.show_hidden') : trans('shop.show_published') }}">
			  <span class="glyphicon {{ $visible ? 'glyphicon-eye-close' : 'glyphicon-eye-open'   }}"></span>
			</a>
		</div>
		<div class="row">
		<div class="col-sm-6">
			@if ($visible)
			<h3 class="text-info"><i>These shops are visible to users</i></h3>
			@else
			<h3 class="text-info"><i>These shops are not visible to users</i></h3>
			@endif
		</div>
		</div>
	@endif

	<div class="row">

		<div class="col-sm-3">
			@include('shops.partials.filter_box')
		</div>

		<div class="col-sm-9 vspace-xs-above-15" infinite-scroll-distance="1" infinite-scroll="loadMoreShops()" data-ng-cloak>

			<div class="text-center fade-in" data-ng-show="totalShops === 0 && !errorFetchingShops && !pendingRequest">
				<h3>{!! LR('shop.no_results') !!}</h3>
			</div>
			<div class="text-center alert alert-danger fade-in" data-ng-show="errorFetchingShops">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span>{!! LR('shop.error_fetching_shops') !!}</span>
			</div>

			<div data-ng-repeat="shopRow in shopRows" class="animate-enter">
				<div class="row">
					<div class="col-sm-4 no-padding" data-ng-repeat="shop in shopRow">
						@include('shops.partials.stub_angular')
					</div>
				</div>
			</div>

			<div class="text-center margin-vertical-15" data-ng-if="pendingRequest">
				<i class="fa fa-spinner fa-spin fa-3x"></i>
			</div>

		</div>

	</div>
</div>

<script type="text/ng-template" id="shop_forwarding_modal.html">
<div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div data-back-img="@{{shop.thumbnail_url}}" class="shop-thumbnail">
				</div>
			</div>
			<div class="col-sm-6">
				<p>
					{!! LR('shop.about_to_redirect') !!}
				</p>
				<h4 class="text-center">
					@{{shop.name}}
				</h4>
				<p>
					{!! LR('shop.about_to_redirect_coda') !!}
				</p>
			</div>
		</div>
	</div>
	<div class="modal-footer text-xs-center">
        <button class="btn btn-success" ng-click="$close()">{!! LR('shop.go_to_the_shop') !!}</button>
        @if (Auth::guest())
        <button class="btn btn-info hidden-xs" ng-click="$close(true)"> {!! LR('shop.go_to_shop_no_bonets') !!}</button>
        @endif
        <button class="btn btn-warning" ng-click="$dismiss()">{!! LR('general.cancel') !!}</button>
    </div>
</script>

@endsection
