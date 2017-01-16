@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">

        <h2>{!! IMG('join.social_impact_lab', FALSE, ['class' => 'img-responsive pull-left', 'width' => 417, 'height' => '313']) !!}
        <div class="clearfix"></div></h2>
        <h1><strong>{!! LR('join.better_world') !!}</strong></h1>
        <h2 class="text-left">{!! LR('join.ambassadors') !!}</h2>
        <ul>
          <li>{!! LR('join.contribute') !!}</li>
          <li>{!! LR('join.represent') !!}</li>
          <li>{!! LR('join.flexibility') !!}</li>
          <li>{!! LR('join.email_and_teams') !!}</li>
          <li>{!! LR('join.duties') !!}</li>
          <li>{!! LR('join.corporate_values') !!}</li>
          <li>{!! LR('join.volunteer') !!}</li>
          <li>{!! LR('join.tshirt') !!}</li>
        </ul>
        <p>&nbsp;</p>
        <h3>{!! LR('join.contact_us') !!}</h3>

      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
