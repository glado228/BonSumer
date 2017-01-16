@extends('layouts.master')
{{--
	$user_id : the user we are showing transactions for, or NULL for all users

--}}

@section('content')

<div class="container-fluid">

	<div class="row">
		<div class="col-sm-3">
		{!! Form::open(['url' => action('Admin\AffiliateController@loadTransactions', ['user_id' => $user_id]) ]) !!}
			{!! Form::hidden('csv', true) !!}
			<button type="submit" class="vspace-below-15 btn btn-primary">Download all transactions as CSV</button>
		{!! Form::close() !!}
		</div>

		<div class="col-sm-6">
			<p>Double click on any transactions to see details for that user</p>
		</div>
	</div>

	<div style="height: 520px;">
		<div ag-grid="gridOptions" class="ag-fresh" style="height: 100%;" data-ng-controller="AffiliateController"></div>
	</div>
</div>


@endsection
