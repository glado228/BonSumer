@extends('layouts.master')

@section('content')

    @include('partials.toolbar')

    <section class="main-section">
        <div class="container">
            <div class="row disable-xs">
                <h2 class="text-center">{!! LR('ambassador.title') !!}</h2>

                <p class="text-left text-medium ambassador-top-text">{!! LR('ambassador.top_text') !!}</p>

                <div class="ambassador-profile-card-section row">
                    <div class="col-sm-4 text-left text-xs-center">
                        <div class="ambassador-profile-card">
                            {!! IMG('ambassador.ambassador1', null, ['class' => 'img-responsive']) !!}
                            <p class="vspace-above-15">
                                <span class="text-medium">{!! LR('ambassador.ambassador1.name') !!}</span><br/>
                            </p>

                            <p>{!! LR('ambassador.ambassador1.about') !!}</p>

                            <p>&nbsp;</p>
                        </div>
                    </div>

                    <div class="col-sm-4 text-left text-xs-center">
                        <div class="ambassador-profile-card">
                            {!! IMG('ambassador.ambassador2', null, ['class' => 'img-responsive']) !!}
                            <p class="vspace-above-15">
                                <span class="text-medium">{!! LR('ambassador.ambassador2.name') !!}</span><br/>
                            </p>

                            <p>{!! LR('ambassador.ambassador2.about') !!}</p>

                            <p>&nbsp;</p>
                        </div>
                    </div>

                    <div class="col-sm-4 text-left text-xs-center">
                        <div class="ambassador-profile-card">
                            {!! IMG('ambassador.ambassador3', null, ['class' => 'img-responsive']) !!}
                            <p class="vspace-above-15">
                                <span class="text-medium">{!! LR('ambassador.ambassador3.name') !!}</span><br/>
                            </p>

                            <p>{!! LR('ambassador.ambassador3.about') !!}</p>

                            <p>&nbsp;</p>
                        </div>
                    </div>

                </div>
                <div class="ambassador-profile-card-section row">

                    <div class="col-sm-4 text-left text-xs-center">
                        <div class="ambassador-profile-card">
                            {!! IMG('ambassador.ambassador4', null, ['class' => 'img-responsive']) !!}
                            <p class="vspace-above-15">
                                <span class="text-medium">{!! LR('ambassador.ambassador4.name') !!}</span><br/>
                            </p>

                            <p>{!! LR('ambassador.ambassador4.about') !!}</p>

                            <p>&nbsp;</p>
                        </div>
                    </div>

                    <div class="col-sm-4 text-left text-xs-center">
                        <div class="ambassador-profile-card">
                            {!! IMG('ambassador.ambassador5', null, ['class' => 'img-responsive']) !!}
                            <p class="vspace-above-15">
                                <span class="text-medium">{!! LR('ambassador.ambassador5.name') !!}</span><br/>
                            </p>

                            <p>{!! LR('ambassador.ambassador5.about') !!}</p>

                            <p>&nbsp;</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <!-- main-section container -->
    </section>
@endsection
