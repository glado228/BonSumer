@extends('layouts.master')


@section('content')

@include('partials.toolbar')

<div class="container-fluid">
	<div class="row vspace-above-125">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="panel {{ $errors->any() ? 'panel-danger' : 'panel-info' }} semitransparent-white-background">
				<div class="panel-heading text-center">
					<div class="panel-title">
					@if ($errors->any())
						{!! LR('auth.password_reset_error') !!}
					@else
						{!! LR('auth.password_reset_header') !!}
					@endif
					</div>
				</div>

				<div class="panel-body">
				{!! Form::open(array('url' => URL::action('Auth\AuthController@postPasswordReset'), 'role' => 'form', 'class' => 'form-horizontal')) !!}

					<div class="form-group {{ $errors->has('email') || $errors->has('failed_login') ? 'has-error' : ''}}">
						<div class="col-sm-12">
						{!! Form::text('email', (isset($email) ? $email : NULL), array('class' => 'form-control', 'placeholder' => trans('auth.email_placeholder'))) !!}
						</div>
						<div class="col-sm-12">
						{!! $errors->first('email', '<label class="control-label" for="email">:message</label>') !!}
						</div>
					</div>

					<div class="form-group vspace-above-25">
						<div class="col-xs-12 col-sm-6 col-sm-offset-3">
							<button type="submit" class="btn btn-primary btn-block">{!! LR('auth.reset_password') !!}</button>
						</div>
					</div>

				{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

