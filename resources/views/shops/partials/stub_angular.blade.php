
<div class="shop-stub" data-ng-class="{'draft': !shop.visible}" data-ng-controller="ShopStubController">

	@if ($adminMode)
	<div class="show-when-parent-hover parent-top-right btn-group">
		<a class="btn btn-default" title="{{ trans('shop.edit') }}" href="@{{ shop.edit_link }}">
		  <span class="glyphicon glyphicon-pencil"></span>
		</a>
		<div data-ng-if="shop.visible">
			<button type="button" class="btn btn-default" title="{{ trans('shop.hide') }}" data-ng-click="setVisibility(false)">
			  <span class="glyphicon glyphicon-eye-close"></span>
			</button>
		</div>
		<div data-ng-if="!shop.visible">
			<button type="button" class="btn btn-default" title="{{ trans('shop.show') }}" data-ng-click="setVisibility(true)">
			  <span class="glyphicon glyphicon-eye-open"></span>
			</button>
		</div>
		<button type="button" data-ng-click="delete()" class="btn btn-default" title="{{ trans('shop.delete') }}">
		  <span class="glyphicon glyphicon-trash"></span>
		</button>
	</div>
	@endif

	<div data-ng-class="{invisible: !shop.bonets_per}" class="text-center">
		<button type="button" class="btn reward-button" data-ng-bind-html="shop.bonets_per || 'NA'" disabled></button>
	</div>

	<div data-ng-mouseover="mouseover=true" data-ng-mouseleave="mouseover=false" data-ng-click="shopClicked()" class="pointer-hover">
		<div data-ng-hide="mouseover && shop.thumbnail_mouseover_url" data-back-img="@{{shop.thumbnail_url}}" class="shop-thumbnail">
		</div>
		<div data-ng-show="mouseover && shop.thumbnail_mouseover_url" data-back-img="@{{shop.thumbnail_mouseover_url}}" class="shop-thumbnail">
		</div>
	</div>

	<div class="text-center">
		<div class="inline-block">
		{!! IMG('shop.ethically_grey', false, ['title' => trans('shop.shop_criteria.ethically_manufactured'), 'class' => 'criteria-icon', 'data-ng-if' => '!shop.shop_criteria[shopCriteriaMap.ethically_manufactured]']) !!}
		{!! IMG('shop.ethically', false, ['title' => trans('shop.shop_criteria.ethically_manufactured'), 'class' => 'criteria-icon', 'data-ng-if' => 'shop.shop_criteria[shopCriteriaMap.ethically_manufactured]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.animals_grey', false, ['title' => trans('shop.shop_criteria.no_animal_experiments'), 'class' => 'criteria-icon', 'data-ng-if' => '!shop.shop_criteria[shopCriteriaMap.no_animal_experiments]']) !!}
		{!! IMG('shop.animals', false, ['title' => trans('shop.shop_criteria.no_animal_experiments'), 'class' => 'criteria-icon', 'data-ng-if' => 'shop.shop_criteria[shopCriteriaMap.no_animal_experiments]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.resources_grey', false, ['title' => trans('shop.shop_criteria.resource_efficient'), 'class' => 'criteria-icon', 'data-ng-if' => '!shop.shop_criteria[shopCriteriaMap.resource_efficient]']) !!}
		{!! IMG('shop.resources', false, ['title' => trans('shop.shop_criteria.resource_efficient'), 'class' => 'criteria-icon', 'data-ng-if' => 'shop.shop_criteria[shopCriteriaMap.resource_efficient]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.healthy_grey', false, ['title' => trans('shop.shop_criteria.healthy_materials'), 'class' => 'criteria-icon', 'data-ng-if' => '!shop.shop_criteria[shopCriteriaMap.healthy_materials]']) !!}
		{!! IMG('shop.healthy', false, ['title' => trans('shop.shop_criteria.healthy_materials'), 'class' => 'criteria-icon', 'data-ng-if' => 'shop.shop_criteria[shopCriteriaMap.healthy_materials]']) !!}
		</div>
		<div class="inline-block">
		{!! IMG('shop.co2_grey', false, ['title' => trans('shop.shop_criteria.low_co2_footprint'), 'class' => 'criteria-icon', 'data-ng-if' => '!shop.shop_criteria[shopCriteriaMap.low_co2_footprint]']) !!}
		{!! IMG('shop.co2', false, ['title' => trans('shop.shop_criteria.low_co2_footprint'), 'class' => 'criteria-icon', 'data-ng-if' => 'shop.shop_criteria[shopCriteriaMap.low_co2_footprint]']) !!}
		</div>
	</div>

	<p class="shop-description" data-ng-bind-html="shop.description" class="shop-description"></p>

</div>

