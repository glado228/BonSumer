{{--
  Variables:

  $donation: optional
  $backUrl: Url to go back to the redeem page
--}}

@extends('layouts.master')

@section('content')

<div class="container-fluid">

  <a href="{{ $backUrl }}">&#8592; Back to the redeem page</a>

{!! Form::open(['novalidate' => '', 'name' => 'form', 'data-ng-controller' => 'DonationOptionFormController']) !!}
  <fieldset data-ng-disabled="isSaving">

  <div class="form-group row">
    <div class="col-sm-9">
       <h3>{{ isset($donation) ? 'You are editing the donation option with ID ' . $donation->id : 'You are creating a new donation option' }}</h3>
    </div>
    <div class="col-sm-3 text-sm-right">
       {!! Form::submit((isset($donation) ? 'Update donation option' : 'Create donation option'), ['data-ng-click' => 'submit($event)','class' => 'btn btn-primary']) !!}
       @if (isset($donation))
        <button type="button" data-ng-click="delete()" class="btn btn-default" title="{{ trans('redeem.delete') }}">
          <span class="glyphicon glyphicon-trash"></span>
        </button>
       @endif
    </div>
  </div>


  <div class="form-group row">
    <div class="col-sm-4" data-ng-class="{'has-error': showErrorFor('name')}">
    <label for="name">Name:</label>
	{!!
    Form::text('name',
              null,
              array('ng-change' => 'changed()',
              'ng-model-options' => "{ debounce: 500 }",
              'data-ng-model' => 'formdata.name',
              'class' => 'form-control',
              'placeholder' => 'Enter the donation option\'s name.'))
  !!}
	  <label class="control-label" data-ng-show="showErrorFor('name')" data-ng-bind="errors.name" for="name"></label>
    </div>
    <div class="col-sm-4">
        <label for="visible">Donation option's visibility:</label>
      <div class="checkbox">
      <label>
        {!!
        Form::checkbox('visible', 1, NULL, [
          'data-ng-model' => 'formdata.visible'
        ])
        !!}
      If checked, the donation option will be visible to users.
      </label>
      </div>
    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-2" data-ng-class="{'has-error': showErrorFor('popularity')}">
        <label>Popularity:&nbsp;</label>
        {!!
      Form::text('popularity',
              null,
              array('ng-change' => 'changed()',
              'ng-model-options' => "{ debounce: 500 }",
              'data-ng-model' => 'formdata.popularity',
              'class' => 'form-control'))
        !!}
        <label class="control-label" data-ng-show="showErrorFor('popularity')" data-ng-bind="errors.popularity"></label>
        <p class="help-block">From 0 (least popular) to 100 (most popular).</p>
    </div>
  </div>

  <div class="form-group row">

    <div class="col-sm-6" data-ng-class="{'has-error': errors.thumbnail}">
      <label>Donation option's thumbnail.</label>
      <p class="help-block">It will be shown on the redeem page.</p>
      <p class="help-block vspace-above-15" data-ng-show="formdata.thumbnail" data-ng-bind="'Current thumbnail location: ' + (formdata.thumbnail || '<empty>')"></p>
      <div class="vspace-above-15 text-center editable-clickable" data-ng-click="changeThumbnail()" style="min-height: 100px;">
        <img class="img-responsive" data-ng-src="@{{Utils.makeImageLink(formdata.thumbnail)}}" alt="Image not found, click to add" title="Click to change image"></img>
      </div>
      <label class="control-label" data-ng-show="errors.thumbnail" data-ng-bind="errors.thumbnail"></label>
    </div>

    <div class="col-sm-6" data-ng-class="{'has-error': errors.thumbnail_mouseover}">
      <label>Donation option's mouseover thumbnail.</label>
      <p class="help-block">It will be shown on the redeem page when the mouse pointer hovers over the donation option.</p>
      <p class="help-block vspace-above-15" data-ng-show="formdata.thumbnail_mouseover" data-ng-bind="'Current thumbnail location: ' + (formdata.thumbnail_mouseover || '<empty>')"></p>
      <div class="vspace-above-15 text-center editable-clickable" data-ng-click="changeMouseoverThumbnail()" style="min-height: 100px;">
        <img class="img-responsive" data-ng-src="@{{Utils.makeImageLink(formdata.thumbnail_mouseover)}}" alt="Image not found, click to add" title="Click to change image"></img>
      </div>
      <label class="control-label" data-ng-show="errors.thumbnail_mouseover" data-ng-bind="errors.thumbnail_mouseover"></label>
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
    'placeholder' => 'Enter the donation option\'s description. This will appear in the overview page.'))
  !!}
  <label class="control-label" data-ng-show="showErrorFor('description')" data-ng-bind="errors.description" for="description"></label>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('tags') }">
    <label for="tags">Tags associated with the donation option, used for searching:</label>
    <tags-input
      style="height: 80px;"
      name="donation_sizes"
      placeholder="Enter a tag"
      ng-model="formdata.tags"
      min-length="3"
      max-length="50"
      on-tag-added="changed()"
      on-tag-removed="changed()"
      replace-spaces-with-dashes="false"
      data-ng-class="{'ng-invalid': showErrorFor('tags')}"
    >
    </tags-input>
    <label class="control-label" data-ng-show="showErrorFor('tags')" data-ng-bind="errors.tags" for="tags"></label>
  </div>

  <div class="form-group" data-ng-class="{'has-error': showErrorFor('donation_sizes') }">
    <label for="donation_sizes">Allowed amounts for a single donation (in Bonets):</label>
    <tags-input
      style="height: 80px;"
      name="donation_sizes"
      placeholder="Enter a donation size in Bonets"
      ng-model="formdata.donation_sizes"
      min-length="1"
      max-length="50"
      on-tag-added="changed()"
      on-tag-removed="changed()"
      replace-spaces-with-dashes="false"
      data-ng-class="{'ng-invalid': showErrorFor('donation_sizes')}"
    >
    </tags-input>
    <label class="control-label" data-ng-show="showErrorFor('donation_sizes')" data-ng-bind="errors.donation_sizes" for="donation_sizes"></label>
    <p class="help-block">You have to enter at least 1 amount</p>
  </div>

{!! Form::close() !!}

</fieldset>

</div>

@endsection
