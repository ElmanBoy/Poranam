//setcookie('cookie2years', 'Cookie будет жить 2 года', (new Date).getTime() + (2 * 365 * 24 * 60 * 60 * 1000));
// alert(getCookie('cookie2years'));
function setcookie(name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + (new Date(expires)) : "") +
        ((path) ? "; path=/" : "; path=/") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}


/**
 * Получить значение куки по имени name
 *
 */
function getcookie(name) {
    var cookie = " " + document.cookie;
    var search = " " + name + "=";
    var setStr = null;
    var offset = 0;
    var end = 0;

    if (cookie.length > 0) {
        offset = cookie.indexOf(search);

        if (offset !== -1) {
            offset += search.length;
            end = cookie.indexOf(";", offset)

            if (end === -1) {
                end = cookie.length;
            }

            setStr = unescape(cookie.substring(offset, end));
        }
    }

    return (setStr);
}

function array_unique(a) {
    //var a = this.concat();
    for (var i = 0; i < a.length; ++i) {
        for (var j = i + 1; j < a.length; ++j) {
            if (a[i] === a[j])
                a.splice(j--, 1);
        }
    }
    return a;
}

function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }

    return found;
}


function arrayItemCompare(field, order) {
    var len = arguments.length;
    if (len === 0) {
        return function (a, b) {
            return (a < b && -1) || (a > b && 1) || 0
        };
    }
    if (len === 1) {
        switch (typeof field) {
            case 'number':
                return field < 0 ?
                    function (a, b) {
                        return (a < b && 1) || (a > b && -1) || 0
                    } :
                    function (a, b) {
                        return (a < b && -1) || (a > b && 1) || 0
                    };
            case 'string':
                return function (a, b) {
                    return (a[field] < b[field] && -1) || (a[field] > b[field] && 1) || 0
                };
        }
    }
    if (len === 2 && typeof order === 'number') {
        return order < 0 ?
            function (a, b) {
                return ((a[field] < b[field] && 1) || (a[field] > b[field] && -1) || 0)
            } :
            function (a, b) {
                return ((a[field] < b[field] && -1) || (a[field] > b[field] && 1) || 0)
            };
    }
    var fields, orders;
    if (typeof field === 'object') {
        fields = Object.getOwnPropertyNames(field);
        orders = fields.map(function (key) {
            return field[key]
        });
        len = fields.length;
    } else {
        fields = new Array(len);
        orders = new Array(len);
        for (let i = len; i--;) {
            fields[i] = arguments[i];
            orders[i] = 1;
        }
    }
    return function (a, b) {
        for (let i = 0; i < len; i++) {
            if (a[fields[i]] < b[fields[i]]) return orders[i];
            if (a[fields[i]] > b[fields[i]]) return -orders[i];
        }
        return 0;
    };
}

function el_postfix(number, one, two, five) {
    number = typeof number != "undefined" ? parseInt(number) : 0;
    var out = '';
    if (number > 20) {
        var numArr = String(number);
        number = numArr[numArr.length - 1];
        out = el_postfix(number, one, two, five);
    } else if (number === 1) {
        out = one;
    } else if (number > 1 && number < 5) {
        out = two;
    } else if (number >= 5 || number === 0) {
        out = five;
    }
    return out;
}


function floatRound(num, presicion) {
    /*presicion = (presicion != undefined) ? presicion : 1;
    var m = Math.pow(10, parseInt(presicion));
    return (parseInt(num * m)) / m;
    var d = presicion || 1,
        m = Math.pow(10, d),
        n = +(d ? num * m : num).toFixed(8),
        i = Math.floor(n), f = n - i,
        e = 1e-8,
        r = (f > 0.5 - e && f < 0.5 + e) ?
            ((i % 2 == 0) ? i : i + 1) : Math.round(n);
    return d ? r / m : r;*/
    decimals = (presicion !== undefined) ? presicion : 1;
    return Number(Math.round(num + 'e' + decimals) + 'e-' + decimals);

}

function addExportFrame(path) {
    var $ef = $("#export_frame");
    if (!$ef.is("iframe")) {
        $("body").after("<iframe id='export_frame' width='0' height='0' frameborder='0'></iframe>");
    }
    $ef.attr("src", path);
}

