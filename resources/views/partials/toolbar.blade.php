<nav class="navbar navbar-default navbar-user" data-ng-controller="NavbarController">
  <div class="container">
  	<div class="navbar-header">
      <button type="button" ng-init="navCollapsed = true" ng-click="navCollapsed = !navCollapsed" class="navbar-toggle">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/">
        {!! IMG('general.logo_tagline', null, ['class' => 'logo-image']) !!}
      </a>
    </div>
    <div collapse="navCollapsed" class="collapse navbar-collapse text-center">
      <ul class="nav navbar-nav navbar-center">
        <li>
          <a href="{{ action('ShopController@index') }}">
            {!! LR('navbar.shopping') !!}
          </a>
        </li>
        <li>
          <a href="{{ action('RedeemController@index') }}">
            {!! LR('navbar.redeem') !!}
          </a>
        </li>
        <li class="hidden-xs separator"><a>|</a></li>
        <li>
          <a href="{{ action('ArticleController@index') }}">
            {!! LR('navbar.magazine') !!}
          </a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        @if (Auth::guest())
        <li><a href="{{ action('Auth\AuthController@getLogin') }}">{!! LR('navbar.login') !!}</a></li>
        <li class="hidden-xs separator"><a>|</a></li>
        <li><a href="{{ action('Auth\AuthController@getSignup') }}">{!! LR('navbar.register') !!}</a></li>
        @else
        <li class="dropdown" dropdown>
          <a href="#" class="dropdown-toggle" dropdown-toggle>{!! LR('navbar.loggedin', ['user' => Auth::user()->firstname, 'bonets' => Auth::user()->bonets]) !!}</a>
          <ul class="dropdown-menu">
            <li><a href="{{ action('AccountController@index') .'#!/history/bonets' }}">{!! LR('account.collected_bonets') !!}</a></li>
            <li><a href="{{ action('AccountController@index') .'#!/history/vouchers' }}">{!! LR('account.vouchers') !!}</a></li>
            <li><a href="{{ action('AccountController@index') .'#!/history/donations' }}">{!! LR('account.donations') !!}</a></li>
            <li><a href="{{ action('AccountController@index') .'#!/refer_friends' }}">{!! LR('account.refer_friends') !!}</a></li>
            <li><a href="#" data-ng-click="logout()">{!! LR('navbar.logout') !!}</a></li>
          </ul>
        </li>
        @endif
      </ul>
    </div>
		<div class="dropdown flags-menu" dropdown>
			<a href="#" class="dropdown-toggle" dropdown-toggle>{!! IMG("flags.$current_locale") !!} <span class="hidden-xs">{!! locale_name($current_locale) !!}</span> <span class="caret"></span></a>
			<ul class="dropdown-menu">
				@foreach($locales as $locale)
					<li><a href="http://{!! $locale_hostnames[$locale] !!}">{!! IMG("flags.$locale") !!} {!! locale_name($locale) !!}</a></li>
				@endforeach
			</ul>
		</div>
  </div> <!-- container-fluid end -->
</nav>
