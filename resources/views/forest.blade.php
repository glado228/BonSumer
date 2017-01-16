@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">
				<div class="afforestation forest-section container-fluid">
					<div class="row">
						<div class="col-lg-4 col-sm-6 col-md-5 col-xs-12">
							<h1>
								{!! LR('forest.reforestation') !!}
							</h1>
							<p>
								<a><b style="font-size:20px">&gt;</b> {!! LR('forest.bonsum_reforestation') !!}
							</p>
						</div>
					</div>
				</div>

				<div class="iplantatree forest-section vspace-above-15 container-fluid">
					<div class="row">
						<div class="col-xs-12 col-sm-6 text-center">
							<div id="ipatWidget"></div>

							<script type="text/javascript" src="https://www.iplantatree.org/js/widget/ipatWidget.js"></script>

							<script type="text/javascript">

									var req = "http://www.iplantatree.org/widget/ipatWidget.html?uid=2568&wt=2&rh=http://www.bonsum.de&lang=de";

									bObj = new JSONscriptRequest(req); bObj.buildScriptTag(); bObj.addScriptTag();

							</script>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="reasons-list">
								<ul>
									<li>{!! LR('forest.vital_for_humanity') !!}</li>
									<li>{!! LR('forest.regulate_evaporation') !!}</li>
									<li>{!! LR('forest.water_cycle') !!}</li>
									<li>{!! LR('forest.filter_pollutants') !!}</li>
									<li>{!! LR('forest.provide_habitat') !!}</li>
									<li>{!! LR('forest.antithesis_of_land_sealing') !!}</li>
									<li>{!! LR('forest.co2_sinks') !!}</li>
									<div>{!! LR('forest.thank_tree_planters') !!}</div>
								</ul>
							</div>
						</div>
					</div>

				</div>

				<div class="forest-section vspace-above-15 container-fluid map-section">
					<div class="row">
						<div class="rainforest col-xs-12 col-sm-6">
							<h2>{!! LR('forest.rainforest_brazil') !!}</h2>
							<p>{!! LR('forest.ninety_percent') !!}</p>

							<p>{!! LR('forest.cerrado') !!}</p>

							<p>{!! LR('forest.billion_trees') !!}</p>

							<p>{!! LR('forest.social_projects') !!}</p>
							<div class="vspace-above-15 forest-section forest-hills"></div>
						</div>
						<iframe class="col-xs-12 col-sm-6 map-container" width='100%' frameBorder='0' src='https://a.tiles.mapbox.com/v4/iovar.fda7d4b0/attribution,zoompan,zoomwheel,geocoder,share.html?access_token=pk.eyJ1IjoiaW92YXIiLCJhIjoiZjA4NDRlNTc2M2Q1MzYzM2U4YjMxODk4N2ZiZmI0YmIifQ.VQi9XdfTvKQ38j6hJiCvfA'></iframe>
					</div>
				</div>
				<div class="vspace-above-25 clearfix"> </div>
      </div>
    </div> <!-- main-section container -->
  </section>
@endsection
