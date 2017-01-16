@extends('layouts.master')


@section('content')

<div class="container-fluid" data-ng-controller="SyncController">

	<div class="row">
		<div class="col-sm-6">
			<h3>Media resources</h3>
		</div>
		<div class="col-sm-6">
			<h3>Language resoruces (coming soon)</h3>
		</div>
	</div>

	<div class="row">

		<div class="col-sm-6">

			<div class="row">
				<p>
					To sync the media resources from staging to production, click on "Sync Media"
				</p>
				<p data-ng-cloak data-ng-show="lockedby && lockedby != my_email" class="text-danger">
					Warning: user @{{lockedby}} appears to be syncing media resources right now. Syncing media resources now may
					cause conflicts
				</p>
			</div>

			<div class="row">
				<div class="col-sm-3">
					<button class="vspace-above-25 btn btn-primary" data-ng-disabled="media_pending" data-ng-click="syncMedia()">Sync Media</button>
				</div>

				<div class="col-sm-3" class="text-left">
					<i data-ng-cloak data-ng-show="media_pending" class="vspace-above-25  fa fa-spinner fa-spin fa-2x"></i>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<label data-ng-cloak data-ng-show="media_pending" class="vspace-above-25 text-info">Syncing...</label>
					<label data-ng-cloak data-ng-show="media_success && !media_pending" class="vspace-above-25 text-success">Media successfully synced</label>
					<label data-ng-cloak data-ng-show="media_error && !media_pending" class="vspace-above-25 text-danger">Error while syncing. See output.</label>

					<textarea readonly id="sync-media-output-area" class="form-control vspace-above-25" rows="10" data-ng-model="syncOutput"></textarea>

				</div>

			</div>

		</div>
	</div>


</div>


@endsection
