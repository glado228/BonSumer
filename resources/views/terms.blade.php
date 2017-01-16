@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">

        <section class="padded-section">
          <div class="lined-header">
            <h1 class="section-title">{!! LR('terms.header') !!}</h1>
          </div>
        </section>
          <h2>{!! LR('terms.term_titles.t1') !!}</h2>
          <p>{!! LR('terms.term_bodies.p1') !!}</p>
          <h2>{!! LR('terms.term_titles.t2') !!}</h2>
          <p>{!! LR('terms.term_bodies.p2_1') !!}</p>
          <p>{!! LR('terms.term_bodies.p2_2') !!}</p>
          <p>{!! LR('terms.term_bodies.p2_3') !!}</p>
          <p>{!! LR('terms.term_bodies.p2_4') !!}</p>
          <p>{!! LR('terms.term_bodies.p2_5') !!}</p>
          <p>{!! LR('terms.term_bodies.p2_6') !!}</p>
          <h2>{!! LR('terms.term_titles.t3') !!}</h2>
          <p>{!! LR('terms.term_bodies.p3') !!}</p>
          <h2>{!! LR('terms.term_titles.t4') !!}</h2>
          <p>{!! LR('terms.term_bodies.p4_1') !!}</p>
          <p>{!! LR('terms.term_bodies.p4_2') !!}</p>
          <p>{!! LR('terms.term_bodies.p4_3') !!}</p>
          <p>{!! LR('terms.term_bodies.p4_4') !!}</p>
          <p>{!! LR('terms.term_bodies.p4_5') !!}</p>
          <p>{!! LR('terms.term_bodies.p4_6') !!}</p>
          <h2>{!! LR('terms.term_titles.t5') !!}</h2>
          <p>{!! LR('terms.term_bodies.p5') !!}</p>
          <h2>{!! LR('terms.term_titles.t6') !!}</h2>
          <p>{!! LR('terms.term_bodies.p6_1') !!}</p>
          <p>{!! LR('terms.term_bodies.p6_2') !!}</p>
          <p>{!! LR('terms.term_bodies.p6_3') !!}</p>
          <h2>{!! LR('terms.term_titles.t7') !!}</h2>
          <p>{!! LR('terms.term_bodies.p7_1') !!}</p>
          <p>{!! LR('terms.term_bodies.p7_2') !!}</p>
          <p>{!! LR('terms.term_bodies.p7_3') !!}</p>
          <p>{!! LR('terms.term_bodies.p7_4') !!}</p>
          <p>{!! LR('terms.term_bodies.p7_5') !!}</p>
          <h2>{!! LR('terms.term_titles.t8') !!}</h2>
          <p>{!! LR('terms.term_bodies.p8_1') !!}</p>
          <p>{!! LR('terms.term_bodies.p8_2') !!}</p>
          <p>{!! LR('terms.term_bodies.p8_3') !!}</p>
          <h2>{!! LR('terms.term_titles.t9') !!}</h2>
          <p>{!! LR('terms.term_bodies.p9') !!}</p>
          <h2>{!! LR('terms.term_titles.t10') !!}</h2>
          <p>{!! LR('terms.term_bodies.p10_1') !!}</p>
          <p>{!! LR('terms.term_bodies.p10_2') !!}</p>
      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
