let users = {
    buttons_init: function(){

        $("#check_all").on("click", function () {
            $("tr td:first-child .custom_checkbox input").prop("checked", $(this).prop("checked"));
        });

        $("#button_user_filter").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_filter', el_tools.getUrlVar());
        });
        $("#button_user_new").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_new');
        });
        $("#button_user_mailing").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_mailing');
        });

        $(".user_remove").off("click").on("click", function(){
            return users.setUserStatus($(this).data("id"), 0);
        });

        $("select[name=userStatus]").on("el_select_change", function(){
            if($(this).val().length > 0){
                let userId = $(this).closest("td").attr("id").replace("uid", "");

                $.post("/", {ajax: 1, action: "changeUserStatus", user: userId, status: $(this).val()}, function(data){
                    let answer = JSON.parse(data);

                    if(answer.result) {
                        el_tools.notify(answer.result, "Результат", answer.resultText);
                    }
                })
            }
        });
    },

    setUserStatus: async function(id, status){
        let ok = await confirm("Вы уверены?");
        if(ok){
            $.post("/", {ajax: 1, action: "setUserStatus", value: status, id: id}, function (data) {
                let answer = JSON.parse(data);
                if(answer.result) {
                    initiatives.userListUpdate();
                }
                alert(answer.resultText);
            });
        }
    },

    userListUpdate: function(){
        $.post("/", {ajax: 1, action: "getUsers"}, function(data){
            $("#users_table").html(data);
            initiatives.buttons_init();
        });
    },

    popupNewInit: function(){
        $("select[name=region]").on("el_select_change", function(){
            if($(this).val().length === 1){
                $.post("/", {ajax: 1, action: "getRegion", subject: $(this).val()}, function (data) {
                    $("select[name=district]").html(data);
                    $(".detail").show();
                });
            }else {
                $(".detail").hide();
            }
        });

        $("#init_select_all").on("change", function(){
            let elems = $(".subject, .prof");
            if($(this).prop("checked")){
                elems.hide();
                $(".detail").hide();
            }else{
                elems.show();
                if($("select[name=region]").val().length === 1){
                    $(".detail").show();
                }
            }
        });

        $("#user_filter button[type=reset]").off("click").on("click", function(){
            document.location.href = document.location.pathname;
        });

        initiatives.cloneAnswer();

        $("#upost_index").off("input keyup").on("input keyup", function(){
            if($(this).val().replace(/_/g, "").length >= 5){
                $.post("/", {ajax: 1, action: "getGroups", index: $(this).val()}, function(data){
                    let answer = JSON.parse(data);

                    if(answer.result) {
                        $("#groups").html(answer.resultText).closest(".item").show();
                    }
                })
            }
        }).mask('999999');
    }
}

$(document).ready(function(){
    users.buttons_init();
});