@extends('emails.en-UK.layouts.master')

@section('content')
Hi {{$name}},

You donated {{$bonets}} bonets, corresponding to {{$currency}} {{$amount}}, to {{$receiver}}.

Thank you for your donation!

@stop
