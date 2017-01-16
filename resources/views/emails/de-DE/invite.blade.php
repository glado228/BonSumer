@extends('emails.de-DE.layouts.master')

@section('content')
{!!$personal_msg!!}

{{$name}} hat Dich zu Bonsum eingeladen. Bitte folge diesem Link um Dich bei Bonsum zu registrieren:

{{$email_invite_url}}
@stop
