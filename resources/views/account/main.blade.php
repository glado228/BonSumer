@extends('layouts.master')

{{--
	$editingOtherUser : true if an admin is editing another user
--}}

@section('content')

@include('partials.toolbar')

<div class="container-fluid">

	@if ($editingOtherUser)
		<h4 class="text-warning"><i>You are editing user {{$user->email}} ({{$user->fullname}})</i></h4>
	@endif

	<div class="row">

		<div class="col-sm-3" data-ng-controller="AccountSideBarController" >
			<div sticky offset="70" media-query="min-width: 768px" confine="true" bottom-line=".site-footer">
				<accordion close-others="false">
					<accordion-group is-open="history_open">
						<accordion-heading>
						{!! LR('account.history') !!}<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': history_open, 'glyphicon-chevron-right': !history_open}"></i>
						</accordion-heading>
						<div>
							<a href="#!/history/bonets"><span data-ng-class="{'invisible': !isActive('/history/bonets')}" class="glyphicon glyphicon-chevron-right"></span> {!! LR('account.collected_bonets') !!}</a>
						</div>
						<div>
							<a href="#!/history/vouchers"><span data-ng-class="{'invisible': !isActive('/history/vouchers')}" class="glyphicon glyphicon-chevron-right"></span> {!! LR('account.vouchers') !!}</a>
						</div>
						<div>
							<a href="#!/history/donations"><span data-ng-class="{'invisible': !isActive('/history/donations')}" class="glyphicon glyphicon-chevron-right"></span> {!! LR('account.donations') !!}</a>
						</div>
						<div>
							<a href="#!/refer_friends"><span data-ng-class="{'invisible': !isActive('/refer_friends')}" class="glyphicon glyphicon-chevron-right"></span> {!! LR('account.refer_friends') !!}</a>
						</div>
					</accordion-group>

					<accordion-group is-open="personal_data_open">
						<accordion-heading>
						{!! LR('account.personal_data') !!}<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': personal_data_open, 'glyphicon-chevron-right': !personal_data_open}"></i>
						</accordion-heading>
						<div>
							<a href="#!/personal"><span data-ng-class="{'invisible': !isActive('/personal')}" class="glyphicon glyphicon-chevron-right"></span> {!! LR('account.edit_your_data') !!}</a>
						</div>
						<div>
							<a href="#!/password"><span  data-ng-class="{'invisible': !isActive('/password')}" class="glyphicon glyphicon-chevron-right"></span> {!! LR('account.change_password') !!}</a>
						</div>
					</accordion-group>

				</accordion>

				<div class="text-center vspace-below-15">
					<button class="btn btn-default" data-ng-click="logout()">{!! LR('navbar.logout') !!}</button>
				</div>

			</div>
		</div>


		<div class="col-sm-9">

			<div data-ng-view class="account-view-animate">
			</div>

		</div>

	</div>

</div>

<script type="text/ng-template" id="refer_friends.html">

	<h2>{!! LR('account.refer_friends_header') !!}</h2>
	{!! Form::open(['novalidate' => '', 'name' => 'form', 'data-ng-controller' => 'ReferFriendsController']) !!}
        <div class="form-group row" data-ng-class="{'has-error': showErrorFor('email')}">
            <div class="col-sm-6">
                <label class="control-label" for="friendemail">{!! LR('account.refer_friends_email_prompt') !!}</label>
                {!! Form::text('friendemail', null, array( 'ng-model-options' => "{ debounce: 500 }", 'data-ng-change' => 'changed()', 'data-ng-model' => 'formdata.email', 'class' => 'form-control')) !!}
                <label class="control-label" data-ng-show="showErrorFor('email')" data-ng-bind="errors.email" for="email"></label>
            </div>
        </div>

        <div class="form-group row" data-ng-class="{'has-error': showErrorFor('message')}">
            <div class="col-sm-6">
                <label class="control-label" for="message">{!! LR('account.refer_friends_msg_prompt') !!}</label>
                {!! Form::textarea('message', null, array('ng-model-options' => "{ debounce: 500 }", 'data-ng-change' => 'changed()', 'data-ng-model' => 'formdata.message', 'class' => 'form-control', )) !!}
                <label class="control-label" data-ng-show="showErrorFor('message')" data-ng-bind="errors.message" for="message"></label>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-6 text-center">
                <button type="submit" data-ng-click="submit($event)" class="btn btn-primary">{!! LR('account.send_invitation') !!}</button>
            </div>
        </div>
    {!! Form::close() !!}


</script>



