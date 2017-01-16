@extends('emails.en-UK.layouts.master')

@section('content')
Hello {{ $name }},

Welcome to Bonsum! This link will take you to your account:

{{ $account_url }}

Have fun
@stop
