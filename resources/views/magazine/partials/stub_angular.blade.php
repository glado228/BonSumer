

<div class="article-stub" data-ng-class="{'draft': !article.visible}">

@if ($adminMode)
<div class="show-when-parent-hover parent-top-right btn-group" data-ng-controller="ArticleStubController">
	<a class="btn btn-default" title="{{ trans('article.edit') }}" href="@{{ article.edit_link}}">
	  <span class="glyphicon glyphicon-pencil"></span>
	</a>
	<div data-ng-if="article.visible">
	<button type="button" class="btn btn-default" title="{{ trans('article.hide') }}" data-ng-click="setVisibility(false)">
	  <span class="glyphicon glyphicon-eye-close"></span>
	</button>
	</div>
	<div data-ng-if="!article.visible">
	<button type="button" class="btn btn-default" title="{{ trans('article.show') }}" data-ng-click="setVisibility(true)">
	  <span class="glyphicon glyphicon-eye-open"></span>
	</button>
	</div>
	<button type="button" data-ng-click="delete()" class="btn btn-default" title="{{ trans('article.delete') }}">
	  <span class="glyphicon glyphicon-trash"></span>
	</button>
</div>
@endif

<h3 class="text-medium">@{{ article.title }}</h3>

<h5><i>@{{ article.localized_date }}</i></h5>
{{--
<h5 class="line-height-150p">
	<span data-ng-repeat="tag in article.tags" class="article-tags label label-info">@{{ tag.text }}</span>
</h5>
--}}

<strong>
       <span ng-repeat="a in article.authors">@{{ (!$first ? ', ' : '') + a.text}}</span>
</strong>

<div ng-if="article.image_url || article.thumbnail_url" class="row">
<img class="img-responsive vspace-above-15" width="95%" data-ng-src="@{{article.thumbnail_url || article.image_url}}"></img>
</div>
<div class="vspace-above-15 text-medium">@{{ article.description }}</div>
<div class="text-left vspace-above-25">
	<div data-ng-if="article.visible">
		<a class="btn btn-bonsum btn-xs-block" ng-href="{{action('ArticleController@show',NULL)}}/@{{article.url_friendly_title}}">{{ trans('article.read_more') }}</a>
	</div>
	@if ($adminMode)
	<div data-ng-if="!article.visible">
          <a class="btn btn-bonsum btn-xs-block" ng-href="{{action('ArticleController@showInvisible',NULL)}}/@{{article.url_friendly_title}}">{{ trans('article.read_more') }}</a>
   	</div>
   	@endif
</div>

</div>
