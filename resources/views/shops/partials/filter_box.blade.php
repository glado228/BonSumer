<div sticky offset="70" media-query="min-width: 768px" bottom-line=".site-footer" confine="true" class="filter-box">

	<div class="row">
		<p class="text-center">{!! LR('shop.matching_shops') !!}&nbsp;<span class="badge">@{{totalShops}}</span></p>
	</div>

	<div class="input-group">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default" disabled>
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</span>
		<input type="text" class="form-control" placeholder="{{ trans('shop.search_placeholder') }}" data-ng-model="filter.searchString" data-ng-model-options="{ debounce: 500 }" data-ng-change="reloadShops()">
	</div>

	<fieldset>
		<div class="vspace-above-15 hidden-xs">
			<h5>{!! LR('shop.category_search') !!}</h5>
			@include('partials.filter_categories')
		</div>

		<div class="input-group vspace-above-15 hidden-xs">
			<h5>{!! LR('shop.refine_search') !!}</h5>
			@include('partials.filter_criteria')
		</div>

	</fieldset>

	<a scroll-to-top data-hide=true class="btn btn-block btn-default vspace-above-15"> {!! IMG('general.back_to_top_icon', null, ['width' => 16]) !!} {!! LR('general.back_to_top') !!}</a>
</div>
