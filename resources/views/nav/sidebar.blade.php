<div id="sidebar-menu">
    <ul>
        <li class="text-muted menu-title">{{ trans('general.navigation') }}</li>

        <li>
            <a href="{{ url('/') }}">
                <i class="zmdi zmdi-view-dashboard"></i>
                <span> {{ trans('general.dashboard') }} </span>
            </a>
        </li>

        {{--<li class="has_sub">--}}
            {{--<a href="javascript:void(0);" class="n-waves-effect">--}}
                {{--<i class="zmdi zmdi-map"></i>--}}
                {{--<span> {{ trans('general.language') }} </span>--}}
                {{--<span class="menu-arrow"></span>--}}
            {{--</a>--}}
            {{--<ul class="list-unstyled">--}}
                {{--@foreach(config('languages') as $slug => $title)--}}
                    {{--<li class="{{ ($slug == auth()->user()->lang() ? 'active' : '') }}">--}}
                        {{--<a href="{{ route('user.prefs.lang', ['language' => $slug]) }}">{{ $title }}</a>--}}
                    {{--</li>--}}
                {{--@endforeach--}}
            {{--</ul>--}}
        {{--</li>--}}

        {{--<li class="has_sub">--}}
            {{--<a href="javascript:void(0);" class="n-waves-effect">--}}
                {{--<i class="zmdi zmdi-layers"></i>--}}
                {{--<span> {{ trans('general.theme') }} </span>--}}
                {{--<span class="menu-arrow"></span>--}}
            {{--</a>--}}
            {{--<ul class="list-unstyled">--}}
                {{--@foreach(config('app.themes') as $layout)--}}
                    {{--<li class="{{ ($layout == auth()->user()->theme() ? 'active' : '') }}">--}}
                        {{--<a href="{{ route('user.prefs.layout', ['layout' => $layout]) }}">{{ trans('layout.' . $layout) }}</a>--}}
                    {{--</li>--}}
                {{--@endforeach--}}
            {{--</ul>--}}
        {{--</li>--}}

        <li>
            <a href="{{ url('logout') }}">
                <i class="zmdi zmdi-square-right"></i>
                <span> {{ trans('general.logout') }} </span>
            </a>
        </li>

    </ul>
    <div class="clearfix"></div>
</div>