<footer class="site-footer">
  <div class="container footer-menu">
    <div class="row">
      <div class="col-sm-3">
        <h3>{!! LR('footer.more_info') !!}</h3>
        <nav>
          <ul class="list-unstyled">
            <li><a href="{{action('StaticController@howto')}}">{!! LR('footer.how') !!}</a></li>
            <li><a href="{{action('StaticController@faq')}}">{!! LR('footer.faq') !!}</a></li>
            <li><a href="{{action('StaticController@about')}}">{!! LR('footer.about') !!}</a></li>
            <li><a href="{{action('LexiconController@index')}}">{!! LR('footer.lexicon') !!}</a></li>
            <li><a href="{{action('StaticController@shopOwners')}}">{!! LR('footer.owners') !!}</a></li>
          </ul>
        </nav>
      </div>
      <div class="col-sm-2">
        <h3>{!! LR('footer.contact') !!}</h3>
        <nav>
          <ul class="list-unstyled">
            <li><a href="{{action('StaticController@press')}}">{!! LR('footer.press') !!}</a></li>
            <li><a href="{{action('StaticController@contact')}}">{!! LR('footer.contact') !!}</a></li>
            <li><a href="{{action('StaticController@join')}}">{!! LR('footer.join') !!}</a></li>
            <li><a href="{{action('StaticController@jobs')}}">{!! LR('footer.jobs') !!}</a></li>
          </ul>
        </nav>
      </div>
      <div class="col-sm-3 text-center">
        <h3>{!! LR('footer.awards') !!}</h3>
        <nav>
          <ul class="list-unstyled">
            <li>
              <a href="https://socialimpactstart.eu/startups/bonsum-1344">
                {!! IMG('general.awards.socialimpact', null, ['class' => 'img-responsive ', 'width' => 104]) !!}
              </a>
            </li>
            <li>
              <a href="https://www.strato.de/ueber-uns/">
                {!! IMG('general.awards.co2', null, ['class' => 'img-responsive ', 'width' => 134]) !!}
              </a>
            </li>
            <li>
              <a href="https://www.werkstatt-n.de/">
                {!! IMG('general.awards.werkstatt', null, ['class' => 'img-responsive ', 'width' => 180]) !!}
              </a>
            </li>
          </ul>
        </nav>
      </div>
      <div class="col-sm-4 social">
				@include('partials.newsletter_widget')

{{--
        <h3>{!! LR('footer.suscribe') !!}</h3>
        {!! Form::open(['url' => '#', 'class' => '', 'role' => 'form']) !!}
        <div class="input-group">
          <input type="text" class="form-control" placeholder="{!! trans('footer.newsletter_placeholder') !!}">
        </div>
--}}
        {!! Form::close() !!}
        <h3>{!! LR('footer.follow_us') !!}</h3>
        <ul class="list-unstyled social">
          <li>
            @if ($locale == 'en-UK')
            <a href="https://de-de.facebook.com/BonsumUK">
            @else
            <a href="https://de-de.facebook.com/Bonsum.de">
            @endif
              {!! IMG('general.social.facebook', true, ['class' => 'img-responsive ', 'width' => 52]) !!}
            </a>
          </li>
          <li>
            <a href="https://twitter.com/Bonsum">
              {!! IMG('general.social.twitter', true, ['class' => 'img-responsive ', 'width' => 52]) !!}
            </a>
          </li>
          <li>
            <a href="https://www.pinterest.com/ibonsum/">
              {!! IMG('general.social.pinterest', true, ['class' => 'img-responsive ', 'width' => 52]) !!}
            </a>
          </li>
          <li>
            <a href="https://plus.google.com/+BonsumBerlin">
              {!! IMG('general.social.googleplus', true, ['class' => 'img-responsive ', 'width' => 52]) !!}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="bottom-strip">
    <div class="container">
      <ul class="row list-unstyled clearfix">
        <li class="pull-left">
          <a href="{{action('StaticController@imprint')}}">{!! LR('footer.imprint') !!}</a>
          <a href="{{action('StaticController@terms')}}">{!! LR('footer.terms') !!}</a>
          <a href="{{action('StaticController@privacy')}}">{!! LR('footer.privacy') !!}</a>
        </li>
        <li class="pull-right">{!! LR('general.copyright_note') !!}</li>
      </ul>
    </div>
  </div>

<script>
{!! app('JavaScript')->buildJavaScriptSyntax(app('frontend')->makeVars()) !!}
</script>

@foreach ($scripts as $script)
{!! HTML::script("js/${script}") !!}
@endforeach


@foreach ($inline_scripts as $script)
<script>
{!! $script !!}
</script>
@endforeach

@if (isset($share_tags))
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "f7df29d4-d0e8-47aa-8472-8d628f599998", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
@endif

@if (isset($piwik_site_id))
@include('partials.piwik')
@endif


{{-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries --}}
{{-- WARNING: Respond.js doesn't work if you view the page via file:// --}}
<!--[if lt IE 9]>
{{!! HTML::script('https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js') !!}}
{{!! HTML::script('https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js') !!}}
<![endif]-->
@include('partials.angular')



</footer>



