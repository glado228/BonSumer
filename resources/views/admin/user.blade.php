@extends('layouts.master')

{{--
Variable:

$user = the user being edited (required)

--}}

@section('content')

<div class="container-fluid" data-ng-controller="UserFormController">

  <a href="{{ action('Admin\UserController@showUsers') }}">&#8592; Back to the user list</a>

{{-- !! Form::open(['novalidate' => '', 'name' => 'form', 'data-ng-controller' => 'UserFormController']) !! --}}
	<fieldset data-ng-disabled="isSaving">

		 <div class="form-group row">
		    <div class="col-sm-9">
		       <h3>{{ 'User ID: '. $user->id . ' Email:' . $user->email . ' (' . $user->full_name . ')' }}</h3>
		       @if ($user->id === Auth::user()->id)
		       <h4 class="text-danger">This is you!!!</h4>
		       @endif
		    </div>
		    <div class="col-sm-3 text-sm-right">
			    <button type="button" data-ng-disabled="currentUser.id === user.id" data-ng-click="delete()" class="btn btn-default" title="Delete user">
			      <span class="glyphicon glyphicon-trash"></span>
			    </button>
		    </div>
		 </div>

	    <fieldset data-ng-disabled="currentUser.id === user.id">

		 <div class="form-group row">

		 	<div class="col-sm-4">
		 		<div data-ng-if="!user.confirmed" data-ng-cloak>
			 		<h4 class="text-danger">
			 			This user has yet to confirm their account
			 		</h4>
				    <button type="button" data-ng-click="sendConfirmationReminder()" class="btn btn-primary">
				    	Send a confirmation reminder
				    </button>
			    </div>
		 		<div data-ng-if="user.confirmed" data-ng-cloak>
					<h4 data-ng-bind="(user.disabled ? 'This user has been disabled' : 'This user is active')"
					data-ng-class="{'text-danger': user.disabled, 'text-success': !user.disabled}"
					>
					</h4>
			 	 	<button data-ng-bind="(user.disabled ? 'Reactivate user' : 'Disable user')"
			 	 	type="button" class="btn"
			 	 	data-ng-class="{'btn-danger': !user.disabled, 'btn-warning': user.disabled}"
			 	 	data-ng-click="toggleDisabled()"
			 	 	>
			 	 	</button>
			 	 </div>
			</div>

		 </div>

		 </fieldset>

		 <div class="form-group row">
		 	 <div class="col-sm-4">
			    <label>User name:</label>&nbsp;
			    <p>{{$user->full_name}}</p>
			    <div><a href="{{ action('AccountController@indexAdmin', ['user_id' => $user->id]) }}">Go to user's profile page</a></div>
			    <div><a href="{{ action('Admin\AffiliateController@showTransactions', ['user_id' => $user->id]) }}">See user's affiliate transactions</a></div>
			    <div><a href="{{ action('Admin\DonationController@index', ['user_id' => $user->id]) }}">See user's donations</a></div>

			 </div>
		 </div>

		 <div class="form-group row">
		 	 <div class="col-sm-3">
			    <label>Bonets:</label>
			    {!!
    				Form::text('bonets',
	              null,
	              array(
	              'data-ng-model' => 'user.bonets',
	              'disabled' => true,
	              'class' => 'form-control'))
			  !!}
			 </div>

		 </div>

		 <div class="form-group row">

 		 	 <div class="col-sm-3" data-ng-class="{'has-error': errors.bonets}">
			    <label class="control-label">Credit the user with new bonets:</label>
			    {!!
    			Form::text('bonets_credit',
	              null,
	              array(
	              'data-ng-change' => 'errors.bonets = null',
	              'data-ng-model' => 'bonets_credit',
	              'class' => 'form-control'))
			  !!}
			  	<label class="control-label" data-ng-show="errors.bonets" data-ng-bind="errors.bonets"></label>
			 </div>

			 <div class="col-sm-2">
			 	<label class="invisible">X</label>
 			  	<button type="button" class="btn btn-primary form-control" data-ng-click="creditBonets()">Credit bonets</button>
			 </div>

		</div>

		 <div class="form-group row">
		 	<div class="col-sm-6">
		 		<label class="control-label">Enter a personalized message that will be shown to the user:</label>
			    {!!
    			Form::textarea('bonets_credit_message',
				  null,
	              array(
	              'placeholder' => trans('account.bonets_credit'),
	              'data-ng-model' => 'bonets_credit_message',
	              'class' => 'form-control'))
			  !!}
		 	</div>

		 </div>

		 <div class="form-group row">

		 	<div class="col-sm-4">
				<h4 data-ng-show="user.admin" class="text-danger">
					This user is an admin
				</h4>
				<h4 data-ng-show="!user.admin" class="text-info">
					This user is not an admin
				</h4>
		 	 	<button data-ng-bind="(user.admin ? 'Revoke admin rights' : 'Grant admin rights')"
		 	 	type="button" class="btn" data-ng-disabled="currentUser.id === user.id"
		 	 	data-ng-class="{'btn-danger': !user.admin, 'btn-warning': user.admin}"
		 	 	data-ng-click="toggleAdmin()"
		 	 	>
		 	 	</button>
			</div>

			<div class="col-sm-4">
				<h4>
					Here you can reset the user password
				</h4>
		 	 	<button
		 	 	type="button" class="btn btn-primary"
		 	 	data-ng-disabled="currentUser.id === user.id || !user.confirmed"
		 	 	data-ng-click="resetPassword()">
		 	 	Reset Password
		 	 	</button>
				@if ($user->reset_token)
		 	 	<p class="help-block">
		 	 		The password was reset on {{$user->reset_token_creation->toRfc850String()}}. You can only send one reset email per hour.
		        </p>
		 	 	@endif
			</div>
		 </div>

	</fieldset>


</div>

@endsection
