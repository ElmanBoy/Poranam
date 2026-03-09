var orderData = {}, nologin = 0;

var imlcallback = function (pvz) {
    $("#pvz_info").html("Выбран постамат по адресу: " + pvz
        .address);
    $("[name=pvz_info]").val(pvz.ID);
    console.log(pvz);
}

function iniLogisticMap() {
    var options = {
        isselectpvz: 1,
        isc2cpage: 0,
        shwgetmapcode: 0,
        copydesc: 1,
        showfitroom: 0,
        shwdelivery: 0,
        city: $.trim($("[name=city]").val().replace("г", ""))/*,
        region: $.trim($("[name=region]").val().replace("г", ""))*/
    },
    wurl = 'https://iml.ru',
    $mapContainer = $('#map-container');

    if($("[name=logist]:checked").val() === "2"){
        options["sdtype"] = 1;
    }
    $mapContainer.html("");
    window.iml_map.init('100%', '500px', options, imlcallback, wurl);
    $('iframe[src^="' + wurl + '"]').appendTo($mapContainer);
}

function calcTotalPrice() {
    if ($("#cartSumm").is("div")) {
        var cartSumm = parseInt($("#cartSumm").html().replace(" ", "").replace("&nbsp;", "")),
            delPrice = parseInt($("#deliveryPrice").html().replace(" ", "").replace("&nbsp;", "")) || 0;

        $("#totalSumm").text(new Intl.NumberFormat('ru-RU').format(cartSumm + delPrice));
    }
}

var token = "eb83a00ad060d6cca3d2341f2acb15cdb76b67df";

function geolocateCity($city) {
    var citySgt = $city.suggestions();
    if (typeof citySgt != "undefined") {
        citySgt.getGeoLocation().done(
            function (locationData) {
                if (locationData.city) {
                    var suggestionVal = locationData.city_type + " " + locationData.city,
                        suggestion = {value: suggestionVal, data: locationData};
                    citySgt.setSuggestion(suggestion);
                } else if (locationData.region) {
                    var suggestionVal = locationData.region_type + " " + locationData.region,
                        suggestion = {value: suggestionVal, data: locationData};
                    citySgt.setSuggestion(suggestion);
                }
            });
    }
}

function showPostalCode(suggestion) {
    $("[name=index]").val(suggestion.data.postal_code);
}

function clearPostalCode() {
    $("[name=index]").val("");
}

function orderComplete(){
    orderData.action = "order";
    $.post("/", orderData, function (data) {
        if(typeof data == "string") var data = JSON.parse(data);
        if (data.result) {
            $("form[name=form_order]").html(data.resultText);
            $(".order_summary .title").text("О заказе № " + data.orderNumber);
            $("#cartCount").text(data.goodCount);
            $("#cartSumm").html(data.orderSumm);
            $("#deliveryPrice").html(data.deliveryPrice);
            $("#totalSumm").html(data.totalSumm);

            $("#order_print").on("click", function (e) {
                e.preventDefault();
                window.print();
                return false;
            });
            $("#continue_shopping").on("click", function (e) {
                e.preventDefault();
                document.location.href = "/";
                return false;
            });
        } else {
            notify("error", "Ошибка", data.resultText);
        }
    });
}

var type = "ADDRESS",
    orderData = {};
$(document).ready(function () {

    $("[name=logist]").on("change", function () {
        var cartSumm = parseInt($("#cartSumm").html().replace(" ", "").replace("&nbsp;", ""))
        var delivId = $(this).val(),
            delDesc = $(this).parents("label").find(".logist_desc").html(),
            delDate = $(this).parents("label").find(".date_logist").text().replace("Дата:", "");
        $("#deliveryPrice").text(cartSumm >= 2000 ? 0 : logist[delivId]);
        if (typeof delDesc != "undefined")
            $("#deliveryType").html(delDate + ", " + delDesc);
        if (parseInt(delivId) === 1) {
            $("#delivery_point").hide();
            $("#delivery_address").show();
            scrollToObj("#delivery_address");
        } else {
            $("#delivery_point").show();
            $("#delivery_address").hide();
            scrollToObj("#delivery_point");
        }
        //iniLogisticMap();
        calcTotalPrice();
    });
    $("input[type=tel]").mask('+7 (999) 999-99-99');

    var $region = $("[name=region]");
//var $area   = $("#area");
    var $city = $("[name=city]");
//var $settlement = $("#settlement");
    var $street = $("[name=street]");
    var $house = $("[name=house]");
    var cartProducts = [];

// регион
    $region.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "region"
    });


// город
    $city.suggestions({
        token: token,
        type: type,
        hint: true,
        bounds: "city",
        constraints: $region,
        onSelect: function (){
            $("#delivery_type").show();
            scrollToObj("#delivery_type");
            iniLogisticMap()
        },
    });

    geolocateCity($city);

// улица
    $street.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "street",
        constraints: $city
    });

