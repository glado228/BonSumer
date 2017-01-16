{{--
  Variables:
  $article: the Article model (optional)
  $date_format: The format used for displaying the article date
  $backUrl: Url to go back to the main article list
--}}

@extends('layouts.master')

@section('content')

<div class="container-fluid">

  <a href="{{ $backUrl }}">&#8592; Back to articles' list</a>

{!! Form::open(['novalidate' => '', 'name' => 'form', 'url' => action('ArticleController@store'), 'data-ng-controller' => 'ArticleFormController']) !!}
  <fieldset data-ng-disabled="isSaving">

  <div class="form-group row">
    <div class="col-sm-9">
       <h3>{{ isset($article) ? 'You are editing the article with ID ' . $article->id : 'You are creating a new article' }}</h3>
    </div>
    <div class="col-sm-3 text-sm-right">
       {!! Form::submit((isset($article) ? 'Update article' : 'Create article'), ['data-ng-click' => 'submit($event)','class' => 'btn btn-primary']) !!}
       @if (isset($article))
        <button type="button" data-ng-click="delete()" class="btn btn-default" title="{{ trans('article.delete') }}">
          <span class="glyphicon glyphicon-trash"></span>
        </button>
       @endif
    </div>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('title')}">
    <label for="title">Title:</label>
	{!!
    Form::text('title',
              null,
              array('ng-change' => 'changed()',
              'ng-model-options' => "{ debounce: 500 }",
              'data-ng-model' => 'formdata.title',
              'class' => 'form-control',
              'placeholder' => 'Enter the article\'s title.'))
  !!}
	<label class="control-label" data-ng-show="showErrorFor('title')" data-ng-bind="errors.title" for="title"></label>
  </div>

 <div class="form-group" data-ng-class="{'has-error': showErrorFor('url_friendly_title')}">
    <label for="title">Article's url:</label>
  {!!
    Form::text('url_friendly_title',
              null,
              array('ng-change' => 'changed()',
              'ng-model-options' => "{ debounce: 500 }",
              'data-ng-model' => 'formdata.url_friendly_title',
              'class' => 'form-control',
              'placeholder' => 'Enter the article URL.'))
  !!}
  @if (isset($article))
  <p class="help-block">Defaults to: {{ $article->url_friendly_title }}</p>
  @endif
  <label class="control-label" data-ng-show="showErrorFor('url_friendly_title')" data-ng-bind="errors.url_friendly_title" for="url_friendly_title"></label>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('title_tag')}">
    <label for="title">Article's meta title tag:</label>
  {!!
    Form::text('title_tag',
              null,
              array('ng-change' => 'changed()',
              'ng-model-options' => "{ debounce: 500 }",
              'data-ng-model' => 'formdata.title_tag',
              'class' => 'form-control',
              'placeholder' => 'Enter the meta title tag.'))
  !!}
  @if (isset($article))
  <p class="help-block">Defaults to: {{ $article->title_tag }}</p>
  @endif
  <label class="control-label" data-ng-show="showErrorFor('title_tag')" data-ng-bind="errors.title_tag" for="title_tag"></label>
  </div>

 <div class="form-group" data-ng-class="{'has-error': showErrorFor('meta_description')}">
    <label for="title">Article's meta description:</label>
  {!!
    Form::textarea('meta_description',
              null,
              array('ng-change' => 'changed()', 'rows' => '3',
              'ng-model-options' => "{ debounce: 500 }",
              'data-ng-model' => 'formdata.meta_description',
              'class' => 'form-control',
              'placeholder' => 'Enter the meta meta_description.'))
  !!}
  @if (isset($article))
  <p class="help-block">Defaults to: {{ $article->meta_description }}</p>
  @endif
  <label class="control-label" data-ng-show="showErrorFor('meta_description')" data-ng-bind="errors.meta_description" for="meta_description"></label>
  </div>

  <div class="form-group">
    <label for="visible">Article's visibility:</label>
    <div class="checkbox">
    <label>
      {!!
      Form::checkbox('visible', 1, NULL, [
        'data-ng-model' => 'formdata.visible'
      ])
      !!}
    If checked, the article will be visible to users (published)
    </label>
    </div>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('date')}" data-ng-controller="DatePickerController">
    <div class="row">
    <div class="col-sm-3">
    <label for="date">Article's publication date:</label>
    </div>
    <div class="col-sm-2">
      <div class="input-group">
      {!!
        Form::text('date', null, [
          'class' => 'form-control',
          'datepicker-popup' => $date_format,
          'data-is-open' => 'opened',
          'data-ng-model' => 'formdata.date',
          'data-ng-change' => 'changed()',
          'data-ng-model-options' => '{ debounce: 500 }'
        ])
      !!}
      <span class="input-group-btn">
      <button type="button" class="btn btn-default form-control" data-ng-click="open($event)"><span class="glyphicon glyphicon-calendar"></span></button>
      </span>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-sm-2 col-sm-offset-3">
    <label class="control-label" data-ng-show="showErrorFor('date')" data-ng-bind="errors.date" for="date"></label>
    </div>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('authors') }">
    <label for="tags">Article's author(s):</label>
    <tags-input
      style="height: 80px;"
      name="authors"
      placeholder="Add an author"
      ng-model="formdata.authors"
      min-length="3"
      max-length="50"
      on-tag-added="changed()"
      on-tag-removed="changed()"
      replace-spaces-with-dashes="false"
      data-ng-class="{'ng-invalid': showErrorFor('authors')}"
    >
    </tags-input>
    <label class="control-label" data-ng-show="showErrorFor('authors')" data-ng-bind="errors.authors" for="authors"></label>
    <p class="help-block">At least 1 up to a maximum of 5</p>
  </div>

  <div class="form-group">
    <label>Thumbnail that will be shown on the overview page.</label>
    <p class="help-block">If no thumbnail is chosen, the main image will be shown.</p>
    <p class="help-block vspace-above-15" data-ng-show="formdata.thumbnail" data-ng-bind="'Current image location: ' + (formdata.thumbnail || '<empty>')"></p>
    <div class="vspace-above-15 text-center editable-clickable" data-ng-click="changeThumbnail()" style="min-height: 100px;">
      <img class="img-responsive" data-ng-src="@{{Utils.makeImageLink(formdata.thumbnail)}}" alt="Thumbnail not found, click to add" title="Click to change image"></img>
    </div>
  </div>


  <div class="form-group">
    <label>Main image for the article.</label>
    <p class="help-block">It will be shown on the main article page.</p>
    <p class="help-block vspace-above-15" data-ng-show="formdata.image" data-ng-bind="'Current image location: ' + (formdata.image || '<empty>')"></p>
    <div class="vspace-above-15 text-center editable-clickable" data-ng-click="changeImage()" style="min-height: 100px;">
      <img class="img-responsive" data-ng-src="@{{Utils.makeImageLink(formdata.image)}}" alt="Image not found, click to add" title="Click to change image"></img>
    </div>
  </div>


  <div class="form-group" data-ng-class="{'has-error': showErrorFor('description')}">
    <label for="description">Description:</label>
	{!!
  Form::textarea('description', null,
    array('ng-change' => 'changed()',
    'ng-model-options' => "{ debounce: 1000 }",
    'data-ng-model' => 'formdata.description',
    'class' => 'form-control',
    'rows' => 4,
    'placeholder' => 'Enter the article\'s description. This will appear in the overview page.'))
  !!}
	<label class="control-label" data-ng-show="showErrorFor('description')" data-ng-bind="errors.description" for="description"></label>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('tags') }">
    <label for="tags">Tags that describe the article's topic:</label>
    <tags-input
      style="height: 80px;"
      name="tags"
      ng-model="formdata.tags"
      min-length="3"
      max-length="50"
      on-tag-added="changed()"
      on-tag-removed="changed()"
      data-ng-class="{'ng-invalid': showErrorFor('tags')}"
    >
    </tags-input>
    <label class="control-label" data-ng-show="showErrorFor('tags')" data-ng-bind="errors.tags" for="tags"></label>
    <p class="help-block">Maximum 10 tags allowed</p>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('body') }">
    <label for="body">Article body:</label>
    <div>
	<label class="control-label" data-ng-show="showErrorFor('body')" data-ng-bind="errors.body[0]" for="body"></label>
	</div>
    {!!
    Form::textarea('body', (isset($article) ? $article->body : NULL))
    !!}
  </div>

</div>

</fieldset>
{!! Form::close() !!}

</div>

@endsection