// использование Math.round() даст неравномерное распределение!
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}


function function_exists(function_name) {
    if (typeof function_name == 'string') {
        return (typeof window[function_name] == 'function');
    } else {
        return (function_name instanceof Function);
    }
}

function genPass(maxChars) {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < maxChars; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function copyToClipboard(fieldId) {
    $(fieldId).focus().select();
    return document.execCommand('copy');
}

/*PopUp*/
function showPopup(mode, id) {
    if (!requestMode) {
        $("#popup-" + mode).html("<p style='height:400px'>&nbsp;</p>");
        $("body").addClass("no-scroll");
        $.post("/", {getPopup: 1, ajax: 1, mode: mode, id: id}, function (data) {
            //console.log(data);
            $("#popup-" + mode).html(data);
            $("#popup-" + mode + ", #popup-overlay").css("display", "block");
            centerPopup(mode);
            slidersInit("popup-" + mode);
            $(".hotel-event[data-value='" + id + "'] .ui.loader").remove();
            //initForms();
        });
    }
}

function centerPopup(mode) {
    var $popup = $("#popup-" + mode),
        $body = $("body");
    var windowWidth = parseInt($popup.css("width").replace("px")),
        windowHeight = parseInt($popup.css("height").replace("px")),
        screenWidth = $body.width(),
        screenHeight = $body.height();
    $popup.css({"left": (screenWidth / 2) - (windowWidth / 2), "top": (screenHeight / 2) - (windowHeight / 2)});
}

function notify(result, title, html, buttons) {
    var buttonsHtml = '<input type="submit" class="button close" value="Закрыть">',
        b = [], bName = [], bHandler = [];
    if(!$("#notify_show").is("div")) {
        if(typeof buttons != "undefined"){
            for(let i = 0; i < buttons.length; i++) {
                b[i] = buttons[i].html;
                bHandler[i] = {name: buttons[i].name, handler: buttons[i].handler};
            }
            buttonsHtml = b.join("");
        }
        $("body").append('<div id="notify_show">' +
            '<div class="wrapper"><div class="message ' + result + '">' +
            '<div class="title">' + title + '</div>' +
            '<p>' + html + '</p>' + buttonsHtml + '</div></div></div>');
    }
    $("#notify_show").css("display", "flex");
    if(bHandler.length === 0) {
        $("#notify_show .close").on("click", function () {
            notify_close();
        });
    }else{
        for(let h = 0; h < bHandler.length; h++){
            let action = bHandler[h].handler;
            $(bHandler[h].name).on("click", function () {
                action();
            });
        }
    }
}

function notify_close(){
    $("#notify_show").fadeOut(300, function () {	// Выставляем таймер
        $("#notify_show").remove(); // Удаляем разметку всплывающего окна
    });
}

var uploadedFiles;

function initForms() {
    $("form.ajaxFrm").on("submit", function (e) {
        e.preventDefault();
        let form = $(this);
        form.find("[type=submit]").addClass("loading");
        form.addClass("disabled");
        setTimeout(function () {
            form.find("button, input, select, textarea").attr("disabled", true).addClass("disabled");
        }, 500);
        var data = new FormData(form[0]);
        if (typeof uploadedFiles != "undefined" && uploadedFiles.length > 0) {
            $.each(uploadedFiles, function (key, value) {
                data.append(key, value);
            });
        }

        data.append("ajax", "1");
        data.append("action", form.attr("id"));

        $.ajax({
            url: '/',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (respond) {

                if (typeof respond.error === 'undefined') {
                    if (respond.result === true) {
                        if (typeof respond.resultText != "undefined") {
                            if(typeof respond.answerTarget !== "undefined"){
                                $(respond.answerTarget).html(respond.resultText)
                            }else {
                                notify("success", "Отлично!", respond.resultText);
                            }
                            if(function_exists(modulePath + "Init")){
                                eval(modulePath + "Init()");
                            }
                            if(!form.hasClass("noreset")) {
                                form.removeClass("disabled").trigger("reset");
                            }
                            form.find(".hide").html("");
                            if (typeof uploadedFiles != "undefined" && uploadedFiles.length > 0) {
                                $("#attachZone .removeUpload").click();
                            }
                        }
                    } else {
                        if (typeof respond.resultText != "undefined") {
                            notify("error", "Ошибка", respond.resultText);
                            if (typeof respond.errorFields != "undefined" && respond.errorFields !== []) {
                                highlightFields(respond.errorFields);
                            }
                        }
                    }
                    form.find("[type=submit]").removeClass("loading");
                    setTimeout(function () {
                        form.find("button, input, select, textarea").attr("disabled", false).removeClass("disabled");
                    }, 500);
                } else {
                    console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error);
                }
            },
            error: function (jqXHR, textStatus) {
                console.log('ОШИБКИ AJAX запроса: ' + textStatus);
            }
        });

        return false;
    });
}

