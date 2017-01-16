@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">
        {!! LR('imprint.tagline') !!}
        <p></p>
        <p>{!! LR('imprint.courtesy_of') !!}</p>
        <h1>{!! LR('imprint.website_contact') !!}</h1>
        <p>{!! LR('imprint.website_contact_info.p0') !!}</p>
        <p>{!! LR('imprint.website_contact_info.p1') !!}</p>
        <p>{!! LR('imprint.website_contact_info.p2') !!}</p>
        <p>{!! LR('imprint.website_contact_info.p3') !!}</p>
        <h2>{!! LR('imprint.disclaimer') !!}</h2>
        <h3>{!! LR('imprint.disclaimer_titles.t0') !!}</h3>
        <p>{!! LR('imprint.disclaimer_sections.s0') !!}</p>
        <h3>{!! LR('imprint.disclaimer_titles.t1') !!}</h3>
        <p>{!! LR('imprint.disclaimer_sections.s1') !!}</p>
        <h3>{!! LR('imprint.disclaimer_titles.t2') !!}</h3>
        <p>{!! LR('imprint.disclaimer_sections.s2') !!}</p>
        <h3>{!! LR('imprint.disclaimer_titles.t3') !!}</h3>
        <p>{!! LR('imprint.disclaimer_sections.s3') !!}</p>
        <h3>{!! LR('imprint.disclaimer_titles.t4') !!}</h3>
        <p>{!! LR('imprint.disclaimer_sections.s4') !!}</p>
        <h3>{!! LR('imprint.disclaimer_titles.t5') !!}</h3>
        <ul>
          <li>{!! LR('imprint.disclaimer_sections.s5.li0') !!}</li>
          <li>{!! LR('imprint.disclaimer_sections.s5.li1') !!}</li>
          <li>{!! LR('imprint.disclaimer_sections.s5.li2') !!}</li>
        </ul>
      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
