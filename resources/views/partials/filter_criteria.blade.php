<div class="checkbox">
	<label>
		{!! Form::checkbox('', 1, NULL, [ 'data-ng-model' => 'filter.criteria[0]', 'data-ng-change' => 'reload()', ]) !!}
		{!! IMG('shop.ethically_grey', false, ['title' => trans('shop.shop_criteria.ethically_manufactured'), 'width' => '30','height' => '30', 'data-ng-if' => '!filter.criteria[0]']) !!}
		{!! IMG('shop.ethically', false, ['title' => trans('shop.shop_criteria.ethically_manufactured'), 'width' => '30', 'height' => '30', 'data-ng-if' => 'filter.criteria[0]']) !!}
		{!! LR('shop.shop_criteria.ethically_manufactured') !!}
	</label>
</div>
<div class="checkbox">
	<label>
		{!! Form::checkbox('', 1, NULL, [ 'data-ng-model' => 'filter.criteria[1]', 'data-ng-change' => 'reload()', ]) !!}
		{!! IMG('shop.animals_grey', false, ['title' => trans('shop.shop_criteria.no_animal_experiments'), 'width' => '30','height' => '30', 'data-ng-if' => '!filter.criteria[1]']) !!}
		{!! IMG('shop.animals', false, ['title' => trans('shop.shop_criteria.no_animal_experiments'), 'width' => '30', 'height' => '30', 'data-ng-if' => 'filter.criteria[1]']) !!}
		{!! LR('shop.shop_criteria.no_animal_experiments') !!}
	</label>
</div>
<div class="checkbox">
	<label>
		{!! Form::checkbox('', 1, NULL, [ 'data-ng-model' => 'filter.criteria[2]', 'data-ng-change' => 'reload()', ]) !!}
		{!! IMG('shop.resources_grey', false, ['title' => trans('shop.shop_criteria.resource_efficient'), 'width' => '30','height' => '30', 'data-ng-if' => '!filter.criteria[2]']) !!}
		{!! IMG('shop.resources', false, ['title' => trans('shop.shop_criteria.resource_efficient'), 'width' => '30', 'height' => '30', 'data-ng-if' => 'filter.criteria[2]']) !!}
		{!! LR('shop.shop_criteria.resource_efficient') !!}
	</label>
</div>
<div class="checkbox">
	<label>
		{!! Form::checkbox('', 1, NULL, [ 'data-ng-model' => 'filter.criteria[3]', 'data-ng-change' => 'reload()', ]) !!}
		{!! IMG('shop.healthy_grey', false, ['title' => trans('shop.shop_criteria.healthy_materials'), 'width' => '30','height' => '30', 'data-ng-if' => '!filter.criteria[3]']) !!}
		{!! IMG('shop.healthy', false, ['title' => trans('shop.shop_criteria.healthy_materials'), 'width' => '30', 'height' => '30', 'data-ng-if' => 'filter.criteria[3]']) !!}
		{!! LR('shop.shop_criteria.healthy_materials') !!}
	</label>
</div>
<div class="checkbox">
	<label>
		{!! Form::checkbox('', 1, NULL, [ 'data-ng-model' => 'filter.criteria[4]', 'data-ng-change' => 'reload()', ]) !!}
		{!! IMG('shop.co2_grey', false, ['title' => trans('shop.shop_criteria.low_co2_footprint'), 'width' => '30','height' => '30', 'data-ng-if' => '!filter.criteria[4]']) !!}
		{!! IMG('shop.co2', false, ['title' => trans('shop.shop_criteria.low_co2_footprint'), 'width' => '30', 'height' => '30', 'data-ng-if' => 'filter.criteria[4]']) !!}
		{!! LR('shop.shop_criteria.low_co2_footprint') !!}
	</label>
</div>
