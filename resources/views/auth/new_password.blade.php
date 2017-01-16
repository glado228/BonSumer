@extends('layouts.master')


@section('content')
<div class="container-fluid">
	<div class="row vspace-above-125">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="panel {{ $errors->any() ? 'panel-danger' : 'panel-info' }} semitransparent-white-background">
				<div class="panel-heading text-center">
					<div class="panel-title">
					@if ($errors->any())
						{!! LR('auth.new_password_error') !!}
					@else
						{!! LR('auth.new_password_header') !!}
					@endif
					</div>
				</div>

				<div class="panel-body">

				{!! Form::open(array('url' => URL::action('Auth\AuthController@postNewPassword'), 'role' => 'form')) !!}

					{!! Form::hidden('reset_token', $reset_token) !!}
					{!! Form::hidden('email', $email) !!}

					<label class="control-label" for="email_confirmation">{!! LR('auth.new_password_instructions', ['email' => $email]) !!}</label>
					<div class="form-group {{ $errors->has('password') || $errors->has('failed_login') ? 'has-error' : ''}}">
						{!! Form::password('password', array('class' => 'form-control', 'placeholder' => trans('auth.password_placeholder'))) !!}
						{!! $errors->first('password', '<label class="control-label" for="password">:message</label>') !!}
					</div>

					<label class="control-label" for="email_confirmation">{!! LR('auth.password_confirmation_prompt') !!}</label>
					<div class="form-group {{ $errors->has('password') || $errors->has('failed_login') ? 'has-error' : ''}}">
						{!! Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => trans('auth.password_placeholder'))) !!}
						{!! $errors->first('password', '<label class="control-label" for="password">:message</label>') !!}
					</div>

					<div class="form-group vspace-above-25">
						<div class="col-xs-12 col-sm-6 col-sm-offset-3">
							<button type="submit" class="btn btn-primary btn-block">{!! LR('auth.password_change') !!}</button>
						</div>
					</div>

				{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

