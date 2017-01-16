@extends('emails.en-UK.layouts.master')

@section('content')
Hello {{$name}},

Well done! You just recruited a new Bonsumer and earned a reward. We credited your account with {{$bonets}} Bonets.

We hope you keep having fun with Bonsum
@stop
