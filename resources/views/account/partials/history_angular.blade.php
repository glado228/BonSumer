<script type="text/ng-template" id="bonets.html">
	<table class="table table-responsive">
		<thead>
			<tr class="success">
				<th>{!! LR('account.bonets_available') !!}</th>
				<th>@{{overview.available}}</th>
			</tr>
			<tr>
				<td>{!! LR('account.bonets_pending') !!}</th>
				<td>@{{overview.pending}}</th>
			</tr>
			<tr>
				<td>{!! LR('account.bonets_total') !!}</th>
				<td>@{{overview.total}}</th>
			</tr>
		</thead>
	</table>

	@if ($editingOtherUser)
		{!! Form::open(['url' => action('AccountController@fetchHistoryAdmin', ['user_id' => $user->id]) ]) !!}
		{!! Form::hidden('filter[type]', 'bonets') !!}
		{!! Form::hidden('csv', true) !!}
		<button type="submit" class="vspace-below-15 btn btn-primary">Download data as CSV</button>
		{!! Form::close() !!}
	@endif

	<table class="table table-striped table-responsive" infinite-scroll="loadMoreItems()" infinite-scroll-distance="1">
		<thead>
			<tr>
				<th>{!! LR('account.date_header') !!}</th>
				<th>{!! LR('account.description_header') !!}</th>
				<th>{!! LR('account.value_header') !!}</th>
				<th>{!! LR('account.bonets_header') !!}</th>
				<th>{!! LR('account.status_header') !!}</th>
			</tr>
		</thead>
		<tbody>
			<tr data-ng-repeat="item in items" class="animate-enter" data-ng-class="{'success': item.internal_status===2 || item.internal_status===undefined, 'danger': item.internal_status===3}">
				<td>@{{ item.date }}</td>
				<td data-ng-bind-html="item.description.substr(0,50)"></td>
				<td data-ng-bind-html="item.localized_amount"></td>
				<td>@{{ item.bonets }}</td>
				<td>@{{ item.status }}</td>
			</tr>
		</tbody>
	</table>
	<div class="text-center alert alert-danger fade-in" data-ng-show="errorFetchingItems">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
		<span>{!! LR('account.error_fetching_items') !!}</span>
	</div>
	<div class="text-center fade-in" data-ng-show="items.length === 0 && !errorFetchingItems && !pendingRequest" >
		<h3>{!! LR('account.no_entries') !!}</h3>
	</div>
	<div class="text-center margin-vertical-15" data-ng-if="pendingRequest">
		<i class="fa fa-spinner fa-spin fa-3x"></i>
	</div>

</script>

<script type="text/ng-template" id="vouchers.html">

	<table class="table table-responsive">
		<thead>
			<tr class="success">
				<th>{!! LR('account.bonets_available') !!}</th>
				<th>@{{overview.available}}</th>
			</tr>
			<tr>
				<td>{!! LR('account.bonets_pending') !!}</th>
				<td>@{{overview.pending}}</th>
			</tr>
			<tr>
				<td>{!! LR('account.bonets_total') !!}</th>
				<td>@{{overview.total}}</th>
			</tr>
		</thead>
	</table>

	@if ($editingOtherUser)
		{!! Form::open(['url' => action('AccountController@fetchHistoryAdmin', ['user_id' => $user->id]) ]) !!}
		{!! Form::hidden('filter[type]', 'vouchers') !!}
		{!! Form::hidden('csv', true) !!}
		<button type="submit" class="vspace-below-15 btn btn-primary">Download data as CSV</button>
		{!! Form::close() !!}
	@endif

	<table class="table table-striped table-responsive" infinite-scroll="loadMoreItems()" infinite-scroll-distance="1">
		<thead>
			<tr>
				<th>{!! LR('account.date_header') !!}</th>
				<th>{!! LR('account.shop_header') !!}</th>
				<th>{!! LR('account.value_header') !!}</th>
				<th>{!! LR('account.bonets_header') !!}</th>
				<th>{!! LR('account.voucher_header') !!}</th>
			</tr>
		</thead>
		<tbody>
			<tr data-ng-repeat="item in items" class="animate-enter">
				<td>@{{ item.date }}</td>
				<td data-ng-bind-html="item.description"></td>
				<td data-ng-bind-html="item.localized_amount"></td>
				<td>@{{item.bonets}}</td>
				<td>@{{ item.voucher_code }}</td>
			</tr>
		</tbody>
	</table>
	<div class="text-center alert alert-danger fade-in" data-ng-show="errorFetchingItems">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
		<span>{!! LR('account.error_fetching_items') !!}</span>
	</div>
	<div class="text-center fade-in" data-ng-show="items.length === 0 && !errorFetchingItems && !pendingRequest">
		<h3>{!! LR('account.no_entries') !!}</h3>
	</div>
	<div class="text-center margin-vertical-15" data-ng-if="pendingRequest">
		<i class="fa fa-spinner fa-spin fa-3x"></i>
	</div>

</script>

<script type="text/ng-template" id="donations.html">

	<table class="table table-responsive">
		<thead>
			<tr class="success">
				<th>{!! LR('account.bonets_available') !!}</th>
				<th>@{{overview.available}}</th>
			</tr>
			<tr>
				<td>{!! LR('account.bonets_pending') !!}</th>
				<td>@{{overview.pending}}</th>
			</tr>
			<tr>
				<td>{!! LR('account.bonets_total') !!}</th>
				<td>@{{overview.total}}</th>
			</tr>
		</thead>
	</table>

	@if ($editingOtherUser)
		{!! Form::open(['url' => action('AccountController@fetchHistoryAdmin', ['user_id' => $user->id]) ]) !!}
		{!! Form::hidden('filter[type]', 'donations') !!}
		{!! Form::hidden('csv', true) !!}
		<button type="submit" class="vspace-below-15 btn btn-primary">Download data as CSV</button>
		{!! Form::close() !!}
	@endif

	<table class="table table-striped table-responsive" infinite-scroll="loadMoreItems()" infinite-scroll-distance="1">
		<thead>
			<tr>
				<th>{!! LR('account.date_header') !!}</th>
				<th>{!! LR('account.receiver_header') !!}</th>
				<th>{!! LR('account.value_header') !!}</th>
				<th>{!! LR('account.bonets_header') !!}</th>
			</tr>
		</thead>
		<tbody>
			<tr data-ng-repeat="item in items" class="animate-enter">
				<td>@{{ item.date }}</td>
				<td data-ng-bind-html="item.receiver"></td>
				<td data-ng-bind-html="item.localized_amount"></td>
				<td>@{{ item.bonets }}</td>
			</tr>
		</tbody>
	</table>
	<div class="text-center alert alert-danger fade-in" data-ng-show="errorFetchingItems">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
		<span>{!! LR('account.error_fetching_items') !!}</span>
	</div>
	<div class="text-center fade-in" data-ng-show="items.length === 0 && !errorFetchingItems && !pendingRequest">
		<h3>{!! LR('account.no_entries') !!}</h3>
	</div>
	<div class="text-center margin-vertical-15" data-ng-if="pendingRequest">
		<i class="fa fa-spinner fa-spin fa-3x"></i>
	</div>

</script>
