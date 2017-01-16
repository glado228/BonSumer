{{--
  $category => a category as an element from the language resource array lexicon.categories
  $category_index => index of the cateogory in lexicon.categories
  $lr_prefix => the prefix to use when referring to language resources for the current category
  (i.e. => {$lr_prefix}.name will refer to the name of the category)
--}}

<li>
  <div class="lexicon-category-title">{!! LR($lr_prefix.'.name') !!}</div>
  <accordion data-close-others="false">
    @foreach ($category['brands'] as $brand_index => $brand)
      @include('lexicon.brand', ['lr_prefix' => $lr_prefix.'.brands.'.$brand_index ])
    @endforeach
  </accordion>
</li>
