$(document).ready(function () {

    var li = $('ul.accord > li.parent')
        , menuOpenItem = parseInt(getcookie("openMainMenu"));

    $('ul.accord > li.parent > a').on("click", function (e) {
        $('ul.accord > li.parent > a').removeClass('active');
        $('ul.accord > li.parent > .sub_parent').slideUp('normal');
        var $child = $(this).next('.sub_parent');
        if ($child.is("ul")) {
            e.preventDefault();
            var state = $child.css("display");
            if (state === "none") {
                $('.sub_parent').slideUp('normal');
                $('ul.accord > li.parent > a').removeClass('active');
                $(this).addClass('active').next('.sub_parent').slideDown('normal');
                setcookie("openMainMenu", $(this).parent("li").index(), (new Date).getTime() + (20 * 365 * 24 * 60 * 60 * 1000));
            } else {
                $(this).addClass('active').next('.sub_parent').slideUp('normal');
            }
            return false;
        } else {
            $(this).addClass('active');
            setcookie("openMainMenu", $(this).parent("li").index(), (new Date).getTime() + (20 * 365 * 24 * 60 * 60 * 1000));
        }
    });
    $(".parent a")/*.off("click")*/.on("click", function () {
        if (!$(this).next('.sub_parent').is("ul"))
            $(".menu_adaptive").removeClass("show");
    });
    $(".sub_parent li a").off("click").on("click", function () {
        //$(".sub_parent li a").removeClass("active");
        //$(this).addClass("active");
        $(".menu_adaptive").removeClass("show");
    });

    $(".icon.brackets").on("click", function () {
        $("body").addClass("unscroll");
        $("#shop").css({"opacity": "0", "display": "flex"}).animate(
            {
                opacity: 1
            }, 50, "linear", function () {
                $("#shop .wrap").addClass("show");
            }
        );

    });
    $("#shop #submit").on("click", function () {
        document.location.href = "/order/";
    });
    $("#shop #close").on("click", function () {
        $("#shop .wrap").removeClass("show");
        setTimeout(function(){$("#shop").css("display", "none");$("body").removeClass("unscroll");}, 200);
    });


    $(".icon.favorites").on("click", function () {
        updateFavor("view", 0, 0);
        $("body").addClass("unscroll");
        $("#favore").css({"opacity": "0", "display": "flex"}).animate(
            {
                opacity: 1
            }, 50, "linear", function () {
                $("#favore .wrap").addClass("show");
            }
        );

    });
    $("#favore #closefavorite").on("click", function () {
        $("#favore .wrap").removeClass("show");
        setTimeout(function(){$("#favore").css("display", "none");$("body").removeClass("unscroll");}, 200);
    });

    $("#enter_lk, .icon.log_in").on("click", function(){
        if($(".login_up").css("display") === "none") {
            $("#login_popup, .login_up").show();
        }else{
            $("#login_popup, .login_up").hide();
        }
    });
    $(".login_up").on("click", function(e){
        e.cancelBubble;
        if(e.stopPropagation) {
            e.stopPropagation();
        }
    });
    $("#login_popup").on("click", function(){
        $("#login_popup, .login_up").hide();
    });
    $("#exit").on("click", function(){
        document.location.href = document.location.pathname + "?logout";
    });
    $(".login_up .remember").on("click", function(){
        $(".passField, .login_up .social").hide();
        $("[name=mode]").val("flush");
        $("#enter").val("Отправить пароль");
        $(this).hide();
        $(".login_up .login").show();
    });
    $(".login_up .login").on("click", function(){
        $(".passField, .login_up .social").show();
        $("[name=mode]").val("");
        $("#enter").val("Войти");
        $(this).hide();
        $(".login_up .remember").show();
    });

    if (ajaxFunction()) {
        $("ul.accord li a, .sub_parent li a")/*.off("click")*/.on("click", function (e) {
            e.preventDefault();
            $(".accord li a, .parent li a, .sub_parent li a").removeClass("active");
            $(this).addClass("active");
            //.parents(".parent").addClass("active");
            setMainContent($(this).attr("href"));
            return false;
        });

        $("form[name=form_order]").on("submit", function(e){
            e.preventDefault();
            var form = $(this);
            form.find("[type=submit]").addClass("loading");
            form.addClass("disabled");
            setTimeout(function () {
                form.find("input, select, textarea").attr("disabled", true).addClass("disabled");
            }, 500);
            $.post("/", form.serialize(), function(data){
                if(data.result) {
                    form.html(data.resultText);
                    $("#order_print").on("click", function(e){
                        e.preventDefault();
                        window.print();
                        return false;
                    });
                    $("#continue_shopping").on("click", function(e){
                        e.preventDefault();
                        document.location.href = "/";
                        return false;
                    });
                }else{
                    notify("error", "Ошибка", respond.resultText);
                }
            });

            return false;
        });

        window.addEventListener('popstate', function (e) {
            var url = document.location.href.replace(document.location.protocol + '//' + document.location.host, "");
            $.when(loadContent(url, "mainpage")).then(function (answer) {
                document.title = answer.title;
                $("main").html(answer.main);
                mainInit();
            })
        });

    } else {
        $(li[menuOpenItem]).find("a").click();
    }

    mainInit();

    $(".icon.burger").off("click").on("click", function () {
        var $md = $(".menu_adaptive");
        if ($md.hasClass("show")) {
            $md.removeClass("show");
        } else {
            $md.addClass("show");
        }
    });

    $("#cookiePanel .close").on("click", function(e){
        e.preventDefault();
        $("#cookiePanel").fadeOut("fast");
        setcookie("cookiePanel", "hide", (new Date).getTime() + (20 * 365 * 24 * 60 * 60 * 1000));
    });


    /*header stick*/
    // function Header() {
    //   65 < $(window).scrollTop() ? ($("header").css("position", "sticky").css("box-shadow", "0 -3px 6px 4px var(--icon)").css("padding", //"0").on("transitionend webkitTransitionEnd oTransitionEnd otransitionend")) : ($("header").css("position", "inherit").css("box-shadow", //"none").css("padding", "0.75rem 0").on("transitionend webkitTransitionEnd oTransitionEnd otransitionend"))
    // }
    // Header(), $(window).scroll(function () {
    //     Header()
    // });

});

/*
if ('serviceWorker' in navigator) {
    // Весь код регистрации у нас асинхронный.
    navigator.serviceWorker.register('./sw.js')
        .then(() => navigator.serviceWorker.ready.then((worker) => {
            worker.sync.register('syncdata');
        }))
        .catch((err) => console.log(err));
}
// Detects if device is on iOS
var isIos = function () {
    var userAgent = window.navigator.userAgent.toLowerCase();
    return /iphone|ipad|ipod/.test(userAgent);
}
// Detects if device is in standalone mode
var isInStandaloneMode = function () {
    return (('standalone' in window.navigator) && (window.navigator.standalone));
}

// Checks if should display install popup notification:
if (isIos() && !isInStandaloneMode()) {
    console.log("Установите наше приложение!")
    this.setState({showInstallMessage: true});
}
*/

