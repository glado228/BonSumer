@extends('emails.en-UK.layouts.master')

@section('content')
{!!$personal_msg!!}

{{$name}} invited you to join Bonsum. Please use the link below to sign up:

{{$email_invite_url}}
@stop
