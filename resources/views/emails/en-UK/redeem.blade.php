@extends('emails.en-UK.layouts.master')

@section('content')
Hi {{$name}},

You redeemed {{$bonets}} bonets for a voucher for {{$shop}} worth {{$currency}} {{$amount}}.

Your voucher code is: {{$voucher_code}}

@stop