// дом
    $house.suggestions({
        token: token,
        type: type,
        hint: false,
        noSuggestionsHint: false,
        bounds: "house",
        constraints: $street,
        onSelect: showPostalCode,
        onSelectNothing: clearPostalCode
    });

    calcTotalPrice();

    $("[name=confirm_reg_policy]").on("change", function () {
        var $submit_order = $("#submit_order");
        $submit_order.attr("disabled", !$(this).prop("checked"));
        if ($(this).prop("checked")) {
            $submit_order.removeClass("disable");
        } else {
            $submit_order.addClass("disable");
        }
    });

    // Инициализация виджета
    if($("#saferoute-cart-widget").is("div")) {
        $.when(updateCart("view_full", 0, 0)).then(function (data) {
            let widgetOptions = {
                apiScript: "/modules/saferoute/saferoute-widget-api.php",
                disableMultiRequests: true,
                currency: 'rub'
            };
            cartProducts = data.products;
            widgetOptions['products'] = data.products;
            widgetOptions['weight'] = data.weight;
            if (data.user !== false) {
                var userInfo = data.user,
                    userOptions = ['userFullName', 'userPhone', 'userEmail', 'regionName', 'userAddressStreet',
                        'userAddressBuilding', 'userAddressBulk', 'userAddressApartment', 'nppOption'];
                for (var i = 0; i < userOptions.length; i++) {
                    widgetOptions[userOptions[i]] = userInfo[userOptions[i]];
                }
            }
            //console.log(data, widgetOptions);
            var widget = new SafeRouteCartWidget("saferoute-cart-widget", widgetOptions);
            widget.on("start", function (data) {
                $("#saferoute-cart-widget iframe").contents().find(".top-panel .logo").hide();
            })

            // Обработчики событий
            widget.on("change", function (data) {
                let user_email = data.contacts.email;
                if (user_email !== null) {
                    //let nologin = orderData.nologin == 1 ? 1 : 0;
                    $.post("/", {ajax: 1, action: "findUser", mail: data.contacts.email, nologin: nologin}, function (data) {
                        if (data.exist) {
                            notify("success", "Внимание!", "Пользователь с таким E-mail уже зарегистрирован" +
                                " в нашей базе данных.<br>Если это ваш E-mail, пожалуйста, авторизуйтесь.",
                                [{
                                        html: "<input type=\"button\" id='nologin_continue' class=\"button close\" value=\"Не хочу\">",
                                        name: "#nologin_continue",
                                        handler: function(){
                                            nologin = 1;
                                            notify_close(); }
                                    },
                                    {
                                        html: "<input type=\"button\" id='login_start' class=\"button close\" value=\"Войти\">",
                                        name: "#login_start",
                                        handler: function(){
                                            notify_close();
                                            nologin = 0;
                                            $("#login_popup, .login_up").show();
                                            $("#login_email").val(user_email);
                                            scrollToObj(".icon.log_in");
                                    }
                                }]);
                        }
                    });
                }
                if (data.city != null) {
                    orderData = {
                        ajax: 1,
                        action: "order",
                        name: data.contacts.fullName,
                        mail: data.contacts.email,
                        phone: data.contacts.phone,
                        city: data.city.name,
                        index: data.contacts.address.zipCode,
                        street: data.contacts.address.street,
                        house: data.contacts.address.building + (data.contacts.address.bulk ? " корпус " + data.contacts.address.bulk : ""),
                        flat: data.contacts.address.apartment,
                        comment: data.comment
                    }
                    if (data.delivery !== null) {
                        orderData.logist = data.delivery.deliveryCompanyId;
                        orderData.logistName = data.delivery.deliveryCompanyName;
                        orderData.logistPrice = data.delivery.totalPrice;
                        orderData.logistAddress = data._meta.commonDeliveryData;
                        orderData.logistType = data.delivery.type;
                        calcTotalPrice();
                    }
                    if (data.contacts.companyName != null) {
                        orderData.orgName = data.contacts.companyName;
                        orderData.orgINN = data.contacts.companyTIN;
                    }
                }
                if (data.delivery != null) {
                    $("#deliveryPrice").text(data.delivery.totalPrice);
                }
                //console.log(data);
            });
            widget.on("done", function (response) {
                // Вызовется при успешном оформлении заказа
                // и получении ответа от сервера SafeRoute
                $("#saferoute-cart-widget iframe").contents().find(".done").css("background-image", "");
                orderData.logistOrderId = response.id;
                //console.log(response);
                if (response.id) {
                    $("#payment_type").show();
                    $("#payment_type input[type=radio]").change();
                    scrollToObj("#payment_type");
                }
            });
            widget.on("error", function (errors) {
                // Вызовется при возникновении ошибок при обработке запроса,
                // при передаче в виджет некорректных или неполных данных
                console.log(errors);
            });
        });
    }

    $("#pay_online").on("click", function(e){
        e.preventDefault();
        $("#invoicebox_form").trigger("submit");
        return false;
    })

    $("#payment_type input[type=radio]").on("change", function(){
        var $selectedInput = $("#payment_type input[type=radio]:checked");
        var selectedVal = $selectedInput.val();
        var selectedText = $selectedInput.parents(".radiocontainer").find(".payName").text();
        if(selectedVal === '2'){
            $("#pay_online").show();
            $("#order_confirm").hide();
            orderData.action = "preorder";
            orderData.payment = 2;

            $.post("/", orderData, function(data){
                $("#invoicebox_form_wrap").html(data.resultText);
            });
        }else {
            $("#pay_online").hide();
            $("#order_confirm").show();
            orderData.payment = selectedVal;
            scrollToObj("#order_confirm");
        }
        $("#paymentType").text(selectedText);
    });

    $("#submit_order").on("click", function(){
        orderComplete();
    })
});