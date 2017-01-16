@extends('emails.en-UK.layouts.master')

@section('content')
Hello {{$name}},

You signed up with Bonsum, but unfortunately you have not confirmed your email yet.
Please click on the following link to confirm.

{{$activation_link}}

@stop
