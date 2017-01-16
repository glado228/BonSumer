{{--
  $piwik_site_id = Piwik Site ID
  $piwik_callbacks
--}}

@if (isset($piwik_site_id))
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  @if (isset($piwik_callbacks))
    @foreach ($piwik_callbacks as $callback)
      _paq.push({!! json_encode($callback) !!});
    @endforeach
  @endif
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//h2427251.stratoserver.net:8005/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', {{$piwik_site_id}} ]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//h2427251.stratoserver.net:8005/piwik.php?idsite={{$piwik_site_id}}" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
@endif
