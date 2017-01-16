@extends('layouts.master')

{{--
	$visible
--}}

@section('content')

@include('partials.toolbar')

<!-- the fluid container is used to avoid directly styling the body (to have a gray bg) -->
<div class="container-fluid gray-background">
	<div class="row">

		<div class="container article-container" data-ng-cloak data-ng-controller="ArticleController">

			<div class="row vspace-above-15">
				{!! IMG('article.magazine_banner', null, ['class' => 'img-responsive magazine-banner col-xs-12']) !!}
			</div>

			@if ($adminMode)
			<div class="row vspace-above-15">
				<div class="col-sm-6">
				<h3 class="text-info"><i>These articles are {{ $visible ? '' : 'not' }} visible to users</i></h3>
				</div>
				<div class="parent-top-right btn-group">
					<a href="{{ action('ArticleController@create', ['visible' => $visible]) }}" type="button" class="btn btn-default" title="{{ trans('article.new') }}">
						<span class="glyphicon glyphicon-plus"></span>
					</a>
					<a href="{{ $visible ? action('ArticleController@indexInvisible') : action('ArticleController@index') }}" type="button" class="btn btn-default" title="{{ $visible ? trans('article.show_hidden') : trans('article.show_published') }}">
						<span class="glyphicon {{ $visible ? 'glyphicon-eye-close' : 'glyphicon-eye-open'   }}"></span>
					</a>
				</div>
			</div>
			@endif

			<div class="row vspace-above-15">
				{{-- since we have infinite-load, it only makes sense for search to be on the top,
						 when it can't be on the side (i.e. mobile size). In order to achieve this
						 and also have it on the right, as in the designs, bootstrap's col-sm-push-*,
						 col-sm-pull-* classes are used --}}
				<div class="col-sm-4 col-sm-push-8 col-lg-3 col-lg-push-9">
					<div sticky offset="300" media-query="min-width: 768px" class="article-side-search" bottom-line=".site-footer" confine="true">

						<div class="input-group">
							<input type="text" class="form-control" placeholder="{{ trans('article.search_placeholder') }}" data-ng-model="filter.searchString" data-ng-model-options="{ debounce: 500 }" data-ng-change="reloadArticles()">
							<span class="input-group-btn">
								<button type="button" class="btn btn-bonsum" disabled>
									<span class="glyphicon glyphicon-search"></span>
									</button>
							</span>
						</div>
						<div class="row">
							<div data-ng-cloak data-ng-repeat="item in sortingOptions" class="text-center col-sm-6 col-xs-6 article-sort-options" data-ng-class="{'col-sm-soffset-1': $first}">
								<label>
									<input type="radio" name="redeemModes" data-ng-value="item.value" data-ng-model="filter.sorting" data-ng-change="reloadArticles()">
									<small>@{{item.label}}</small>
								</label>
							</div>
						</div>

						<p class="text-center vspace-above-15 social-buttons hidden-xs">
							<span class='st_facebook_large' displayText='Facebook'></span>
							<span class='st_twitter_large' displayText='Tweet'></span>
							<span class='st_linkedin_large' displayText='LinkedIn'></span>
							<span class='st_googleplus_large' displayText='Google +'></span>
							<span class='st_email_large' displayText='Email'></span>
						</p>

						@include('partials.newsletter_widget', ["className" => "hidden-xs"])
						<a scroll-to-top data-hide=true class="btn btn-block btn-default hidden-xs" style="margin-top: -25px"> {!! IMG('general.back_to_top_icon', null, ['width' => 16]) !!} {!! LR('general.back_to_top') !!}</a>
					</div>
				</div>

				<div class="col-sm-8 col-sm-pull-4 col-lg-9 col-lg-pull-3" infinite-scroll-distance="1" infinite-scroll="loadMoreArticles()" data-ng-cloak>

					<div class="text-center fade-in" data-ng-show="totalArticles === 0 && !errorFetchingArticles && !pendingRequest">
						<h3>{!! LR('article.no_results') !!}</h3>
					</div>
					<div class="text-center alert alert-danger fade-in" data-ng-show="errorFetchingArticles">
						<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
						<span>{!! LR('article.error_fetching_articles') !!}</span>
					</div>

					<div class="row">
						<div class="col-sm-6 hidden-xs">
							<div class="animate-enter" data-ng-repeat="article in articleColumns[0]">
								@include('magazine.partials.stub_angular')
							</div>
						</div>
						<div class="col-sm-6 hidden-xs">
							<div class="animate-enter" data-ng-repeat="article in articleColumns[1]">
								@include('magazine.partials.stub_angular')
							</div>
						</div>
						<div class="col-xs-12 visible-xs">
							<div class="animate-enter" data-ng-repeat="article in articles">
								@include('magazine.partials.stub_angular')
							</div>
						</div>
					</div>

					<div class="clearfix"></div>
					<div class="text-center margin-vertical-15" data-ng-if="pendingRequest">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
