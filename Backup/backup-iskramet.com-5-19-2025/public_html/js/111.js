document.addEventListener("DOMContentLoaded", function () {
    function c(b) {
        var a = [];
        a = {
            projectid: document.getElementById("allrecords").getAttribute("data-tilda-project-id"),
            pageid: document.getElementById("allrecords").getAttribute("data-tilda-page-id"),
            pageurl: document.location.href,
            timezone: (new Date).getTimezoneOffset()
        };
        var c = localStorage.getItem("memberarea_profile");
        null != c && (c = JSON.parse(c), a.session = c.session);
        var d = new XMLHttpRequest;
        d.open("POST", b, !0);
        d.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        d.onreadystatechange = function () {
            if (d.readyState == XMLHttpRequest.DONE) if (200 == d.status) {
                var a = d.response;
                switch (a.action) {
                    case "display":
                        a = a.display;
                        document.open();
                        document.write(a);
                        document.close();
                        break;
                    case "redirect":
                        console.log(a), document.location.replace(document.location.origin + a.redirect)
                }
            } else alert(f[e])
        };
        d.timeout = 3E4;
        d.ontimeout = function () {
            alert(f[e])
        };
        d.responseType = "json";
        d.withCredentials = !0;
        d.send(function (a) {
            var b = [], c;
            for (c in a) a.hasOwnProperty(c) && b.push(encodeURIComponent(c) +
                "=" + encodeURIComponent(a[c]));
            return b.join("&")
        }(a))
    }

    var e = (navigator.language || navigator.userLanguage).substring(0, 2).toLowerCase();
    e = "ru" === e ? "ru" : "en";
    var f = {
        ru: "\u041f\u0440\u043e\u0438\u0437\u043e\u0448\u043b\u0430 \u043e\u0448\u0438\u0431\u043a\u0430 \u043f\u0440\u0438 \u043f\u0435\u0440\u0435\u0434\u0430\u0447\u0435 \u0437\u0430\u043f\u0440\u043e\u0441\u0430.\n\u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u043f\u0440\u043e\u0432\u0435\u0440\u044c\u0442\u0435 \u0412\u0430\u0448\u0435 \u0441\u043e\u0435\u0434\u0438\u043d\u0435\u043d\u0438\u0435 \u0441 \u0418\u043d\u0442\u0435\u0440\u043d\u0435\u0442\u043e\u043c.",
        en: "An error occurred while sending the request.\nPlease check your internet connection."
    };
    document.title = "Loading";
    var a = document.getElementById("tilda-membersarea-js");
    if ("undefined" == typeof a || null == a) a = document.getElementsByTagName("script"), a = a[a.length - 1];
    var b = document.createElement("a");
    b.href = a.src;
    a = b.protocol + "//" + b.host + "/";
    b = document.location.pathname.substring(1);
    "/" == b.substring(b.length - 1) && (b = b.substring(0, b.length - 1));
    b = b.split("/");
    if ("undefined" != typeof b[0] && "members" == b[0])
        if (document.title = "Members area", "undefined" != typeof b[1])
        switch ("courses" == b[1] && null == localStorage.getItem("memberarea_profile") && (localStorage.clear(), c(a + "get/logout/")), b[1]) {
        case "login":
            c(a + "get/login/");
            break;
        case "logout":
            localStorage.clear();
            c(a + "get/logout/");
            break;
        case "signup":
            c(a + "get/signup/");
            break;
        case "recovery-password":
            b = "";
            null != localStorage.getItem("regToGroup") ? (b = localStorage.getItem("regToGroup"), localStorage.clear(), localStorage.setItem("regToGroup", b)) : localStorage.clear();
            c(a + "get/recoverpass/");
            break;
        case "courses":
            "undefined" == typeof b[2] ? document.location.replace(a + "members/") : "taskcheck" == b[2] ? c(a + "get/taskcheck/") : c(a + "get/course/")
    } else null != localStorage.getItem("regToGroup") && localStorage.removeItem("regToGroup"), c(a + "get/mainpage/"); else "undefined" != typeof document.getElementById("allrecords").getAttribute("data-tilda-page-id") ? c(a + "get/page/") : document.location.replace(a + "members/")
});