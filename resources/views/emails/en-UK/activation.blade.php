@extends('emails.en-UK.layouts.master')

@section('content')
Hi {{ $name }},

thanks for signing up with Bonsum. In order to activate your account, click on the following activation link:

{{ $activation_link }}

Thanks,

@stop
