
<div class="donation-stub" data-ng-class="{'draft': !option.visible}" data-ng-controller="OptionStubController">

	@if ($adminMode)
	<div data-ng-if="option.redeem_type == REDEEM_TYPE_DONATING" class="show-when-parent-hover parent-top-right btn-group">
		<a class="btn btn-default" title="{{ trans('redeem.edit') }}" href="@{{ option.edit_link }}">
		  <span class="glyphicon glyphicon-pencil"></span>
		</a>
		<div data-ng-if="option.visible">
			<button type="button" class="btn btn-default" title="{{ trans('redeem.hide') }}" data-ng-click="setVisibility(false)">
			  <span class="glyphicon glyphicon-eye-close"></span>
			</button>
		</div>
		<div data-ng-if="!option.visible">
			<button type="button" class="btn btn-default" title="{{ trans('redeem.show') }}" data-ng-click="setVisibility(true)">
			  <span class="glyphicon glyphicon-eye-open"></span>
			</button>
		</div>
		<button type="button" data-ng-click="delete()" class="btn btn-default" title="{{ trans('redeem.delete') }}">
		  <span class="glyphicon glyphicon-trash"></span>
		</button>
	</div>
	@endif

	<div class="invisible" class="text-center">
		<button type="button" class="btn reward-button" disabled>NA</button>
	</div>

	<div data-ng-mouseover="mouseover=true" data-ng-mouseleave="mouseover=false" data-ng-click="redeemClicked()" class="pointer-hover">
		<div data-ng-hide="mouseover && option.thumbnail_mouseover_url" data-back-img="@{{option.thumbnail_url}}"  data-ng-class="{'donation-thumbnail': option.redeem_type === REDEEM_TYPE_DONATING, 'shop-thumbnail': option.redeem_type === REDEEM_TYPE_BONSUMING}">
		</div>
		<div data-ng-show="mouseover && option.thumbnail_mouseover_url" data-back-img="@{{option.thumbnail_mouseover_url}}"  data-ng-class="{'donation-thumbnail': option.redeem_type === REDEEM_TYPE_DONATING, 'shop-thumbnail': option.redeem_type === REDEEM_TYPE_BONSUMING}">
		</div>
	</div>

	<div class="text-center" data-ng-if="option.redeem_type === REDEEM_TYPE_BONSUMING">
		<div class="inline-block">
		{!! IMG('shop.ethically_grey', false, ['title' => trans('option.shop_criteria.ethically_manufactured'), 'class' => 'criteria-icon', 'data-ng-if' => '!option.shop_criteria[shopCriteriaMap.ethically_manufactured]']) !!}
		{!! IMG('shop.ethically', false, ['title' => trans('option.shop_criteria.ethically_manufactured'), 'class' => 'criteria-icon', 'data-ng-if' => 'option.shop_criteria[shopCriteriaMap.ethically_manufactured]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.animals_grey', false, ['title' => trans('option.shop_criteria.no_animal_experiments'), 'class' => 'criteria-icon', 'data-ng-if' => '!option.shop_criteria[shopCriteriaMap.no_animal_experiments]']) !!}
		{!! IMG('shop.animals', false, ['title' => trans('option.shop_criteria.no_animal_experiments'), 'class' => 'criteria-icon', 'data-ng-if' => 'option.shop_criteria[shopCriteriaMap.no_animal_experiments]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.resources_grey', false, ['title' => trans('option.shop_criteria.resource_efficient'), 'class' => 'criteria-icon', 'data-ng-if' => '!option.shop_criteria[shopCriteriaMap.resource_efficient]']) !!}
		{!! IMG('shop.resources', false, ['title' => trans('option.shop_criteria.resource_efficient'), 'class' => 'criteria-icon', 'data-ng-if' => 'option.shop_criteria[shopCriteriaMap.resource_efficient]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.healthy_grey', false, ['title' => trans('option.shop_criteria.healthy_materials'), 'class' => 'criteria-icon', 'data-ng-if' => '!option.shop_criteria[shopCriteriaMap.healthy_materials]']) !!}
		{!! IMG('shop.healthy', false, ['title' => trans('option.shop_criteria.healthy_materials'), 'class' => 'criteria-icon', 'data-ng-if' => 'option.shop_criteria[shopCriteriaMap.healthy_materials]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.co2_grey', false, ['title' => trans('option.shop_criteria.low_co2_footprint'), 'class' => 'criteria-icon', 'data-ng-if' => '!option.shop_criteria[shopCriteriaMap.low_co2_footprint]']) !!}
		{!! IMG('shop.co2', false, ['title' => trans('option.shop_criteria.low_co2_footprint'), 'class' => 'criteria-icon', 'data-ng-if' => 'option.shop_criteria[shopCriteriaMap.low_co2_footprint]']) !!}
		</div>
	</div>
	<div class="text-center" data-ng-if="option.redeem_type === REDEEM_TYPE_DONATING">
		{!! IMG('shop.ethically', false, ['class' => 'criteria-icon invisible']) !!}
	</div>

	<p class="donation-description" data-ng-bind-html="option.description" class="donation-description"></p>

</div>

