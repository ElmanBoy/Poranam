window.alert = (message) => {
    var PromiseAlert = el_tools.notify(true, 'Внимание!', message);
    return new Promise(function (resolve, reject) {
        PromiseAlert.on('alert_close', resolve);
    });
};

window.confirm = (question) => {
    let confirm = true;
    let PromiseConfirm = el_tools.notify(true, "Подтвердите действие", question,
        [
            {
                html: '<button class="button icon text close_button success" type="button">' +
                    '<span class="material-icons">done</span>Да</button>',
                name: ".close_button",
                handler: function () {
                    $("#notify_show").trigger("alert_confirmed");
                    el_tools.notify_close();
                    confirm = true;
                }
            },
            {
                html: '<button class="button icon text close_button fail" type="button">' +
                    '<span class="material-icons">block</span>Нет</button>',
                name: ".close_button",
                handler: function () {
                    $("#notify_show").trigger("alert_reject");
                    el_tools.notify_close();
                    confirm = false;
                }
            }]);

    $("#notify_show .success").on('click', e => {
        confirm = true;
    });
    $("#notify_show .fail, #notify_show .close").on('click', e => {
        confirm = false;
    });
    return new Promise(function (resolve, reject) {
        PromiseConfirm.on('alert_close', (e) => {
            resolve(confirm);
        });
    });
};

