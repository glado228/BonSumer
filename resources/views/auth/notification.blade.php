@extends('layouts.master')
{{--
	General notifications for login/signup pages.

	$notification_title Title of notification
	$notification_text  Main text of notification
--}}

@section('content')

@include('partials.toolbar')

<div class="container-fluid">
	<div class="row vspace-above-55">
		<div class="col-sm-8 col-sm-offset-2">
			<div class="panel {{ $errors->any() ? 'panel-danger' : 'panel-info' }} semitransparent-white-background">
				<div class="panel-heading text-center">
					<div class="panel-title">
						<strong>{!! $notification_title !!}</strong>
					</div>
				</div>
				<div class="panel-body">
					<p>{!! $notification_text !!}</p>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
