var timer_connected = "", loader_active = true, el_app = {

        loader_show: function () {
            $(".item.logo").addClass("logo_animate");
            $("html").css("cursor", "wait");
            //$("body").append("<div id='loader_wrap'></div>");
        },

        loader_hide: function () {

            $("html").css("cursor", "default");
            setTimeout(function () {
                $(".item.logo").removeClass("logo_animate");
            }, 1500);
            //$("#loader_wrap").remove();
        },


        loadContent: function (url, mode, params) {
            return $.ajax({
                url: '/',
                type: 'POST',
                data: {
                    "ajax": 1,
                    "action": "getContent",
                    "mode": mode,
                    "url": url,
                    "params": params
                }
            });
        },

        setMainContent: function (url, params) {
            $.when(el_app.loadContent(url, "mainpage", params)).then(function (answer) {
                let regexp = /<div class="title">([^<]+)<\/div>/g,
                    title = regexp.exec(answer),
                    queryString = "";
                if (typeof params !== "undefined" && params.length > 0) {
                    queryString = "?" + params;
                }
                document.title = title[1];
                $(".main_data").html(answer);
                $("body,html").animate({"scrollTop": 0}, "slow");
                history.pushState({param: 'Value'}, answer.title, url + queryString);
                el_app.mainInit();
            });
        },

        setPopupContent: function (url) {
            $.when(el_app.loadContent(url, "popup")).then(function (answer) {
                //$("body").css("overflow", "hidden");
                $(".card_up").html(answer.main).css("display", "flex");
                $("#close_info").on("click", function (e) {
                    e.preventDefault();
                    $(".card_up").html("").css("display", "none");
                    //$("body").css("overflow", "auto");
                    return false;
                });
                el_app.mainInit();
            });
        },

        dialog_open: function (dialog_id, params) {
            $.when(el_app.loadContent(dialog_id, "popup", params)).then(function (content) {
                if (!$("#" + dialog_id).is("div")) {
                    $("body").append("<div class='wrap_pop_up' id='" + dialog_id + "'></div>");
                }
                let $dialog = $("#" + dialog_id);

                $dialog.html(content).css("display", "flex");
                el_app.mainInit();
                $("#" + dialog_id + " .close, #" + dialog_id + " .close_button").on("click", function () {
                    el_app.dialog_close(dialog_id);
                });

                $("#" + dialog_id + " input:first").focus();

                $dialog.off("keydown").on("keydown", function (e) {
                    switch (e.which) {
                        //Клавиша Enter
                        case 13:
                            $("#" + dialog_id + " .confirm button:first").click();
                            break;
                        //Клавиша Escape
                        case 27:
                            el_app.dialog_close(dialog_id);
                            break;
                    }
                });
            });
        },

        dialog_close: function (dialog_id) {
            $("#" + dialog_id).remove();
        },

        mainInit: function () {

            $("#logout").on("click", function () {
                document.location.href = "?logout";
            });
            $("select:not(.flatpickr-monthDropdown-months)").el_select();


            if($("[type=date]").parents("#initiative_filter").is("div")
                || $("[type=date]").parents("#votes_filter").is("div")
                || $("[type=date]").parents("#meetings_filter").is("div")){
                $("#meetings_filter input[type=date], #votes_filter input[type=date], #initiative_filter input[type=date]").flatpickr({
                    locale: 'ru',
                    //mode: 'range',
                    //inline: true,
                    time_24hr: true,
                    dateFormat: 'Y-m-d',
                    altFormat: 'd.m.Y',
                    conjunction: '-',
                    altInput: true,
                    altInputClass: "el_input",
                    firstDayOfWeek: 1,
                });
            }else {
                $("[type=date]").flatpickr({
                    locale: 'ru',
                    //mode: 'range',
                    //inline: true,
                    time_24hr: true,
                    dateFormat: 'Y-m-d',
                    altFormat: 'd.m.Y',
                    conjunction: '-',
                    altInput: true,
                    altInputClass: "el_input",
                    allowInvalidPreload: true,
                    firstDayOfWeek: 1,
                    minDate: "today"
                });
            }


            el_tools.initForms();

            $("input[type=tel]").mask('+7 (999) 999-99-99');


            //Клик по чекбоксу в строке таблицы
            $(".custom_checkbox input").on("change", function () {
                if ($(this).prop("checked")) {
                    $(this).closest("tr").addClass("selected");
                } else {
                    $(this).closest("tr").removeClass("selected");
                }
                //Если отмечен один и более чекбоксов снимаем атрибут disabled
                // у иконок групповых действий вверху
                $(".group_action").attr("disabled", ($("tr td:first-child .custom_checkbox input:checked").length === 0));
            });

            /*$(".drag").draggable(({
                handle: '.handle'
            }));

            $('[title]').tipsy({
                arrowWidth: 10,
                cls: null,
                duration: 150,
                offset: 16,
                position: 'right',
                onShow: null,
                onHide: null
            });

            $(".pagination a").off("click").on("click", function (e) {
                e.preventDefault();
                let link = $(this).attr("href"),
                    linkArr = link.split("?");
                $(".pagination .page").removeClass("current");
                $(this).closest(".page").addClass("current");
                el_app.setMainContent(linkArr[0], linkArr[1]);
                return false;
            });*/
        }
    }




$(document).ready(function () {
    el_tools.initForms();
    el_app.mainInit();


    $('a[href="#"]').on('click', async function (e) {
        e.preventDefault();
        await alert('Раздел еще не создан');
    });

    el_tools.controlPlusKey('N', function () {
        $("#button_nav_create").click();
    });

    el_tools.controlPlusKey('C', function () {
        $("#button_nav_clone").click();
    });

    $(document).on("keydown", function (e) {
        if (e.which === 46) {
            e.preventDefault();
            $("#button_nav_delete").click();
        }
    });

    if (el_tools.ajaxFunction()) {
        $.ajaxSetup({
            url: '/',
            type: 'POST',
            //dataType: 'json',
            cache: false,
            headers: {
                'X-Csrf-Token': el_tools.getcookie("CSRF-TOKEN")
            },
            beforeSend: function () {
                if (loader_active)
                    el_app.loader_show();
            },
            complete: function () {
                if (loader_active)
                    el_app.loader_hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (textStatus === "timeout") {
                    alert("Утеряно подключение к серверу!");
                }
                console.log('Ошибка: ' + textStatus + ' | ' + errorThrown);
            }
        });

        $(".main_nav a").off("click").on("click", async function (e) {
            let link = $(this).attr("href");
            if (link !== '' && link !== '/' && link !== '#') {
                e.preventDefault();
                $(".main_nav a").removeClass("active");
                $(this).addClass("active");
                el_app.setMainContent(link);
                return false;
            } else {
                await alert('Раздел ещё не создан');
            }
        });

        window.addEventListener('popstate', function (e) {
            var url = document.location.href.replace(document.location.protocol + '//' + document.location.host, ""),
                query = document.location.search,
                params = undefined,
                urlArr = url.split("?");
            if (query.length > 0) {
                params = query;
            }
            //el_app.setMainContent(urlArr[0], urlArr[1]);
            $.when(el_app.loadContent(urlArr[0], "mainpage", urlArr[1])).then(function (answer) {
                let regexp = /<div class="title">([^<]+)<\/div>/g,
                    title = regexp.exec(answer);
                document.title = "OHANA SOFT - " + title[1];
                $(".main_data").html(answer);
                $("body,html").animate({"scrollTop": 0}, "slow");
                el_app.mainInit();
            })
        });


    }
})

$(window).on("beforeunload", function () {
    el_app.loader_show();
    setTimeout('', 1000);
})