$(document).ready(function () {
    var menuHidden = true;
    $('.ti-menu').on('click', function () {
        if(menuHidden == true) {
            $('#sidebar_user_name').animate({'margin-left': '0px'}, 50);
            $('.topbar .topbar-left').animate({'margin-left': '0px'}, 200);
            $('.side-menu').animate({'margin-left': '0px'}, 200);
            $('.content-page').animate({'margin-left': '250px'}, 200);
            menuHidden = !menuHidden;
        } else {
            $('#sidebar_user_name').animate({'margin-left': '-800px'}, 200);
            $('.topbar .topbar-left').animate({'margin-left': '-210px'}, 200);
            $('.side-menu').animate({'margin-left': '-210px'}, 200);
            $('.content-page').animate({'margin-left': '40px'}, 200);
            menuHidden = !menuHidden;
        }
    })
})