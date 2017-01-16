@extends('layouts.master')


@section('content')

@include('partials.toolbar')


<div class="container" data-ng-controller="FaqController">

	<h2 class="h3_title_topone">{!! LR('faq.title') !!}</h2>

	<h2>{!! LR('faq.current_message_title') !!}</h2>
	<p>{!! LR('faq.current_message_body') !!}</p>


	<div><a href="#" data-ng-click="toggle(true)">{!! LR('faq.expand_all') !!}</a>&nbsp;<a href="#" data-ng-click="toggle(false)">{!! LR('faq.collapse_all') !!}</a></div>

	<accordion close-others="false">
		<h3 class="vspace-above-15">{!! LR('faq.about_bonsum') !!}</h3>

		<accordion-group is-open="status.open[0]">
		<accordion-heading>
		{!! LR('faq.what_is.question') !!}
	</accordion-heading>
	<h5>{!! LR('faq.what_is.answer') !!}</h5>
	</accordion-group>

	<accordion-group is-open="status.open[1]">
	<accordion-heading>
	{!! LR('faq.criteria.heading') !!}
</accordion-heading>
<h5>{!! LR('faq.criteria.general_description') !!}</h5>
<h5>
	<strong>{!! LR('faq.criteria.list_heading') !!}</strong>
</h5>

{!! IMG('shop.ethically') !!}

<h5><em>{!! LR('faq.criteria.ethical.question') !!}</em></h5>

<h5>{!! LR('faq.criteria.ethical.answer') !!}</h5>

{!! IMG('shop.animals') !!}

<h5><em>{!! LR('faq.criteria.animals.question') !!}</em></h5>

<h5>{!! LR('faq.criteria.animals.answer') !!}</h5>

{!! IMG('shop.resources') !!}

<h5><em>{!! LR('faq.criteria.resources.question') !!}</em></h5>

<h5>{!! LR('faq.criteria.resources.answer') !!}</h5>

{!! IMG('shop.healthy') !!}

<h5><em>{!! LR('faq.criteria.healthy.question') !!}</em></h5>

<h5>{!! LR('faq.criteria.healthy.answer') !!}</h5>

{!! IMG('shop.co2') !!}

<h5><em>{!! LR('faq.criteria.co2.question') !!}</em></h5>

<h5>{!! LR('faq.criteria.co2.answer') !!}</h5>

<h5>{!! LR('faq.criteria.request_for_feedback') !!}</h5>
</accordion-group>

<accordion-group is-open="status.open[2]">
<accordion-heading>
{!! LR('faq.sustainable.question') !!}
</accordion-heading>
<h5>
{!! LR('faq.sustainable.answer') !!}
</h5>
</accordion-group>

<h3 class="vspace-above-15">{!! LR('faq.bonets_section') !!}</h3>
<accordion-group is-open="status.open[3]">
<accordion-heading>
{!! LR('faq.bonets.question') !!}
</accordion-heading>
<h5>{!! LR('faq.bonets.answer') !!}
</h5>
</accordion-group>

<accordion-group is-open="status.open[4]">
<accordion-heading>
{!! LR('faq.redeem.question') !!}

</accordion-heading>
<h5>{!! LR('faq.redeem.answer', ['redeem_url' => action('RedeemController@index')]) !!}
</h5>
</accordion-group>


<accordion-group is-open="status.open[5]">
<accordion-heading>
{!! LR('faq.bonets_delay.question') !!}
</accordion-heading>
<h5>
{!! LR('faq.bonets_delay.answer', ['account_url' => action('AccountController@index') ]) !!}
</h5>
</accordion-group>

<accordion-group is-open="status.open[7]">
<accordion-heading>
{!! LR('faq.bonets_all_shops.question') !!}
</accordion-heading>
<h5>{!! LR('faq.bonets_all_shops.answer') !!}
</h5>
</accordion-group>

<accordion-group is-open="status.open[8]">
<accordion-heading>
{!! LR('faq.bonets_shop_integration.question') !!}
</accordion-heading>
<h5>{!! LR('faq.bonets_shop_integration.answer') !!}
</h5>
</accordion-group>

<accordion-group is-open="status.open[9]">
<accordion-heading>
{!! LR('faq.bonets_how_to_get.question') !!}
</accordion-heading>
<h5>{!! LR('faq.bonets_how_to_get.answer') !!}
</h5>
</accordion-group>



<h3 class="vspace-above-15">{!! LR('faq.technical_section') !!}</h3>

<accordion-group is-open="status.open[10]">
<accordion-heading>
{!! LR('faq.technical.help.question') !!}
</accordion-heading>
<h5>
{!! LR('faq.technical.help.answer') !!}


</h5>
</accordion-group>

<accordion-group is-open="status.open[11]">
<accordion-heading>
{!! LR('faq.technical.requirements.question') !!}
</accordion-heading>
<h5>
	{!! LR('faq.technical.requirements.answer') !!}
</h5>
</accordion-group>
</accordion>

<p class="vspace-below-15">{!! LR('faq.footer') !!}</p>

</div>

@endsection
