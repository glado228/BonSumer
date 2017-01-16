@extends('emails.de-DE.layouts.master')

@section('content')
Hallo {{ $name }},

Du hast Deine gesammelten Bonets soeben erfolgreich für einen Gutschein im Wert von {{ $amount }} € für unseren Partnershop {{ $shop }} eingelöst.

Informiere Dich regelmäßig über unsere Einlösemöglichkeiten und entschiede, ob Du Deine gesammelten Bonets z.B. an eine soziale Organisation spenden oder in Einkaufsgutscheine einlösen möchtest.

Deinen aktuellen Kontostand findest Du jederzeit in unserem Mitgliederbereich:

{{ $account_url }}

Wir wünschen Dir viel Spaß beim Einlösen,
@stop
