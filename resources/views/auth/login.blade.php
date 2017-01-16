@extends('layouts.master')


@section('content')

@include('partials.toolbar')

<div class="container-fluid">
	<div class="row vspace-above-55">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="panel {{ $errors->any() ? 'panel-danger' : 'panel-info' }} semitransparent-white-background">
				<div class="panel-heading text-center">
					<div class="panel-title">
					@if ($errors->any())
						{!! LR('auth.login_failed') !!}
					@elseif (Session::has('url.intended'))
						{!! LR('auth.login_header_redirect') !!}
					@elseif (isset($login_header))
						{!! $login_header !!}
					@else
						{!! LR('auth.login_header') !!}
					@endif
					</div>
				</div>

				<div class="panel-body">
				{!! Form::open(array('url' => URL::action('Auth\AuthController@postLogin'), 'role' => 'form')) !!}

					<div class="form-group {{ $errors->has('email') || $errors->has('failed_login') ? 'has-error' : ''}}">
						{!! Form::text('email', (isset($email) ? $email : NULL), array('class' => 'form-control', 'placeholder' => trans('auth.email_placeholder'))) !!}
						{!! $errors->first('email', '<label class="control-label" for="email">:message</label>') !!}
					</div>

					<div class="form-group {{ $errors->has('password') || $errors->has('failed_login') ? 'has-error' : ''}}">
						{!! Form::password('password', array('class' => 'form-control', 'placeholder' => trans('auth.password_placeholder'))) !!}
						{!! $errors->first('password', '<label class="control-label" for="password">:message</label>') !!}
					</div>

					<div class="form-group">
						<div class="checkbox">
						    <label>
						      {!! Form::checkbox('remember', 1, NULL, []) !!} {!! LR('auth.remember_me') !!}
						    </label>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-sm-6 text-sm-left text-xs-center">
							<a href="{{ action('Auth\AuthController@getPasswordReset') }}">{!! LR('auth.password_forgotten') !!}</a>
						</div>
						<div class="col-sm-6 text-sm-right text-xs-center">
							<a href="{{ action('Auth\AuthController@getSignup') }}">{!! LR('auth.sign_up') !!}</a>
						</div>
					</div>

					<div class="form-group vspace-above-25 row">
						<div class="col-xs-12 col-sm-6 col-sm-offset-3">
							<button type="submit" class="btn btn-primary btn-block">{!! LR('auth.login_button') !!}</button>
						</div>
					</div>

					<div class="form-group vspace-above-15">
						<div class="col-xs-6 col-xs-offset-3 text-center">
							<a href="{{action('Auth\FIWareController@getLogin')}}">
								{!! LR('auth.fiware_login') !!}
								{!! IMG('general.fiwarelab_icon', FALSE, ['class' => 'img-responsive']) !!}
							</a>
						</div>
					</div>

				{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

