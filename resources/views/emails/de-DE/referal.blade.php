@extends('emails.de-DE.layouts.master')

@section('content')
Hallo {{$name}},

gut gemacht! Du hast soeben erfolgreich einen neuen Bonsumer geworben und Dir damit eine Belohnung verdient. Wir haben Deinem Bonsum Konto soeben {{$bonets}} Bonets gutgeschrieben.

Weiter so!

Weiterhin viel Spaß beim Bonsumieren,
@stop
