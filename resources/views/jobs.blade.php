@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">
        <h1>{!! LR('jobs.currently_seeking') !!}</h1>
        <ul>
          <li>{!! LR('jobs.positions.pos1') !!}</li>
          <li>{!! LR('jobs.positions.pos2') !!}</li>
        </ul>
        <p>{!! LR('jobs.minimum_wage') !!}</p>
        <div>{!! LR('jobs.other_skills') !!}</div>
        <div class="vspace-above-25">{!! LR('jobs.flexible_work') !!}</div>
        <p class="vspace-above-25">{!! LR('jobs.contact_ceo') !!}</p>
        <section >
          <div class="lined-header">
            <h2 class="section-title">{!! LR('jobs.about_bonsum') !!}</h2>
          </div>
          <p>{!! LR('jobs.about_text') !!}</p>
        </section>
      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
