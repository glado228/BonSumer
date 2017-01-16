<!DOCTYPE html>
<html lang="{{app('localization')->getLang()}}" data-ng-app="bonsumApp">
  <head>
    <meta charset="utf-8">

    <meta name="description" content="{{ isset($meta_description) ? trans($meta_description) : trans('seo.home.meta_description') }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="google-site-verification" content="xda3JWqI5lpy3omd392DWRaVbAQFkj7c8Vq2yjkzSYE">
    @if (isset($meta_robots))
    <meta name="robots" content="{{$meta_robots}}">
    @endif

    @foreach ($css as $css_path)
    {!! HTML::style("css/${css_path}") !!}
    @endforeach

    @foreach ($scripts as $script)
    {!! HTML::script("js/${script}") !!}
    @endforeach

    @foreach ($inline_scripts as $script)
    <script>
    {!! $script !!}
    </script>
    @endforeach


    <title>{{ $title_tag or trans('seo.home.title_tag') }}</title>
  </head>
  <body>
    @if ($adminMode)
      @include('admin.partials.admintoolbar')
    @endif

    <div class="{{ isset($bonsum_background_image) ? 'bonsum-background-image' : '' }}" style="min-height: 1200px">

    	@yield('content')

    </div>

    @include('partials.footer')

  </body>

</html>
