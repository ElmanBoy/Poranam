
/* pop_up_welcome ******************************************* */
function pop_up_welcome() {
    $("#pop_up_welcome").show().css('display', 'flex');
}

function pop_up_welcome_close() {
    $("#pop_up_welcome").hide();
};
/* pop_up_filter_users ******************************************* */
function pop_up_filter_users() {
    $("#pop_up_filter_users").show().css('display', 'flex');
}

function pop_up_filter_users_close() {
    $("#pop_up_filter_users").hide();
};
/* pop_up_profile ******************************************* */
function pop_up_profile() {
    $("#pop_up_profile").show().css('display', 'flex');
}

function pop_up_profile_close() {
    $("#pop_up_profile").hide();
};
/* pop_up_finance **************************************** */
function pop_up_finance() {
    $("#pop_up_finance").show().css('display', 'flex');
}

function pop_up_finance_close() {
    $("#pop_up_finance").hide();
};
/* pop_up_filter_finance ************************* */
function pop_up_filter_finance() {
    $("#pop_up_filter_finance").show().css('display', 'flex');
}

function pop_up_filter_finance_close() {
    $("#pop_up_filter_finance").hide();
};
/* pop_up_finance_import ************************* */
function pop_up_finance_import() {
    $("#pop_up_finance_import").show().css('display', 'flex');
}

function pop_up_finance_import_close() {
    $("#pop_up_finance_import").hide();
};
/* pop_up_filter_vote ******************************************/
function pop_up_filter_vote() {
    $("#pop_up_filter_vote").show().css('display', 'flex');
}

function pop_up_filter_vote_close() {
    $("#pop_up_filter_vote").hide();
};
/* pop_up_vote *************************************/
function pop_up_vote() {
    $("#pop_up_vote").show().css('display', 'flex');
}

function pop_up_vote_close() {
    $("#pop_up_vote").hide();
};
/* pop_up_filter_init ****************************/
function pop_up_filter_init() {
    $("#pop_up_filter_init").show().css('display', 'flex');
}

function pop_up_filter_init_close() {
    $("#pop_up_filter_init").hide();
};
/* pop_up_initiative **********************************/
function pop_up_initiative() {
    $("#pop_up_initiative").show().css('display', 'flex');
}

function pop_up_initiative_close() {
    $("#pop_up_initiative").hide();
};
/* pop_up_profile_add ************************************/
function pop_up_profile_add() {
    $("#pop_up_profile_add").show().css('display', 'flex');
}

function pop_up_profile_add_close() {
    $("#pop_up_profile_add").hide();
};
/* pop_up_sender *****************************************/
function pop_up_sender() {
    $("#pop_up_sender").show().css('display', 'flex');
}

function pop_up_sender_close() {
    $("#pop_up_sender").hide();
};
/* pop_up_filter_meet *****************************************/
function pop_up_filter_meet() {
    $("#pop_up_filter_meet").show().css('display', 'flex');
}

function pop_up_filter_meet_close() {
    $("#pop_up_filter_meet").hide();
};

/* pop_up_meeting *****************************************/
function pop_up_meeting() {
    $("#pop_up_meeting").show().css('display', 'flex');
}

function pop_up_meeting_close() {
    $("#pop_up_meeting").hide();
};
/* pop_up_meeting_list *****************************************/
function pop_up_meeting_list() {
    $("#pop_up_meeting_list").show().css('display', 'flex');
}

function pop_up_meeting_list_close() {
    $("#pop_up_meeting_list").hide();
};
/*
- pop_up_welcome -/ вход-выход +
- pop_up_filter_vote -/ фильтр голосований +
- pop_up_filter_users -/ фильтр пользователей +
- pop_up_filter_init -/ фильтр  инициатив +
- pop_up_filter_meet -/ фильтр  мероприятий +
- pop_up_filter_finance -/ фильтр  финансов +
- pop_up_profile -/ карточка юзера +
- pop_up_profile_add -/ добавление юзера +
- pop_up_vote -/ настройка голосования +
- pop_up_initiative -/ настройка инициативы +
- pop_up_meeting -/ настройка (создание) мероприятия +
pop_up_meeting_list -/ список участников +
- pop_up_sender -/ рассылка +
- pop_up_finance -/ добавление и редактирование операций +
- pop_up_finance_mport -/ импорт фин. операций +
*/

function closePopup(){
    $(document).off("click").on("click", function(e){
        if (!$(".pop_up, .panel").is(e.target) && $(".pop_up, .panel").has(e.target).length === 0) {
            $(".pop_up, .panel").hide();
            setTimeout('$(document).off("click")', 100);
        }
    })
}

function bindDelButton(){
    $(".delButton").off("click").on("click", function(){
        let action = $(this).closest("form").attr("id").replace("set", "del"),
            id = $(this).data("id"),
            that = $(this);
        $.post("/", {ajax: 1, action: action, id: id}, function(data){
            //let answer = JSON.parse(data);
            that.closest("form").html(data.resultText);
        })
    });
}

function settingsInit(){
    bindDelButton();
}

$(document).ready(function(){
    $(".account button").on("click", function(){
        $("#pop_up_welcome").css('display', 'flex', );
        setTimeout('closePopup()', 100);
    });

    $("#gen_pass").on("click", function(e){
        e.preventDefault();
        $("#metka_1g").attr("type", "text").val(el_tools.genPass(10));
    });

    $("#copy_link").on("click", function(e){
        e.preventDefault();

        $("#metka_1s").select();
        document.execCommand("copy");
        alert("Ссалка скопирована в буфер обмена.");
    });

    $("#metka_1p").on("input", function(){
        $("#metka_1f").val(el_tools.strip_tags($(this).val()));
    });

    $("#logout").on("click", function(){
        document.location.href = "?logout";
    });

    $("#lk").on("click", function(){
        document.location.href = "/lichnyy-kabinet/profile/";
    });

    $("input[type=tel]").mask("+7 (999) 999-99-99");

    $("select[name=region]").on("el_select_change", function(){
        $.post("/", {ajax: 1, action: "getRegion", subject: $(this).val()}, function(data){
            $("select[name=district]").html(data);
        });
    });

    $(".deleteProfile").on("click", async function(e){
        e.preventDefault();
        let ok = await confirm("<strong>Вы уверены?</strong><br>Ваш профиль будет удалён безвозвратно.");
        if(ok){
            $.post("/", {ajax: 1, action: "deleteProfile"}, function (data) {
                let answer = JSON.parse(data);
                alert(answer.resultText);
            });
        }
    })

    $("form#registration input:required, form#registration select:required").on("change el_select_change", function(){
        let fields = $("form#registration input:required, form#registration select:required"),
            count = fields.length,
            emptyFields = 0;

        for(let i = 0; i < count; i++){
            if($(fields[i]).attr("type") === "checkbox"){
                if(!$(fields[i]).prop("checked")){
                    emptyFields++;
                }
            }else{
                if($.trim($(fields[i]).val()).length === 0){
                    emptyFields++;
                }
            }
        }
        $(this).closest("form").find("[type=submit]").attr("disabled", (emptyFields > 0));
    });

    bindDelButton();
});