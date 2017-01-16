@extends('layouts.master')
{{--
	'referer_id' => id of user who referred (optional)
	'referer_name' => name of user who referred (optional)
--}}

@section('content')

@include('partials.toolbar')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<div class="panel {{ $errors->any() ? 'panel-danger' : 'panel-info' }} semitransparent-white-background"
				data-ng-controller="SignUpFormController" data-ng-class="{'panel-danger': submitFailed()}"
				@if (isset($referer_id))
				data-ng-init="formdata.referer_id={{$referer_id}}"
				@endif
				>
				<div class="panel-heading text-center">
					<div class="panel-title">
						<div data-ng-cloak data-ng-show="submitFailed()">
							{!! LR('auth.signup_failed') !!}
						</div>
						<div data-ng-cloak data-ng-show="!submitFailed()">
							@if (isset($referer_id))
								{!! LR('auth.signup_header_referred', ['referer' => $referer_name]) !!}
							@else
								{!! LR('auth.signup_header') !!}
							@endif
						</div>
					</div>
				</div>
				<div class="panel-body">
			    <p class="help-block">{!! LR('auth.signup_instructions') !!}</p>

				{!! Form::open(array('novalidate' => '','name' => 'form', 'url' => URL::action('Auth\AuthController@postSignup'), 'role' => 'form')) !!}
				<fieldset data-ng-disabled="isSaving">
				<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}" data-ng-class="{'has-error': showErrorFor('email')}">
					<label class="control-label" for="email">{!! LR('auth.email_prompt') !!}</label>
					{!! Form::text('email', null, array('ng-change' => 'changed()', 'ng-model-options' => "{ debounce: 500 }", 'data-ng-model' => 'formdata.email', 'class' => 'form-control', 'placeholder' => trans('auth.email_placeholder'))) !!}
					{!! $errors->first('email', '<label class="control-label" for="email">:message</label>') !!}
					<label class="control-label" data-ng-show="showErrorFor('email')" data-ng-bind="errors.email[0]" for="emal"></label>
				</div>
				<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}" data-ng-class="{'has-error': showErrorFor('email')}">
					<label class="control-label" for="email_confirmation">{!! LR('auth.email_confirmation_prompt') !!}</label>
					{!! Form::text('email_confirmation', null, array('ng-change' => 'changed()', 'ng-model-options' => "{ debounce: 500 }", 'data-ng-model' => 'formdata.email_confirmation', 'class' => 'form-control', 'placeholder' => trans('auth.email_placeholder'))) !!}
				</div>

				<div class="form-group {{ $errors->has('firstname') || $errors->has('lastname') ? 'has-error' : ''}}" data-ng-class="{'has-error': showErrorFor('firstname', 'lastname')}">
					<label class="control-label" for="frstname">{!! LR('auth.firstlastname_prompt') !!}</label>
					<div class="row">
						<div class="col-sm-6">
						{!! Form::text('firstname', null, array('ng-change' => 'changed()', 'ng-model-options' => "{ debounce: 500 }", 'data-ng-model' => 'formdata.firstname', 'class' => 'form-control', 'placeholder' => trans('auth.firstname_placeholder'))) !!}
						</div>
						<div class="col-sm-6 vspace-xs-above-5">
						{!! Form::text('lastname', null, array('ng-change' => 'changed()', 'ng-model-options' => "{ debounce: 500 }", 'data-ng-model' => 'formdata.lastname', 'class' => 'form-control', 'placeholder' => trans('auth.lastname_placeholder'))) !!}
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
						{!! $errors->first('firstname', '<label class="control-label" for="email">:message</label>') !!}
						<label class="control-label" data-ng-show="showErrorFor('firstname')" data-ng-bind="errors.firstname[0]" for="firstname"></label>
						</div>
						<div class="col-sm-6">
						{!! $errors->first('lastname', '<label class="control-label" for="email">:message</label>') !!}
						<label class="control-label" data-ng-show="showErrorFor('lastname')" data-ng-bind="errors.lastname[0]" for="lastname"></label>
						</div>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-sm-4">
						<label class="control-label" for="gender">{!! LR('auth.gender_prompt') !!}</label>
					</div>
					<div class="col-sm-2">
						{!! Form::select('gender', ['F' => trans('auth.female'), 'M' => trans('auth.male'), NULL => trans('auth.none')], NULL, ['class' => 'form-control', 'data-ng-model' => 'formdata.gender']) !!}
					</div>
				</div>

				<div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}" data-ng-class="{'has-error': showErrorFor('password')}">
					<label class="control-label" for="password">{!! LR('auth.password_prompt') !!}</label>
					{!! Form::password('password', array('ng-change' => 'changed()', 'ng-model-options' => "{ debounce: 500 }", 'data-ng-model' => 'formdata.password', 'class' => 'form-control', 'placeholder' => trans('auth.password_placeholder'))) !!}
					{!! $errors->first('password', '<label class="control-label" for="email">:message</label>') !!}
					<label class="control-label" data-ng-show="showErrorFor('password')" data-ng-bind="errors.password[0]" for="password"></label>
				    <p class="help-block">{!! LR('auth.password_constraints') !!}</p>
				</div>
				<div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}" data-ng-class="{'has-error': showErrorFor('password')}">
					<label class="control-label" for="password_confirmation">{!! LR('auth.password_confirmation_prompt') !!}</label>
					{!! Form::password('password_confirmation', array('ng-change' => 'changed()', 'ng-model-options' => "{ debounce: 500 }", 'data-ng-model' => 'formdata.password_confirmation', 'class' => 'form-control', 'placeholder' => trans('auth.password_placeholder'))) !!}
				</div>

				<div class="form-group {{ $errors->has('terms_and_conditions') ? 'has-error' : ''}}" data-ng-class="{'has-error': showErrorFor('terms_and_conditions')}">
					<div class="checkbox">
					    <label>
					      {!! Form::checkbox('terms_and_conditions', 1, NULL, ['data-ng-model' => 'formdata.terms_and_conditions', 'ng-change' => 'changed()', 'ng-model-options' => "{ debounce: 500 }"]) !!} {!! LR('auth.terms_and_conditions', ['link' => action('StaticController@terms') ]) !!}
					    </label>
					</div>
					{!! $errors->first('terms_and_conditions', '<label class="control-label" for="terms_and_conditions">:message</label>') !!}
					<label class="control-label" data-ng-show="showErrorFor('terms_and_conditions')" data-ng-bind="errors.terms_and_conditions[0]" for="terms_and_conditions"></label>
				</div>

				<div class="form-group vspace-above-25">
					<div class="col-sm-6 col-sm-offset-3">
						<button type="submit" data-ng-click="submit($event)" class="btn btn-primary btn-block">{!! LR('auth.signup_button') !!}</button>
					</div>
				</div>

				</fieldset>

				{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
