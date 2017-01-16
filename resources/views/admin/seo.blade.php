@extends('layouts.master')


@section('content')


<div class="container-fluid">
	<p>
		On this page you can modify some SEO tags for our pages.
	</p>
	<p>
		You modifiy the tags just as you do with other language resources.
		Just click on the "Start editing" button.
	</p>
	<p class="vspace-below-25">
	<strong>
		Please only use simple text and do not enter any HTML. These fields are supposed to be in plain text.
	</strong>
	</p>
	<table class="table-striped table-bordered">
		<tr>
			<th>Page Name</th>
			<th>Title Tag</th>
			<th>Meta Description</th>
		</tr>

		@foreach ($views as $view)
		<tr>
			<td><i>{{ $view }}</i></td>
			<td>{!! LR('seo.'.$view.'.title_tag') !!}</td>
			<td>{!! LR('seo.'.$view.'.meta_description') !!}</td>
		</tr>
		@endforeach

	</table>

</div>

@endsection
