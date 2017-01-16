@extends('layouts.master')

{{--
	$visible
--}}

@section('content')

@include('partials.toolbar')


<div class="container redeem-container" data-ng-cloak data-ng-controller="RedeemController">

	@if ($adminMode)
	<div class="parent-top-right btn-group">
		<a href="{{ action('RedeemController@create', ['visible' => $visible]) }}" type="button" class="btn btn-default" title="{{ trans('redeem.new') }}">
		  <span class="glyphicon glyphicon-plus"></span>
		</a>
		<a href="{{ $visible ? action('RedeemController@indexInvisible') : action('RedeemController@index') }}" type="button" class="btn btn-default" title="{{ $visible ? trans('redeem.show_hidden') : trans('redeem.show_published') }}">
		  <span class="glyphicon {{ $visible ? 'glyphicon-eye-close' : 'glyphicon-eye-open'   }}"></span>
		</a>
	</div>
	<div class="row">
		<div class="col-sm-6">
			@if ($visible)
			<h3 class="text-info"><i>These items are visible to users</i></h3>
			@else
			<h3 class="text-info"><i>These items are not visible to users</i></h3>
			@endif
		</div>
	</div>
	@endif

	<div class="row">

		<div class="col-sm-3">
			@include('redeem.partials.filter_box')
		</div>

		<div class="col-sm-9 vspace-xs-above-15" infinite-scroll-distance="1" infinite-scroll="loadMoreOptions()" data-ng-cloak>

			<div class="text-center fade-in" data-ng-show="totalOptions === 0 && !errorFetchingOptions && !pendingRequest">
				<h3>{!! LR('redeem.no_results') !!}</h3>
			</div>
			<div class="text-center alert alert-danger fade-in" data-ng-show="errorFetchingOptions">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span>{!! LR('redeem.error_fetching_redeems') !!}</span>
			</div>

			<div data-ng-repeat="optionRow in optionRows" class="animate-enter">
				<div class="row">
					<div class="col-sm-4 no-padding" data-ng-repeat="option in optionRow">
						@include('redeem.partials.stub_angular_option')
					</div>
				</div>
			</div>

			<div class="text-center margin-vertical-15" data-ng-if="pendingRequest">
				<i class="fa fa-spinner fa-spin fa-3x"></i>
			</div>

		</div>
	</div>
</div>


<script type="text/ng-template" id="redeem_modal.html">
	<div class="modal-body">
		<div class="row" data-ng-class="{'greyed-out': !currentUser}">
			<div class="col-sm-6">
				<div data-back-img="@{{option.thumbnail_url}}" data-ng-class="{'donation-thumbnail': option.redeem_type === REDEEM_TYPE_DONATING, 'shop-thumbnail': option.redeem_type === REDEEM_TYPE_BONSUMING}">
				</div>
			</div>

			<div class="col-sm-6">
				<p data-ng-if="option.redeem_type === REDEEM_TYPE_DONATING">
					{!! LR('redeem.donation_header') !!}
				</p>
				<p data-ng-if="option.redeem_type === REDEEM_TYPE_BONSUMING">
					{!! LR('redeem.redeem_header') !!}
				</p>
				<h4 class="text-center">
					@{{option.name}}
				</h4>
				<p data-ng-if="option.redeem_type === REDEEM_TYPE_DONATING">
					{!! LR('redeem.donation_size_selection') !!}
				</p>
				<p data-ng-if="option.redeem_type === REDEEM_TYPE_BONSUMING">
					{!! LR('redeem.voucher_size_selection') !!}
				</p>
				<div data-ng-if="option.redeem_type === REDEEM_TYPE_DONATING" data-ng-repeat="donation in option.donation_options | orderBy: 'value'">
					<button type="button" data-ng-disabled="!currentUser || currentUser.bonets < donation.value" data-ng-bind-html="donation.label" class="btn btn-block btn-primary vspace-above-5" data-ng-click="donate(donation.value, donation.success_message)">
					</button>
				</div>
				<div data-ng-if="option.redeem_type === REDEEM_TYPE_BONSUMING" data-ng-repeat="voucher in option.voucher_options | orderBy: 'value'">
					<button type="button" data-ng-disabled="!currentUser || currentUser.bonets < voucher.bonets_value" data-ng-bind-html="voucher.label" class="btn btn-block btn-primary vspace-above-5" data-ng-click="getVoucher(voucher.value, voucher.bonets_value, voucher.success_message)">
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<p class="text-center" data-ng-if="!currentUser">
			{!! LR('redeem.login_prompt', ['login_url' => action('Auth\AuthController@getLogin')]) !!}
		</p>
        <button class="btn btn-warning" data-ng-if="currentUser" data-ng-click="$close()">{!! LR('general.cancel') !!}</button>
    </div>
</script>


<script type="text/ng-template" id="redeem_success_modal.html">
<div class="modal-header">
	<h4>@{{header_message}}</h4>
</div>
<div class="modal-body">
		<div class="row">
			<div class="col-sm-6">
				<div data-back-img="@{{option.thumbnail_url}}" data-ng-class="{'donation-thumbnail': option.redeem_type === REDEEM_TYPE_DONATING, 'shop-thumbnail': option.redeem_type === REDEEM_TYPE_BONSUMING}">
				</div>
			</div>
			<div class="col-sm-6">
				<p data-ng-bind-html="success_message">
				</p>
				<h4 data-ng-if="voucher" class="text-center">
					@{{voucher}}
				</h4>
				<p data-ng-bind-html="success_coda">
				</p>
			</div>
		</div>
	</div>
	<div class="modal-footer">
        <button class="btn btn-warning" ng-click="$close()">{!! LR('general.ok') !!}</button>
    </div>
</script>


@endsection