function getCurrentDate() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = "0" + dd
    }

    if (mm < 10) {
        mm = "0" + mm
    }

    return dd + "." + mm + "." + yyyy;
}

function decodeHtml(text) {
    return text
        .replace(/&amp;/g, '&')
        .replace(/&lt;/, '<')
        .replace(/&gt;/, '>')
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'");
}

function htmlspecialchars(str) {
    if (typeof (str) == "string") {
        str = str.replace(/&/g, "&amp;");
        /* must do &amp; first */
        str = str.replace(/"/g, "&quot;");
        str = str.replace(/'/g, "&#039;");
        str = str.replace(/</g, "&lt;");
        str = str.replace(/>/g, "&gt;");
    }
    return str;
}

function strip_tags( str ){
    return str.replace(/<\/?[^>]+>/gi, '');
}

function highlightFields(fieldNameArr) {
    $("input.error, select.error, textarea.error, button.error").removeClass("error");
    for (var i = 0; i < fieldNameArr.length; i++) {
        $("*[name=" + fieldNameArr[i] + "]").addClass("error");
    }
}

function getUrlVar(url) {
    // извлекаем строку из URL или объекта window
    var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

    // объект для хранения параметров
    var obj = {};

    // если есть строка запроса
    if (queryString) {

        // данные после знака # будут опущены
        queryString = queryString.split('#')[0];

        // разделяем параметры
        var arr = queryString.split('&');

        for (var i = 0; i < arr.length; i++) {
            // разделяем параметр на ключ => значение
            var a = arr[i].split('=');

            // обработка данных вида: list[]=thing1&list[]=thing2
            var paramNum = undefined;
            var paramName = a[0].replace(/\[\d*\]/, function (v) {
                paramNum = v.slice(1, -1);
                return '';
            });

            // передача значения параметра ('true' если значение не задано)
            var paramValue = typeof (a[1]) === 'undefined' ? true : a[1];

            // преобразование регистра
            paramName = paramName.toLowerCase();
            paramValue = decodeURI(paramValue.toLowerCase());

            // если ключ параметра уже задан
            if (obj[paramName]) {
                // преобразуем текущее значение в массив
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                // если не задан индекс...
                if (typeof paramNum === 'undefined') {
                    // помещаем значение в конец массива
                    obj[paramName].push(paramValue);
                }
                // если индекс задан...
                else {
                    // размещаем элемент по заданному индексу
                    obj[paramName][paramNum] = paramValue;
                }
            }
            // если параметр не задан, делаем это вручную
            else {
                obj[paramName] = paramValue;
            }
        }
    }

    return obj;
}

function ajaxFunction() {
    var ajaxRequest;  // The variable that makes Ajax possible!

    try {
        // Opera 8.0+, Firefox, Safari (1st attempt)
        ajaxRequest = new XMLHttpRequest();
    } catch (e) {
        // IE browser (2nd attempt)
        try {
            ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                // 3rd attempt
                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                console.log("AJAX не поддерживается");
                return false;
            }
        }
    }
    //console.log("AJAX поддерживается");
    return true;
}

function setCheckedFilter(url) {
    var ch = $("form[name=form_filters] input[type=checkbox], form[name=form_filters] input[type=radio]"),
        getParams = getUrlVar(url);
    for (var c = 0; c < ch.length; c++) {
        var cName = $(ch[c]).attr("name"),
            cValue = $(ch[c]).val();
        if (typeof cName != "undefined") {
            var getName = decodeURI(cName).replace("[]", "%5b%5d");
            var getValues = getParams[getName];
            //console.log(getParams, getName, getValues, Array.isArray(getParams[getName]))
            if (Array.isArray(getValues) &&
                ($.inArray(cValue.toLowerCase(), getValues) != -1 ||
                    $.inArray(cValue, getValues) != -1)) {
                $(ch[c]).prop("checked", true);
            } else if (cValue.toLowerCase() === getValues ||
                cValue === getValues) {
                $(ch[c]).prop("checked", true);
            }
        }
    }
}

function loadContent(url, mode) {
    return $.ajax({
        url: '/',
        type: 'POST',
        data: {
            "ajax": 1,
            "action": "getContent",
            "mode": mode,
            "url": url
        }
    });
}

function setMainContent(url) {
    $.when(loadContent(url, "mainpage")).then(function (answer) {
        document.title = answer.title;
        $(".title_nav h1").text(answer.caption);
        $("main").html(answer.main);
        $("body,html").animate({"scrollTop": 0}, "slow");
        setCheckedFilter(answer.url);
        if(url.indexOf('/lk/') > -1){
            lkInit();
        }
        history.pushState({param: 'Value'}, answer.title, answer.url);
        mainInit();
        if(document.location.pathname.indexOf("/catalog/") > -1
            && document.location.href.indexOf(".html") > -1){
            if(typeof window.DataMetrica.ecommerce === "undefined") {
                window.DataMetrica.push({
                    "ecommerce": {
                        "detail": {
                            "products": [
                                {
                                    "id": parseInt(document.location.href.match(/-(\d+)\.html/)[1]),
                                    "name": $.trim($(".card .text .title").text()),
                                    "price": parseFloat($(".card .price").text().replace(" ", ""))
                                }
                            ]
                        }
                    }
                });
            }else{
                window.DataMetrica.ecommerce.detail.products.push({
                    "id": parseInt(document.location.href.match(/-(\d+)\.html/)[1]),
                    "name": $.trim($(".card .text .title").text()),
                    "price": parseFloat($(".card .price").text().replace(" ", ""))
                })
            }
        }
    });
}

function setPopupContent(url) {
    $.when(loadContent(url, "popup")).then(function (answer) {
        //$("body").css("overflow", "hidden");
        $(".card_up").html(answer.main);
        $(".card_up").css("display", "flex");
        $("#close_info").on("click", function (e) {
            e.preventDefault();
            $(".card_up").html("").css("display", "none");
            //$("body").css("overflow", "auto");
            return false;
        });
        mainInit();
    });
}

function titleInit() {
    $('[title]').tipsy({
        arrowWidth: 10,
        cls: null,
        duration: 150,
        offset: 7,
        position: 'top-center',
        trigger: 'hover',
        onShow: null,
        onHide: null
    });
}

function scrollToObj(objId){
    var pos = $(objId).position().top;
    $("body,html").animate({"scrollTop": pos}, "slow");
}






$(document).ready(function () {
    $("input[type=tel]").mask('+7 (999) 999-99-99');
    if (ajaxFunction()) {
        $.ajaxSetup({
            url: '/',
            type: 'POST',
            dataType: 'json',
            //cache: false,
            beforeSend: function () {
                $("html").css("cursor", "wait");
                //$('.loader').css({"display": "block", "opacity": "0"}).animate({"opacity": "1"}, 1000);
            },
            complete: function () {
                //$('.loader').hide().css("opacity", "0");
                $("html").css("cursor", "default");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Ошибка: ' + textStatus + ' | ' + errorThrown);
            }
        });

        $(".pagination a, .bread a, section .title h4 a").off("click").on("click", function (e) {
            var link = $(this).attr("href");
            if(link != '' && link != '/') {
                e.preventDefault();
                setMainContent($(this).attr("href"));
                return false;
            }
        });

        initForms();
    }

    $("input[type=tel]").mask('+7 (999) 999-99-99');

});