const el_tools = {
    uploadedFiles: "",

    setcookie: function (name, value, expires, path, domain, secure) {
        document.cookie = name + "=" + escape(value) +
            ((expires) ? "; expires=" + (new Date(expires)) : "") +
            ((path) ? "; path=/" : "; path=/") +
            ((domain) ? "; domain=" + domain : "") +
            ((secure) ? "; secure" : "");
    },


    /**
     * Получить значение куки по имени name
     *
     */
    getcookie: function (name) {
        let cookie = " " + document.cookie;
        let search = " " + name + "=";
        let setStr = null;
        let offset = 0;
        let end = 0;

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
    },

    array_unique: function (a) {
        //let a = this.concat();
        for (let i = 0; i < a.length; ++i) {
            for (let j = i + 1; j < a.length; ++j) {
                if (a[i] === a[j])
                    a.splice(j--, 1);
            }
        }
        return a;
    },

    in_array: function (needle, haystack, strict) {
        let found = false, key, stricted = !!strict;
        for (key in haystack) {
            if (haystack.hasOwnProperty(key)) {
                if ((stricted && haystack[key] === needle) || (!stricted && haystack[key] === needle)) {
                    found = true;
                    break;
                }
            }
        }

        return found;
    },

    array_clean: function (a) {
        let out = [];
        for (let i = 0; i < a.length; ++i) {
            if (a[i].length > 0) {
                out.push(a[i]);
            }
        }
        return out;
    },

    arrayItemCompare: function (field, order) {
        let len = arguments.length;
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
        let fields, orders;
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
    },

    el_postfix: function (number, one, two, five) {
        number = typeof number != "undefined" ? parseInt(number) : 0;
        let out = '';
        if (number > 20) {
            let numArr = String(number);
            number = numArr[numArr.length - 1];
            out = el_tools.el_postfix(number, one, two, five);
        } else if (number === 1) {
            out = one;
        } else if (number > 1 && number < 5) {
            out = two;
        } else if (number >= 5 || number === 0) {
            out = five;
        }
        return out;
    },

    floatRound: function (num, presicion) {
        decimals = (presicion !== undefined) ? presicion : 1;
        return Number(Math.round(num + 'e' + decimals) + 'e-' + decimals);

    },

    addExportFrame: function (path) {
        let $ef = $("#export_frame");
        if (!$ef.is("iframe")) {
            $("body").after("<iframe id='export_frame' width='0' height='0' frameborder='0'></iframe>");
        }
        $ef.attr("src", path);
    },

    // использование Math.round() даст неравномерное распределение!
    getRandomInt: function (min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },

    function_exists: function (function_name) {
        if (typeof function_name == 'string') {
            return (typeof window[function_name] == 'function');
        } else {
            return (function_name instanceof Function);
        }
    },

    genPass: function (maxChars) {
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (let i = 0; i < maxChars; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    },

    copyStringToClipboard:  function(str) {
        let el = document.createElement('textarea');
        el.value = str;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    },

    copyToClipboard: function (fieldId) {
        $(fieldId).focus().select();
        return document.execCommand('copy');
    },

    notify: function (result, title, html, buttons) {
        var buttonsHtml = '<button class="button icon text close_button success" type="submit">' +
            '<span class="material-icons">done</span>OK</button>',
            resultClass = result ? 'alert' : 'error',
            b = [], bName = [], bHandler = [];
        if (!$("#notify_show").is("div")) {
            if (typeof buttons != "undefined") {
                for (let i = 0; i < buttons.length; i++) {
                    b[i] = buttons[i].html;
                    bHandler[i] = {name: buttons[i].name, handler: buttons[i].handler};
                }
                buttonsHtml = b.join("");
            }
            $("body").append('<div id="notify_show" class="wrap_pop_up">' +
                '<div class="wrapper"><div class="message ' + resultClass + '">' +
                '<div class="title">' + title + '</div>' +
                '<p>' + html + '</p>' + buttonsHtml + '</div></div></div>');
            /*$("#notify_show .drag").draggable(({
                handle: '.handle'
            }));*/
        }
        $("#notify_show").css("display", "flex");
        $("#notify_show .close, #notify_show .close_button").on("click", function () {
            el_tools.notify_close();
        });

        $("#notify_show .close_button.success").focus();

        $("#notify_show").off("keydown").on("keydown", function (e) {
            switch (e.which) {
                //Клавиша Enter
                case 13:
                    $("#notify_show .close_button.success").click();
                    break;
                //Клавиша Escape
                case 27:
                    $("#notify_show .close_button.fail").click();
                    break;
            }
        });

        if (bHandler.length > 0) {
            for (let h = 0; h < bHandler.length; h++) {
                let action = bHandler[h].handler;
                $(bHandler[h].name).on("click", function () {
                    action();
                });
            }
        }
        $("#notify_show").trigger("alert_open");
        return $("#notify_show");
    },

    notify_close: function () {
        $("#notify_show").fadeOut(300, function () {	// Выставляем таймер
            $("#notify_show").trigger("alert_close").remove(); // Удаляем разметку всплывающего окна
            //$(".wrap_pop_up").hide();
        });
    },

    initForms: function () {
        $("form.ajaxFrm").off("submit").on("submit", function (e) {
            e.preventDefault();
            let form = $(this);
            form.find("[type=submit]").addClass("loading");
            form.addClass("disabled");
            setTimeout(function () {
                form.find("button, input, select, textarea").attr("disabled", true).addClass("disabled");
            }, 500);
            let data = new FormData(form[0]);
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
                                el_tools.notify("success", "Отлично!", respond.resultText);
                                if (!form.hasClass("noreset")) {
                                    form.removeClass("disabled").trigger("reset");
                                }
                                form.find(".hide").html("");
                                if (typeof uploadedFiles != "undefined" && uploadedFiles.length > 0) {
                                    $("#attachZone .removeUpload").click();
                                }
                            }
                        } else {
                            if (typeof respond.resultText != "undefined") {
                                el_tools.notify("error", "Ошибка", respond.resultText);
                                if (typeof respond.errorFields != "undefined" && respond.errorFields !== []) {
                                    el_tools.highlightFields(respond.errorFields);
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
    },

    getCurrentDate: function () {
        let today = new Date();
        let dd = today.getDate();
        let mm = today.getMonth() + 1; //January is 0!
        let yyyy = today.getFullYear();

        if (dd < 10) {
            dd = "0" + dd
        }

        if (mm < 10) {
            mm = "0" + mm
        }

        return dd + "." + mm + "." + yyyy;
    },

    decodeHtml: function (text) {
        return text
            .replace(/&amp;/g, '&')
            .replace(/&lt;/, '<')
            .replace(/&gt;/, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#039;/g, "'");
    },

    htmlspecialchars: function (str) {
        if (typeof (str) == "string") {
            str = str.replace(/&/g, "&amp;");
            /* must do &amp; first */
            str = str.replace(/"/g, "&quot;");
            str = str.replace(/'/g, "&#039;");
            str = str.replace(/</g, "&lt;");
            str = str.replace(/>/g, "&gt;");
        }
        return str;
    },

    strip_tags: function (str) {
        return str.replace(/<\/?[^>]+>/gi, '');
    },

    translit: function(word){
        let answer = "", a = {};

        a["Ё"]="YO";a["Й"]="I";a["Ц"]="TS";a["У"]="U";a["К"]="K";a["Е"]="E";a["Н"]="N";a["Г"]="G";a["Ш"]="SH";
        a["Щ"]="SCH";a["З"]="Z";a["Х"]="H";a["Ъ"]="'";a["ё"]="yo";a["й"]="i";a["ц"]="ts";a["у"]="u";a["к"]="k";
        a["е"]="e";a["н"]="n";a["г"]="g";a["ш"]="sh";a["щ"]="sch";a["з"]="z";a["х"]="h";a["ъ"]="'";
        a["Ф"]="F";a["Ы"]="I";a["В"]="V";a["А"]="a";a["П"]="P";a["Р"]="R";a["О"]="O";a["Л"]="L";a["Д"]="D";
        a["Ж"]="ZH";a["Э"]="E";a["ф"]="f";a["ы"]="i";a["в"]="v";a["а"]="a";a["п"]="p";a["р"]="r";a["о"]="o";
        a["л"]="l";a["д"]="d";a["ж"]="zh";a["э"]="e";a["Я"]="Ya";a["Ч"]="CH";a["С"]="S";a["М"]="M";a["И"]="I";
        a["Т"]="T";a["Ь"]="'";a["Б"]="B";a["Ю"]="YU";a["я"]="ya";a["ч"]="ch";a["с"]="s";a["м"]="m";a["и"]="i";
        a["т"]="t";a["ь"]="'";a["б"]="b";a["ю"]="yu";a[" "]="-";a[","]="-";a["?"]="";a["!"]="";a["."]="-";

        for (i = 0; i < word.length; ++i){
            answer += a[word[i]] === undefined ? word[i] : a[word[i]];
        }
        return answer;
    },

    highlightFields: function (fieldNameArr) {
        $("input.error, select.error, textarea.error, button.error").removeClass("error");
        for (let i = 0; i < fieldNameArr.length; i++) {
            $("*[name=" + fieldNameArr[i] + "]").addClass("error");
        }
    },

    getUrlVar: function (url) {
        // извлекаем строку из URL или объекта window
        let queryString = url ? url.split('?')[1] : window.location.search.slice(1);

        // объект для хранения параметров
        let obj = {};

        // если есть строка запроса
        if (queryString) {

            // данные после знака # будут опущены
            queryString = queryString.split('#')[0];

            // разделяем параметры
            let arr = queryString.split('&');

            for (let i = 0; i < arr.length; i++) {
                // разделяем параметр на ключ => значение
                let a = arr[i].split('=');

                // обработка данных вида: list[]=thing1&list[]=thing2
                let paramNum = undefined;
                let paramName = a[0].replace(/\[\d*\]/, function (v) {
                    paramNum = v.slice(1, -1);
                    return '';
                });

                // передача значения параметра ('true' если значение не задано)
                let paramValue = typeof (a[1]) === 'undefined' ? true : a[1];

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
    },

    controlPlusKey: function (targetKey, callback) {
        // добавляем событие нажатия клавиш на документ
        $(document).on('keydown', function(event) {
            //console.log(event.which)
            // если нажаты клавишы Cntrl и targetKey
            if ((event.ctrlKey || event.metaKey) && event.which === targetKey.charCodeAt(0)) {
                // запрещаем действие по умолчанию
                event.preventDefault();
                // вызываем наше действие
                callback();
            }
        });
    },

    el_calcPercent: function (value, total){
        return (parseInt(total) > 0) ? el_tools.floatRound((100 / total) * value, 1) : 0;
    },

    ajaxFunction: async function () {
        let ajaxRequest;  // The variable that makes Ajax possible!

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
                    await alert("AJAX не поддерживается, работа приложения невозможна. Обновите или смените браузер.");
                    return false;
                }
            }
        }
        //console.log("AJAX поддерживается");
        return true;
    },

    scrollToObj: function (objId) {
        let pos = $(objId).position().top;
        $("body,html").animate({"scrollTop": pos}, "slow");
    }
}
