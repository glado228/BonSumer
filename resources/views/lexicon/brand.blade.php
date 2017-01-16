{{--
  $category => a category as an element from the language resource array lexicon.categories
  $brand => a brand as an element from the language resource array lexicon.categories.brands
  $category_index => index of the cateogory in lexicon.categories
  $brand_index => index of the brand in lexicon.categories.{{$category_index}}.brands

  $lr_prefix => the prefix to use when referring to language resources for the current brand
  (i.e. => {$lr_prefix}.name will refer to the name of the brand)

--}}

<accordion-group is-open="categories[ {{ $category_index }} ].brands[ {{ $brand_index }} ].open">
<accordion-heading>
  {!! LR($lr_prefix .'.name') !!}
</accordion-heading>

{!! IMG($lr_prefix.'.img', null, ['alt' => (isset($brand['name']) ? $brand['name'] : '')]) !!}

<p><strong>{!! LR('lexicon.attributes.category') !!}:</strong>&nbsp;{!! LR($lr_prefix.'.category') !!}</p>
<p>&nbsp;</p>
<p><strong>{!! LR('lexicon.attributes.publisher') !!}:</strong>&nbsp;{!! LR($lr_prefix.'.publisher') !!}</p>
<p>&nbsp;</p>
<p><strong>{!! LR('lexicon.attributes.objectives') !!}:</strong>&nbsp;{!! LR($lr_prefix.'.objectives') !!}</p>
<p>&nbsp;</p>
<p><strong>{!! LR('lexicon.attributes.standards') !!}:</strong>
  <ul>
  	@if (isset($brand['standards']) && is_array($brand['standards']))
	  	@foreach ($brand['standards'] as $index => $standard)
		    <li>{!! LR($lr_prefix.'.standards.'.$index) !!}</li>
	    @endforeach
    @endif
  </ul>
</p>
<p>&nbsp;</p>
<p><strong>Website:</strong>&nbsp;<a target="_blank" href="{{ $brand['website'] or '' }}">{!! LR($lr_prefix.'.website') !!}</a></p>

</accordion-group>
