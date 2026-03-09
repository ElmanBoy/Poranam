let users = {
    buttons_init: function(){

        $("#check_all").on("change", function () {
            let $self = $(this);
            $.post("/", {ajax: 1, action: "mark_user", id: 0, state: $self.prop("checked")});
            $("tr td:first-child .custom_checkbox input").prop("checked", $(this).prop("checked"));
        });

        $(".table_data [type=checkbox]:not(#check_all)").on("change", function(){
            let $self = $(this);
            $.post("/", {ajax: 1, action: "mark_user", id: $self.val(), state: $self.prop("checked")});
            if($(this).val() !== 0) {
                $("#check_all").prop("checked", false);
            }
        });

        $("#button_user_filter").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_filter', el_tools.getUrlVar());
        });
        $("#button_user_new").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_new');
        });

        $(".user_profile_link").off("click").on("click", function(){
            el_app.dialog_open('user_new', $(this).data("id"));
        });

        $("#button_user_mailing").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_mailing', document.location.search);
        });

        $(".user_remove").off("click").on("click", function(e){
            e.preventDefault();
            let checked = $(".custom_checkbox input:checked"),
                ids = [];

            if(checked.length === 0){
                alert("Выберите удаляемых пользователей");
            }else{
                for(let i = 0; i < checked.length; i++){
                    if($(checked[i]).val() !== 'on')
                        ids.push($(checked[i]).val());
                }
                return users.setUserStatus(ids, 0);
                //console.log(ids);
            }

        });

        $(".user_inactive").off("click").on("click", function(e){
            e.preventDefault();
            let checked = $(".custom_checkbox input:checked"),
                ids = [];

            if(checked.length === 0){
                alert("Выберите блокируемых пользователей");
            }else{
                for(let i = 0; i < checked.length; i++){
                    if($(checked[i]).val() !== 'on')
                        ids.push($(checked[i]).val());
                }
                return users.setUserStatus(ids, 2);
                //console.log(ids);
            }

        });

        $(".user_active").off("click").on("click", function(e){
            e.preventDefault();
            let checked = $(".custom_checkbox input:checked"),
                ids = [];

            if(checked.length === 0){
                alert("Выберите разблокируемых пользователей");
            }else{
                for(let i = 0; i < checked.length; i++){
                    if($(checked[i]).val() !== 'on')
                        ids.push($(checked[i]).val());
                }
                return users.setUserStatus(ids, 1);
                //console.log(ids);
            }

        });

        $("select[name=userStatus]").on("el_select_change", function(){
            if($(this).val().length > 0){
                let userId = $(this).closest("td").attr("id").replace("uid", ""),
                    userStatus = parseInt($(this).val()),
                    $userGroupsSelect = $("#uid" + userId + " [name=userGroups]"),
                    $userGroups = $("#uid" + userId + " [name=userGroups] ~ .el_data");

                $.post("/", {ajax: 1, action: "changeUserStatus", user: userId, status: $(this).val()}, function(data){
                    let answer = JSON.parse(data);

                    if(answer.result) {
                        el_tools.notify(answer.result, "Результат", answer.resultText);

                        if(userStatus === 10) {
                            setTimeout(function (){users.updateUserGroup(userId);}, 300);
                            $userGroupsSelect.addClass('userGroups_hide');
                            $userGroups.hide();
                        }else{
                            setTimeout(function (){users.updateUserGroup(userId);}, 300);
                            setTimeout(function (){
                                users.updateUserGroups(userId);
                                $userGroupsSelect.removeClass('userGroups_hide');
                                $userGroups.show();
                            }, 300);

                        }
                    }
                })
            }
        });

        $("select[name=userGroups]").on("el_select_change", function(){
            let userId = $(this).closest("td").attr("id").replace("uid", "");
            $.post("/", {ajax: 1, action: "setUserGroups", user: userId, group: $(this).val()}, function(data){
                let answer = JSON.parse(data);

                if(answer.result) {
                    el_tools.notify(answer.result, "Результат", answer.resultText);
                }
            })
        });

        $("select[name=userGroup]").on("el_select_change", function(){
            let userId = $(this).closest("td").attr("id").replace("uid", "");
            $.post("/", {ajax: 1, action: "setUserGroup", user: userId, group: $(this).val()}, function(data){
                let answer = JSON.parse(data);

                if(answer.result) {
                    el_tools.notify(answer.result, "Результат", answer.resultText);
                }
            })
        });
    },

    updateUserGroup: function(userId){
        let selectContent = "";
        $.post("/", {ajax: 1, action: "getUserGroup", uid: userId}, function(data){
            let answer = JSON.parse(data);
            if(answer.result) {
                selectContent = answer.resultText;
            }
            $("#uid" + userId + " [name='userGroup']").html(selectContent);
        });
    },

    updateUserGroups: function(userId){
        let selectContent = "";
        $.post("/", {ajax: 1, action: "getUserGroups", uid: userId}, function(data){
            let answer = JSON.parse(data);
            if(answer.result) {
                selectContent = answer.resultText;
            }
            $("#uid" + userId + " [name='userGroups']").html(selectContent);
        });
    },

    bindChangeUserGroup: function(){
        $("select[name=userGroups]").on("el_select_change", function(){
            let selectContent = "",
            userId = $(this).closest("td").attr("id").replace("uid", "");;
            $.post("/", {ajax: 1, action: "getUserGroups", uid: userId}, function(data){
                let answer = JSON.parse(data);
                if(answer.result) {
                    selectContent = answer.resultText;
                }
                $("#uid" + userId + " .user_groups select").html(selectContent);
            });
        });
    },

    setUserStatus: async function(id, status){
        let ok = await confirm("Вы уверены?");
        if(ok){
            $.post("/", {ajax: 1, action: "setUserStatus", value: status, id: id}, function (data) {
                let answer = JSON.parse(data);
                if(answer.result) {
                    users.userListUpdate();
                }
                alert(answer.resultText);
            });
        }
    },

    userListUpdate: function(){
        $.post("/", {ajax: 1, action: "getUsers"}, function(data){
            $("#users_table").html(data);
            users.buttons_init();
        });
    },

    getUsersCount(params){
        $("#found_users span").html("");
        $("#found_users img").show();
        $.post("/", {ajax: 1, action: "getUsersCount", filter: params}, function (data) {
            $("#user_mailing button").attr("disabled", data === "Пользователи не найдены");
            $("#found_users img").hide();
            $("#found_users span").html(data);
            $("#found_users").show();
        });
    },

    popupNewInit: function(){

        //users.getUsersCount();

        $("select[name='sf8[]']").on("el_select_change", function(){
            if($(this).val().length === 1){
                let region = $(this).val();
                $.post("/", {ajax: 1, action: "getRegion", subject: region}, function (data) {
                    $("select[name='sf9[]']").html(data);
                    $(".detail").show();
                    users.getUsersCount({sf8: region})
                });
            }else {
                $(".detail").hide();
                $("#found_users").hide();
            }
        });

        $("select[name='region']").on("el_select_change", function(){
            if($(this).val().length > 0){
                $.post("/", {ajax: 1, action: "getRegion", subject: $(this).val(), values: $(this).data("values")},
                    function (data) {
                        $("select[name='district']").html(data);
                        $(".detail").show();
                    }
                );
            }else {
                $(".detail").hide();
            }
        });

        $("#user_mailing select").on("el_select_change", function(){
            let $elements = $("#user_mailing select"),
                params = {};
            for(let i = 0; i < $elements.length; i++){
                if($($elements[i]).val().length > 0) {
                    params[$($elements[i]).attr("name").replace(/\[\]/gm, '')] = $($elements[i]).val();
                }
            }
            users.getUsersCount(params);
        });

        $("#gen_pass").on("click", function(e){
            e.preventDefault();
            $("#metka_6").attr("type", "text").val(el_tools.genPass(10));
        });

        $("#init_select_all").on("change", function(){
            let elems = $(".subject, .prof");
            if($(this).prop("checked")){
                elems.hide();
                $(".detail").hide();
                $("[name='sf16[]']").closest(".item").hide();
                users.getUsersCount();
            }else{
                elems.show();
                if($("select[name='sf8[]']").val().length === 1){
                    $(".detail").show();
                    users.getUsersCount({sf8: $(this).val()})
                }else{
                    $("#found_users").hide();
                }
            }
        });

        $("#user_filter button[type=reset]").off("click").on("click", function(){
            document.location.href = document.location.pathname;
        });

        $("#fpost_index").off("input keyup").on("input keyup", function(){
            if($(this).val().replace(/_/g, "").length >= 5){
                $.post("/", {ajax: 1, action: "getGroups", index: $(this).val()}, function(data){
                    let answer = JSON.parse(data);

                    if(answer.result) {
                        $("[name='sf16[]']").html(answer.resultText).closest(".item").show();
                    }
                })
            }
        }).mask('999999');

        $("#upost_index").off("input keyup").on("input keyup", function(){
            if($(this).val().replace(/_/g, "").length >= 5){
                $.post("/", {ajax: 1, action: "getGroups", index: $(this).val(), values: $(this).data("values")}, function(data){
                    let answer = JSON.parse(data);

                    if(answer.result) {
                        $("[name='group']").html(answer.resultText).closest(".item").show();
                    }
                })
            }
        }).mask('999999');

        $("#reset_filter").off("click").on("click", function(e){
            e.preventDefault();
            document.location.href = "/lichnyy-kabinet/polzovateli/";
        });

        $("select[name=region]").trigger("el_select_change");
        $("#upost_index").trigger("keyup");
        //setTimeout(function (){$("#upost_index").trigger("keyup");}, 1000);
    }

}

$(document).ready(function(){
    users.buttons_init();
});