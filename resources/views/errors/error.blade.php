@extends('layouts.master')

<div class="container">
	<div class="row vspace-above-55">
		<div class="col-sm-6 col-sm-offset-3 text-center">
			{!! IMG('general.logo', FALSE, ['class' => 'img-responsive']) !!}
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 text-danger text-center">
			<h1>Oops! This is an Error</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 text-danger text-center">
			<h2>{{{ $statusCode }}}: {{{ $statusText }}}</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 text-danger text-center">
			<h3>{{{ $errorMsg }}}</h3>
		</div>
	</div>
</div>
