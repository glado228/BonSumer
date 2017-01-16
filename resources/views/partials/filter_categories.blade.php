<div class="categories-select">
	<div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.home') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[4]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.home', false, [ 'title' => trans('shop.type.home'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(4)']) !!}
				{!! IMG('shop.type.off.home', false, [ 'title' => trans('shop.type.home'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(4)']) !!}
				</label>
			</div>
		</div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.finance') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[0]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.finance', false, [ 'title' => trans('shop.type.finance'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(0)']) !!}
				{!! IMG('shop.type.off.finance', false, [ 'title' => trans('shop.type.finance'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(0)']) !!}
				</label>
			</div>
		</div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.pets') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[3]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.pets', false, [ 'title' => trans('shop.type.pets'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(3)']) !!}
				{!! IMG('shop.type.off.pets', false, [ 'title' => trans('shop.type.pets'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(3)']) !!}
				</label>
			</div>
		</div>
	</div>

	<div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.lifestyle') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[2]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.lifestyle', false, [ 'title' => trans('shop.type.lifestyle'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(2)']) !!}
				{!! IMG('shop.type.off.lifestyle', false, [ 'title' => trans('shop.type.lifestyle'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(2)']) !!}
				</label>
			</div>
		</div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.technology') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[1]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.technology', false, [ 'title' => trans('shop.type.technology'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(1)']) !!}
				{!! IMG('shop.type.off.technology', false, [ 'title' => trans('shop.type.technology'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(1)']) !!}
				</label>
			</div>
		</div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.baby') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[5]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.baby', false, [ 'title' => trans('shop.type.baby'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(5)']) !!}
				{!! IMG('shop.type.off.baby', false, [ 'title' => trans('shop.type.baby'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(5)']) !!}
				</label>
			</div>
		</div>
	</div>
	<div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.food') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[7]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.food', false, [ 'title' => trans('shop.type.food'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(7)']) !!}
				{!! IMG('shop.type.off.food', false, [ 'title' => trans('shop.type.food'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(7)']) !!}
				</label>
			</div>
		</div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.fashion') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[8]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.fashion', false, [ 'title' => trans('shop.type.fashion'), 'width' => '48','height' => '48', 'data-ng-if' => 'typeVisible(8)']) !!}
				{!! IMG('shop.type.off.fashion', false, [ 'title' => trans('shop.type.fashion'), 'width' => '48','height' => '48', 'data-ng-if' => '!typeVisible(8)']) !!}
				</label>
			</div>
		</div>
		<div>
			<div class="checkbox" popover="{!! trans('shop.type.beauty') !!}" popover-trigger="mouseenter">
				<label>
				{!! Form::checkbox('', 1, NULL, ['data-ng-model' => 'filter.types[6]', 'data-ng-change' => 'reload()', ]) !!}
				{!! IMG('shop.type.on.beauty', false, [ 'title' => trans('shop.type.beauty'), 'width' => '38','height' => '38', 'data-ng-if' => 'typeVisible(6)']) !!}
				{!! IMG('shop.type.off.beauty', false, [ 'title' => trans('shop.type.beauty'), 'width' => '38','height' => '38', 'data-ng-if' => '!typeVisible(6)']) !!}
				</label>
			</div>
		</div>
	</div>
</div>