<script type="text/ng-template" id="personal_data.html">


	{!! Form::open(['novalidate' => '', 'name' => 'form', 'data-ng-controller' => 'PersonalDataController']) !!}

		<fieldset data-ng-disabled="isSaving">

		<div class="form-group row" data-ng-class="{'has-error': showErrorFor('firstname')}">

			<div class="col-sm-6">
				<label class="control-label" for="frstname">{!! LR('auth.firstname_prompt') !!}</label>
				{!! Form::text('firstname', null, array( 'ng-model-options' => "{ debounce: 500 }", 'data-ng-change' => 'changed()', 'data-ng-model' => 'formdata.firstname', 'class' => 'form-control', 'placeholder' => trans('auth.firstname_placeholder'))) !!}
				<label class="control-label" data-ng-show="showErrorFor('firstname')" data-ng-bind="errors.firstname" for="firstname"></label>

			</div>
		</div>

		<div class="form-group row" data-ng-class="{'has-error': showErrorFor('lastname')}">
			<div class="col-sm-6">
				<label class="control-label" for="frstname">{!! LR('auth.lastname_prompt') !!}</label>
				{!! Form::text('lastname', null, array('ng-model-options' => "{ debounce: 500 }", 'data-ng-change' => 'changed()', 'data-ng-model' => 'formdata.lastname', 'class' => 'form-control', 'placeholder' => trans('auth.lastname_placeholder'))) !!}
				<label class="control-label" data-ng-show="showErrorFor('firstname')" data-ng-bind="errors.lastname" for="lastname"></label>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-sm-3">
				<label class="control-label" for="gender">{!! LR('auth.gender_prompt') !!}</label>
				{!! Form::select('gender', ['F' => trans('auth.female'), 'M' => trans('auth.male'), NULL => trans('auth.none')], NULL, ['class' => 'form-control', 'data-ng-model' => 'formdata.gender']) !!}
			</div>
		</div>

		<div class="form-group row">
			<div class="col-sm-6 text-center">
				<button type="submit" data-ng-click="submit($event)" class="btn btn-primary">{!! LR('account.personal_data_submit') !!}</button>
			</div>
		</div>

		</fieldset>

	{!! Form::close() !!}

</script>

<script type="text/ng-template" id="change_password.html">


	{!! Form::open(['novalidate' => '', 'name' => 'form', 'data-ng-controller' => 'PersonalDataController']) !!}

		@if ($editingOtherUser)
		<p>
			This function is not available in admin mode when editing another user.
			As an admin, you can reset the password of another user from <a href="{{ action('Admin\UserController@showSingleUser', ['user' => $user->id]) }}">here</a>.
		</p>
		@endif

		@if ($editingOtherUser)
		<fieldset disabled>
		@else
		<fieldset data-ng-disabled="isSaving">
		@endif

		<div class="form-group row" data-ng-class="{'has-error': showErrorFor('current_password')}">

			<div class="col-sm-6">
				<label class="control-label" for="current_password">{!! LR('account.current_password_prompt') !!}</label>
				{!! Form::password('current_password', array( 'ng-model-options' => "{ debounce: 500 }", 'data-ng-model' => 'formdata.current_password', 'class' => 'form-control', 'placeholder' => trans('account.current_password_placeholder'))) !!}
				<label class="control-label" data-ng-show="showErrorFor('current_password')" data-ng-bind="errors.current_password" for="current_password"></label>
			</div>
		</div>

		<div class="form-group row" data-ng-class="{'has-error': showErrorFor('new_password')}">

			<div class="col-sm-6">
				<label class="control-label" for="new_password">{!! LR('account.new_password_prompt') !!}</label>
				{!! Form::password('new_password', array( 'ng-model-options' => "{ debounce: 500 }", 'data-ng-change' => 'changed()', 'data-ng-model' => 'formdata.new_password', 'class' => 'form-control', 'placeholder' => trans('account.new_password_placeholder'))) !!}
				<label class="control-label" data-ng-show="showErrorFor('new_password')" data-ng-bind="errors.new_password[0]" for="new_password"></label>
			</div>
		</div>

		<div class="form-group row" data-ng-class="{'has-error': showErrorFor('new_password')}">

			<div class="col-sm-6">
				<label class="control-label" for="new_password_confirmation">{!! LR('account.new_password_confirmation') !!}</label>
				{!! Form::password('new_password_confirmation', array( 'ng-model-options' => "{ debounce: 500 }", 'data-ng-change' => 'changed()', 'data-ng-model' => 'formdata.new_password_confirmation', 'class' => 'form-control')) !!}
			</div>
		</div>


		<div class="form-group row">
			<div class="col-sm-6 text-center">
				<button type="submit" data-ng-click="submit($event)" class="btn btn-primary">{!! LR('account.password_submit') !!}</button>
			</div>
		</div>

		</fieldset>

	{!! Form::close() !!}

</script>

@include('account.partials.history_angular')

@endsection
