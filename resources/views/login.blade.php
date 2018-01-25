<!DOCTYPE html>
<html ng-app="app">

@include('partials._head')

<body>
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="text-center">
        <a href="{{ url('/') }}" class="logo"><span>{{ trans('general.name') }}</span></a>
        {{--<h5 class="text-muted m-t-0 font-600">{{ config('app.name') }}</h5>--}}
    </div>
    <div class="m-t-40 card-box">
        @if ($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        @endif
        <div class="text-center">
            <h3 class="text-uppercase font-bold m-b-0">{{ trans('auth.authentication') }}</h3>
        </div>
        <div class="panel-body">
            <div class="form-group text-center">
                <br />
                <a href="{{ url('auth/google') }}" class="btn btn-danger">
                    <i class="fa fa-google-plus"></i>&nbsp;
                    {{ trans('auth.sign_in_with_g_plus') }}
                </a>
            </div>
        </div>
    </div>
    <!-- end card-box-->
</div>
<!-- end wrapper page -->

</body>
</html>
