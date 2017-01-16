<div sticky offset="70" media-query="min-width: 768px" bottom-line=".site-footer" confine="true" class="filter-box">

	<div class="row">
		<p class="text-center">{!! LR('redeem.matching_options') !!}&nbsp;<span class="badge">@{{totalOptions}}</span></p>
	</div>

	<div class="input-group vspace-above-15">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default" disabled>
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</span>
		<input type="text" class="form-control" placeholder="{{ trans('redeem.search_placeholder') }}" data-ng-model="filter.searchString" data-ng-model-options="{ debounce: 500 }" data-ng-change="reloadOptions()">
	</div>

	<a scroll-to-top data-hide=true class="btn btn-block btn-default vspace-above-15"> {!! IMG('general.back_to_top_icon', null, ['width' => 16]) !!} {!! LR('general.back_to_top') !!}</a>
</div>
