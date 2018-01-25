<div class="user-box">
    <div class="user-img">

            <img src="{{ auth()->user()->avatar ?: '/assets/images/users/avatar-1.jpg' }}" alt="user-img" title="Mat Helme" class="img-circle img-thumbnail img-responsive"/>

        {{--<div class="user-status online"><i class="zmdi zmdi-dot-circle"></i></div>--}}
    </div>

    <h5 id="sidebar_user_name">{{ auth()->user()->name }}</h5>
</div>