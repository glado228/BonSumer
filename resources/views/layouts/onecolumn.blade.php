@extends('layouts.master')


@section('content')

<div class="container-fluid">

  <div class="row">

    <div class="col-sm-12">

      @section('top')
        <h2> This is a header where you can place some important messages </h2>
      @show

    </div>

  </div>

  <div class="row">

    <div class="col-sm-12">

      @section('main')
        <h3> This is where the main content of the page will go. </h3>
      @show

    </div>

  </div>

</div>

@stop
