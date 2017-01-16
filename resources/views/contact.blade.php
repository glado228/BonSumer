@extends('layouts.master')

@section('content')

@include('partials.toolbar')

  <section class="main-section">
    <div class="container">
      <div class="row disable-xs">

        <section class="padded-section">
          <div class="lined-header">
          <h1 class="section-title">{!! LR('contact.contact') !!}</h1>
          </div>
        </section>

        <p>{!! LR('contact.suggestions') !!}</p>
        <p>&nbsp;</p>
        <h3><u>{!! LR('contact.email') !!}</u></h3>
        <p>{!! IMG('general.email_icon', FALSE, ['class' => 'pull-left', 'width' => 20, 'height' => 20]) !!}</p>
        <p><a href="mailto:info@bonsum.de">info@bonsum.de</a></p>
        <h3><u>{!! LR('contact.phone') !!}</u></h3>
        <div>
        <p>{!! IMG('general.phone_icon', FALSE, ['class' => 'pull-left', 'width' => 20, 'height' => 20]) !!}</p>
        <p>+49 (0)&nbsp;30 64312659</p>
        </div>
        <h3><u>{!! LR('contact.fax') !!}</u></h3>
        <p>{!! IMG('general.fax_icon', FALSE, ['class' => 'pull-left', 'width' => 20, 'height' => 20]) !!}</p>
        <p>+49 (0)&nbsp;355 28925 89 2454</p>
        <p>&nbsp;</p>
        <h3><u>{!! LR('contact.mailing_address') !!}</u></h3>
        <p>{!! LR('contact.address_info') !!}</p>
        <p>&nbsp;</p>
        <p>
          <a href="https://de-de.facebook.com/Bonsum.de">
            {!! IMG('general.social.facebook', FALSE, ['class' => '', 'width' => 52]) !!}
          </a>
          <a href="https://twitter.com/Bonsum">
            {!! IMG('general.social.twitter', FALSE, ['class' => '', 'width' => 52]) !!}
          </a>
          <a href="http://www.pinterest.com/ibonsum/">
            {!! IMG('general.social.pinterest', FALSE, ['class' => '', 'width' => 52]) !!}
          </a>
          <a href="https://plus.google.com/+BonsumBerlin">
            {!! IMG('general.social.googleplus', FALSE, ['class' => '', 'width' => 52]) !!}
          </a>
        </p>
      </div>
    </div> <!-- main-section container -->
  </section>

@endsection
