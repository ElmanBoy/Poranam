let stat = {
    buttons_init: function(){
        //Снять выделние в таблице
        $("#check_all").on("click", function () {
            $("tr td:first-child .custom_checkbox input").prop("checked", $(this).prop("checked"));
        });
        $("#button_initiative_filter").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('initiative_filter', el_tools.getUrlVar());
        });
        $("#button_initiative_new").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('initiative_new');
        });
        $("#button_initiative_start").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.groupSetInitStatus(2);
        });
        $("#button_initiative_stop").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.groupSetInitStatus(3);
        });
        $("#button_initiative_remove").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.groupSetInitStatus(0);
        });

        $(".edit_init").off("click").on("click", function(){
            el_app.dialog_open('initiative_new', $(this).data("id"));
        });

        $(".init_run").off("click").on("click", async function(){
            initiatives.setInitStatus($(this).data("id"), 2);
        });

        $(".init_stop").off("click").on("click", function(){
            initiatives.setInitStatus($(this).data("id"), 3);
        });

        $(".init_vote").off("click").on("click", function(){
            initiatives.setInitStatus($(this).data("id"), 4);
        });

        $(".init_remove").off("click").on("click", function(){
            initiatives.setInitStatus($(this).data("id"), 0);
        });

        $("[name=init_vote]").off("change").on("change", function(){
            let initId = $(this).data("id");

            $.post("/", {ajax: 1, action: "init_vote", id: initId, vote: $(this).val()}, function(data){
                let answer = JSON.parse(data),
                    aYes = $("#tr" + initId + " .description .answer.yes .bar"),
                    aNo = $("#tr" + initId + " .description .answer.no .bar"),
                    statValue = $("#tr" + initId + " .description .svg_value"),
                    statCalc = $("#tr" + initId + " .description .svg_calc"),
                    voteResultYes = $("#tr" + initId + " .description .answer .voteResultYes"),
                    voteResultNo = $("#tr" + initId + " .description .answer .voteResultNo"),
                    statInfo = $("#tr" + initId + " .description .interes .statInfo");

                if(answer.result) {
                    let positive = el_tools.el_calcPercent(parseInt(answer.votes[1]), answer.totalVotes) || 0,
                        negative = el_tools.el_calcPercent(parseInt(answer.votes[0]), answer.totalVotes) || 0;
                    aYes.css("width", positive + "%");
                    aYes.find("span").html(parseInt(positive));
                    aNo.css("width", negative + "%");
                    aNo.find("span").html(parseInt(negative));

                    statValue.text(answer.voteStat.percent);
                    voteResultNo.html((typeof answer.votes[0] !== "undefined") ? "(" + answer.votes[0] + ")" : "");
                    voteResultYes.html((typeof answer.votes[1] !== "undefined") ? "(" + answer.votes[1] + ")" : "");
                    statInfo.html(answer.voteStat.votes + " из " + answer.voteStat.total)
                    statCalc.attr("stroke-dasharray", answer.voteStat.percent + " 100");
                }
                el_tools.notify(answer.result, "Результат", answer.resultText);
            })
        })

        $(".user_profile_link").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_profile', $(this).data("id"));
        })

    },

    groupSetInitStatus: async function(status){
        let ids = [];
        if(await confirm("Вы уверены?")){
            let checked = $(".table_data .group_check:checked");
            for(let i = 0; i < checked.length; i++){
                ids.push($(checked[i]).val());
            }
            $.post("/", {ajax: 1, action: "setInitStatus", value: status, id: ids}, function (data) {
                let answer = JSON.parse(data);
                if(answer.result) {
                    initiatives.initListUpdate();
                }
                alert(answer.resultText);
            });
        }
    },

    setInitStatus: async function(id, status){
        let ok = await confirm("Вы уверены?");
        if(ok){
            $.post("/", {ajax: 1, action: "setInitStatus", value: status, id: id}, function (data) {
                let answer = JSON.parse(data);
                if(answer.result) {
                    initiatives.initListUpdate();
                }
                alert(answer.resultText);
            });
        }
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

        $("#initiative_filter button[type=reset]").off("click").on("click", function(){
            document.location.href = document.location.pathname;
        });

        $("#post_index").off("input keyup").on("input keyup", function(){
            if($(this).val().replace(/_/g, "").length >= 5){
                $.post("/", {ajax: 1, action: "getGroups", index: $(this).val()}, function(data){
                    let answer = JSON.parse(data);

                    if(answer.result) {
                        $("#groups").html(answer.resultText).closest(".item").show();
                    }
                })
            }
        }).mask('999999');
    },

    initListUpdate: function(){
        $.post("/", {ajax: 1, action: "getInitiatives"}, function(data){
            $("#init_table").html(data);
            initiatives.buttons_init();
        });
    }
}

$(document).ready(function(){
    initiatives.buttons_init();

    $('.more').click(function () {
        $(this).parent().parent().next('.hidden').slideToggle(100, function () {


            if ($(this).is(':hidden')) {
                $('.more .material-icons').html('unfold_less');

            } else {

                $('.more .material-icons').html('unfold_more');

            }
        });
        return false;
    });
});