{{--
  Variables:

  $shop: optional
  $backUrl: Url to go back to the shop list
--}}

@extends('layouts.master')

@section('content')

    <div class="container-fluid" data-ng-controller="ShopFormController">

        <a href="{{ $backUrl }}">&#8592; Back to the shops list</a>

        {!! Form::open(['novalidate' => '', 'name' => 'form']) !!}
        <fieldset data-ng-disabled="isSaving">

            <div class="form-group row">
                <div class="col-sm-9">
                    <h3>{{ isset($shop) ? 'You are editing the shop with ID ' . $shop->shop_id : 'You are creating a new shop' }}</h3>
                </div>
                <div class="col-sm-3 text-sm-right">
                    {!! Form::submit((isset($shop) ? 'Update shop' : 'Create shop'), ['data-ng-click' => 'submit($event)','class' => 'btn btn-primary']) !!}
                    @if (isset($shop))
                        <button type="button" data-ng-click="delete()" class="btn btn-default"
                                title="{{ trans('shop.delete') }}">
                            <span class="glyphicon glyphicon-trash"></span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="form-group row" data-ng-class="{'has-error': showErrorFor('shop_id')}">
                <div class="col-sm-4">
                    <label for="name">Shop ID:</label>
                    {!!
                      Form::text('shop_id',
                                null,
                                array('ng-change' => 'changed()',
                                'ng-model-options' => "{ debounce: 500 }",
                                'data-ng-model' => 'formdata.shop_id',
                                'class' => 'form-control',
                                'placeholder' => 'Enter the shop\'s ID or leave empty ot auto-assign.'))
                    !!}
                    <label class="control-label" data-ng-show="showErrorFor('shop_id')" data-ng-bind="errors.shop_id"
                           for="shop_id"></label>
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
                              'placeholder' => 'Enter the shop\'s name.'))
                  !!}
                    <label class="control-label" data-ng-show="showErrorFor('name')" data-ng-bind="errors.name"
                           for="name"></label>
                </div>
                <div class="col-sm-4">
                    <label for="visible">Shop's visibility:</label>

                    <div class="checkbox">
                        <label>
                            {!!
                            Form::checkbox('visible', 1, NULL, [
                              'data-ng-model' => 'formdata.visible'
                            ])
                            !!}
                            If checked, the shop will be visible to users.
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label for="select">Currency:</label>

                    <div class="selectbox">
                        <label>
                            {!!
                            Form::checkbox('visible', 1, NULL, [
                              'data-ng-model' => 'formdata.visible'
                            ])
                            !!}
                            If checked, the shop will be visible to users.
                        </label>
                    </div>
                </div>
            </div>


            <div class="form-group row">
                <div class="col-sm-2" data-ng-class="{'has-error': showErrorFor('affiliate')}">
                    <label>Affiliate network:&nbsp;</label>
                    <select data-ng-change="changed()" class="form-control"
                            data-ng-options="item as item for item in affiliates"
                            data-ng-model="formdata.affiliate"></select>
                    <label class="control-label" data-ng-show="showErrorFor('affiliate')"
                           data-ng-bind="errors.affiliate"></label>
                </div>

                <div class="col-sm-6" data-ng-class="{'has-error': showErrorFor('link')}">
                    <label>Link:&nbsp;</label>
                    {!!
                      Form::text('link',
                                null,
                                array('ng-change' => 'changed()',
                                'ng-model-options' => "{ debounce: 500 }",
                                'data-ng-model' => 'formdata.link',
                                'class' => 'form-control',
                                'placeholder' => 'URL to the shop.'))
                  !!}
                    <label class="control-label" data-ng-show="showErrorFor('link')" data-ng-bind="errors.link"></label>

                    <p class="help-block">Required: it will be used to redirect to the shop. In case of a shop belonging
                        to an affiliate network, this link must be provided by the affiliate network.
                    </p>
                </div>

                <div class="col-sm-2" data-ng-class="{'has-error': showErrorFor('reward_type')}">
                    <label>Type of reward:&nbsp;</label>
                    <select data-ng-change="changed()" class="form-control"
                            data-ng-options="item.value as item.label for item in rewardTypes"
                            data-ng-model="formdata.reward_type"></select>
                    <label class="control-label" data-ng-show="showErrorFor('reward_type')"
                           data-ng-bind="errors.reward_type"></label>
                </div>

                <div class="col-sm-2" data-ng-cloak>
                    <div class="fade-in" data-ng-show="formdata.reward_type === REWARD_TYPE_PROPORTIONAL"
                         data-ng-class="{'has-error': showErrorFor('proportional_reward')}">
                        <label>Bonets per euro:&nbsp;</label>
                        {!!
                      Form::text('proportional_reward',
                              null,
                              array('ng-change' => 'changed()',
                              'ng-model-options' => "{ debounce: 500 }",
                              'data-ng-model' => 'formdata.proportional_reward',
                              'class' => 'form-control'))
                      !!}
                        <label class="control-label" data-ng-show="showErrorFor('proportional_reward')"
                               data-ng-bind="errors.proportional_reward"></label>
                    </div>
                    <div class="fade-in" data-ng-show="formdata.reward_type === REWARD_TYPE_FIXED"
                         data-ng-class="{'has-error': showErrorFor('fixed_reward')}">
                        <label>Bonets per purchase:&nbsp;</label>
                        {!!
                      Form::text('fixed_reward',
                              null,
                              array('ng-change' => 'changed()',
                              'ng-model-options' => "{ debounce: 500 }",
                              'data-ng-model' => 'formdata.fixed_reward',
                              'class' => 'form-control'))
                        !!}
                        <label class="control-label" data-ng-show="showErrorFor('fixed_reward')"
                               data-ng-bind="errors.fixed_reward"></label>
                    </div>
                </div>
            </div>

            <div class="form-group row">

                <div class="col-sm-2" data-ng-cloak data-ng-class="{'has-error': showErrorFor('shop_type')}">
                    <label>Shop type:&nbsp;</label>

                    <div data-ng-repeat="label in shop_types_labels">
                        <div class="checkbox">
                            <label>
                                {!!
                                Form::checkbox('', 1, NULL, [
                                  'data-ng-model' => 'formdata.shop_type[$index]'
                                ])
                                !!}
                                @{{ label }}
                            </label>
                        </div>
                    </div>
                    <label class="control-label" data-ng-show="showErrorFor('shop_type')"
                           data-ng-bind="errors.shop_type"></label>
                </div>

                <div class="col-sm-2" data-ng-cloak data-ng-class="{'has-error': showErrorFor('shop_criteria')}">
                    <label>Sustainability criteria:&nbsp;</label>

                    <div data-ng-repeat="label in shop_criteria_labels">
                        <div class="checkbox">
                            <label>
                                {!!
                                Form::checkbox('', 1, NULL, [
                                  'data-ng-model' => 'formdata.shop_criteria[$index]'
                                ])
                                !!}
                                @{{ label }}
                            </label>
                        </div>
                    </div>
                    <label class="control-label" data-ng-show="showErrorFor('shop_criteria')"
                           data-ng-bind="errors.shop_criteria"></label>
                </div>

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
                    <label class="control-label" data-ng-show="showErrorFor('popularity')"
                           data-ng-bind="errors.popularity"></label>

                    <p class="help-block">From 0 (least popular) to 100 (most popular).</p>
                </div>
            </div>

            <div class="form-group row">

                <div class="col-sm-6" data-ng-class="{'has-error': errors.thumbnail}">
                    <label>Shop's thumbnail.</label>

                    <p class="help-block">It will be shown on the shop overview page.</p>

                    <p class="help-block vspace-above-15" data-ng-show="formdata.thumbnail"
                       data-ng-bind="'Current thumbnail location: ' + (formdata.thumbnail || '<empty>')"></p>

                    <div class="vspace-above-15 text-center editable-clickable" data-ng-click="changeThumbnail()"
                         style="min-height:100px;">
                        <img class="img-responsive" data-ng-src="@{{Utils.makeImageLink(formdata.thumbnail)}}"
                             alt="Image not found, click to add" title="Click to change image"></img>
                    </div>
                    <label class="control-label" data-ng-show="errors.thumbnail"
                           data-ng-bind="errors.thumbnail"></label>
                </div>

                <div class="col-sm-6" data-ng-class="{'has-error': errors.thumbnail_mouseover}">
                    <label>Shop's mouseover thumbnail.</label>

                    <p class="help-block">It will be shown on the shop overview page when the mouse pointer hovers over
                        the shop.</p>

                    <p class="help-block vspace-above-15" data-ng-show="formdata.thumbnail_mouseover"
                       data-ng-bind="'Current thumbnail location: ' + (formdata.thumbnail_mouseover || '<empty>')"></p>

                    <div class="vspace-above-15 text-center editable-clickable"
                         data-ng-click="changeMouseoverThumbnail()" style="min-height:100px;">
                        <img class="img-responsive" data-ng-src="@{{Utils.makeImageLink(formdata.thumbnail_mouseover)}}"
                             alt="Image not found, click to add" title="Click to change image"></img>
                    </div>
                    <label class="control-label" data-ng-show="errors.thumbnail_mouseover"
                           data-ng-bind="errors.thumbnail_mouseover"></label>
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
                  'placeholder' => 'Enter the shop\'s description. This will appear in the overview page.'))
                !!}
                <label class="control-label" data-ng-show="showErrorFor('description')"
                       data-ng-bind="errors.description" for="description"></label>
            </div>

            <div class="form-group" data-ng-class="{'has-error': showErrorFor('tags') }">
                <label for="tags">Tags associated with the shop, used for searching:</label>
                {!!
                Form::textarea('tags', null,
                [
                  'name' => 'donation_sizes',
                  'placeholder' => 'Enter a list of comma separated tags for the shop',
                  'data-ng-model' => 'formdata.tags',
                  'class' => 'form-control',
                  'rows' => '6',
                  'data-ng-class' => "{'ng-invalid': showErrorFor('tags')}"
                ])
                !!}
                <label class="control-label" data-ng-show="showErrorFor('tags')" data-ng-bind="errors.tags"
                       for="tags"></label>
            </div>

        </fieldset>

        {!! Form::close() !!}

        <fieldset data-ng-disabled="voucherOp || !formdata._id">
            <p class="text-info help-block" data-ng-show="!formdata._id">You can add vouchers only after creating the
                shop.</p>

            <div class="form-group row">
                <div class="col-sm-4">
                    <label class="control-label text-danger" data-ng-show="showErrorFor('vouchers')"
                           data-ng-bind="errors.vouchers"></label>
                    <label data-ng-cloak>Vouchers available for this shop:&nbsp;@{{vouchers.length}}</label>

                    <div ag-grid="vouchersGridOptions" class="ag-fresh shop-admin-grid" style="height: 300px;"></div>
                </div>

                <div class="col-sm-6">
                    <label>Add new vouchers&nbsp;</label>

                    <div class="row">
                        <div class="col-sm-8" data-ng-class="{'has-error': errors.vouchers.codes}">
                            <label>Single code or comma separated codes:&nbsp;</label>
                            <textarea type="textarea" rows="8" class="form-control" ng-model-options="{ debounce: 500 }"
                                      data-ng-change="newVoucherChanged()"
                                      data-ng-model="new_voucher_form.codes"></textarea>
                            <label class="control-label" data-ng-show="errors.vouchers.codes"
                                   data-ng-bind="errors.vouchers.codes"></label>

                            <div class="fade-in fade-out" data-ng-show="voucher_added" ng-cloak>
                                <label class="text-success">Voucher added!&nbsp;</label>
                            </div>
                        </div>
                        <div class="col-sm-2" data-ng-class="{'has-error': errors.vouchers.value}">
                            <label>Value:&nbsp;</label>
                            <input type="text" class="form-control" ng-model-options="{ debounce: 500 }"
                                   data-ng-change="newVoucherChanged()" data-ng-model="new_voucher_form.value"></input>
                            <label class="control-label" data-ng-show="errors.vouchers.value"
                                   data-ng-bind="errors.vouchers.value"></label>
                        </div>
                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <button type="button" data-ng-click="addVoucher()"
                                    class="form-control btn-sm btn btn-primary"><span
                                        class="glyphicon glyphicon-plus"></span></button>
                        </div>
                    </div>

                    <label class="vspace-above-15">Select a voucher from table to delete&nbsp;</label>

                    <div class="row">
                        <div class="col-sm-6" data-ng-class="{'has-error': errors.delete_voucher.code}">
                            <label>Selected code:&nbsp;</label>
                            <input readonly type="text" class="form-control"
                                   data-ng-model="vouchersGridOptions.selectedRows[0].code"></input>
                            <label class="control-label" data-ng-show="errors.delete_voucher.code"
                                   data-ng-bind="errors.delete_voucher.code"></label>

                            <div class="fade-in fade-out" data-ng-show="voucher_deleted" ng-cloak>
                                <label class="text-success">Voucher deleted!&nbsp;</label>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <button type="button" data-ng-disabled="!vouchersGridOptions.selectedRows[0]"
                                    data-ng-click="deleteVoucher()" class="form-control btn-sm btn btn-primary"><span
                                        class="glyphicon glyphicon-minus"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

    </div>

@endsection
