@extends('app')

@section('content')
    <div class="ng-cloak" ng-show="resized" ng-controller="SizerController">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                @include('dashboard.weather')
            </div>
            <div class="col-lg-6 col-md-6">
                @include('dashboard.rss')
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6">
                @include('dashboard.gmail')
            </div>
            <div class="col-lg-6 col-md-6">
                @include('dashboard.calendar')
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6">
                @include('dashboard.quote')
            </div>
            <div class="col-lg-6 col-md-6" id="new-event-container">
                @include('dashboard.custom-event')
            </div>
        </div>
    </div>

@endsection