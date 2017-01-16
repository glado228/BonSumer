@extends('layouts.master')

@section('content')

<div class="container-fluid">
	<div class="row">

		<div class="col-sm-2">
		{!! Form::open(['url' => action('Admin\UserController@loadUsers') ]) !!}
			{!! Form::hidden('csv', true) !!}
			<button type="submit" class="vspace-below-15 btn btn-primary">Download all users as CSV</button>
		{!! Form::close() !!}
		</div>
		<div class="col-sm-6">
			<p>Double click on any user to see more details</p>
		</div>
	</div>

	<div style="height: 520px;">
		<div ag-grid="gridOptions" class="ag-fresh" style="height: 100%;" data-ng-controller="UserListController"></div>
	</div>
</div>

@endsection
