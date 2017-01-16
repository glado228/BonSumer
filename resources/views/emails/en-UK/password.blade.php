@extends('emails.en-UK.layouts.master')

@section('content')
Hi {{ $name }},

You requested a new password. The following link will take you to a form where you will be able to enter a new password:

{{ $reset_link }}

Thanks,
@stop
