@extends('layouts.master')

{{--
$article -> Article model (required)
$backUrl -> url to go back
--}}

@section('content')

@include('partials.toolbar')


<div class="container">

	<div class="row">

		<div class="col-sm-4 col-sm-push-8 col-lg-3 col-lg-push-9 vspace-xs-below-15">
			<div sticky offset="70" media-query="min-width: 768px" class="article-side-search" bottom-line=".site-footer" confine="true">

				{!! Form::open(['action' => 'ArticleController@index', 'method' => 'GET']) !!}
				<div class="input-group">
					<input type="text" name="searchString" class="form-control" placeholder="{{ trans('article.search_placeholder') }}">
					<span class="input-group-btn">
						<button type="submut" class="btn btn-bonsum">
							<span class="glyphicon glyphicon-search"></span>
							</button>
					</span>
				</div>
				{!! Form::close() !!}

				<p class="text-center vspace-above-15 social-buttons hidden-xs">
					<span class='st_facebook_large' displayText='Facebook'></span>
					<span class='st_twitter_large' displayText='Tweet'></span>
					<span class='st_linkedin_large' displayText='LinkedIn'></span>
					<span class='st_googleplus_large' displayText='Google +'></span>
					<span class='st_email_large' displayText='Email'></span>
				</p>

				@include('partials.newsletter_widget', ["className" => "hidden-xs"])
			</div>
		</div>

		<div class="col-sm-8 col-sm-pull-4 col-lg-9 col-lg-pull-3 vspace-below-25">

			<h1>{{ $article->title }}</h1>

			<p><i>{{ with(new Jenssegers\Date\Date($article->date))->toFormattedDateString() }}</i></p>

{{--
			<h4>
				@foreach (array_flatten($article->tags) as $tag)
				<span class="label label-info">{{ $tag }}</span>
				@endforeach
			</h4>
--}}
			<p>{!! LR('article.authors') !!}&nbsp;{{ implode(', ', array_flatten($article->authors)) }}</p>

			<strong>{{ $article->description }}</strong>


			@if ($article->image)
			<img class="vspace-above-25 img-responsive" width="95%" src="{{ Bonsum\Helpers\Resource::getMediaURL($article->image) }}"></img>
			@endif


			<p>{!! $article->body !!}</p>

		</div>


	</div>
</div>
@endsection
