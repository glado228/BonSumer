

<nav class="navbar navbar-inverse navbar-fixed-top" data-ng-controller="AdminToolBarController">
  <div class="container-fluid">
  	<div class="navbar-header">
      <a href="{{action('HomeController@index')}}" class="navbar-brand">Bonsum Admin Bar</a>
   	  <button type="button" ng-init="navCollapsed = true" ng-click="navCollapsed = !navCollapsed" class="navbar-toggle">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div collapse="navCollapsed" class="collapse navbar-collapse">
      <ul class="nav navbar-nav navbar-collapse">
        <li class="dropdown" dropdown>
            <a href="#" class="dropdown-toggle" dropdown-toggle>
              Admin pages
            </a>
            <ul class="dropdown-menu">
              <li><a href="{{ action('Admin\AffiliateController@showTransactions') }}">Affiliate transactions</a></li>
              <li><a href="{{ action('Admin\DonationController@index') }}">Donations overview</a></li>
              <li><a href="{{ action('Admin\UserController@showUsers') }}">User list</a></li>
              <li><a href="{{ action('Auth\AuthController@getSignup') }}">Sign-up new user</a></li>
              <li><a href="{{ action('StaticController@seo') }}">SEO tags</a></li>
              <li><a href="{{ action('Admin\SyncController@index') }}">Sync resources</a></li>
            </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-collapse" data-ng-show="howManyChanged()">
        <li>
          <form class="navbar-form">
          <button class="btn btn-primary"
            data-ng-bind="saving ? TEXT.SAVING : TEXT.SAVE_CHANGES"
            data-ng-click="saveChanges($event)"
            data-ng-cloak
            data-ng-disabled="saving || editorOpen()"
            title="@{{TEXT.SAVE_CHANGES_HINT}}">
          </button>
          <button class="btn btn-default" type="button"
            data-ng-click="toggleChanges($event)"
            data-ng-cloak
            data-ng-disabled="saving || editorOpen()"
            data-ng-class="{active: changesShowing}"
            title="@{{TEXT.SHOW_CHANGES_HINT}}">
            @{{changesShowing() ? TEXT.HIDE_CHANGES : TEXT.SHOW_CHANGES}} <span class="badge">@{{howManyChanged()}}</span>
          </button>
          <button class="btn btn-default"
            data-ng-bind="TEXT.DISCARD_CHANGES"
            data-ng-cloak
            data-ng-disabled="saving || editorOpen()"
            data-ng-click="discardChanges($event)"
            title="@{{TEXT.DISCARD_CHANGES_HINT}}">
          </button>
          </form>
        </li>
      </ul>
    	<ul class="nav navbar-nav navbar-right navbar-collapse">
{{--    <li>
          <div class="navbar-text text-center">Logged in as: {{ Auth::user()->firstname }}</div>
        </li> --}}
        <li>
          <a href="{{ Request::fullUrl() . (empty(Request::query()) ? '?' : '&') . 'asNormalUser=1' }}" target="_blank">Show as normal user</a>
        </li>
        <li>
          <!-- form class="navbar-form"-->
          <button class="btn navbar-btn btn-primary"
            data-ng-bind="editing() ? TEXT.TURN_EDIT_OFF : TEXT.TURN_EDIT_ON"
            data-ng-class="{active: editorState.active}"
            data-ng-click="toggleEditing($event)">
          </button>
          <!--/form-->
        </li>
        <li>
          <form class="navbar-form">
          {!! Form::select('setLocale', $available_locales, null, ['data-ng-model' => 'selectedLocale', 'data-ng-change' => 'localeChanged()', 'class' => 'form-control']) !!}
         </form>
        </li>
	  	</ul>
    </div>
  </div>
</nav>

{{--
<div data-ng-controller="ResourceEditorController" data-draggable
data-ng-cloak
data-ng-click="$event.stopPropagation()"
data-ng-show="isVisible()"
class="resource-editor"
data-ng-style="{'top':getVerticalOffset(), 'left':getHorizontalOffset()}">
      <text-angular
          data-ta-toolbar-class="cursor-move"
          data-ta-toolbar="@{{toolbar}}"
          data-ta-text-editor-setup="textAreaSetup"
          data-ng-model="ResourceEditorState.editorContent"
          data-ta-text-editor-class="text-editor"
          data-ta-html-editor-class="text-editor"
          >
      </text-angular>
</div>
--}}

