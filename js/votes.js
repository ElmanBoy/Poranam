let initiatives = {
    buttons_init: function(){
        //Снять выделние в таблице
        $("#check_all").on("click", function () {
            $("tr td:first-child .custom_checkbox input").prop("checked", $(this).prop("checked"));
        });
        $("#button_votes_filter").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('votes_filter', el_tools.getUrlVar());
        });
        $("#button_votes_new").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('votes_new');
        });
        $("#button_votes_approve").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.groupSetInitStatus(5);
        });
        $("#button_votes_start").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.groupSetInitStatus(6);
        });
        $("#button_votes_stop").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.groupSetInitStatus(3);
        });
        $("#button_votes_remove").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.groupSetInitStatus(0);
        });

        $(".edit_votes").off("click").on("click", function(){
            el_app.dialog_open('votes_new', $(this).data("id"));
        });

        $(".votes_approve").off("click").on("click", async function(){
            initiatives.setInitStatus($(this).data("id"), 5);
        });

        $(".votes_run").off("click").on("click", async function(){
            initiatives.setInitStatus($(this).data("id"), 6);
        });

        $(".votes_stop").off("click").on("click", function(){
            initiatives.setInitStatus($(this).data("id"), 7);
        });

        /*$(".votes_vote").off("click").on("click", function(){
            initiatives.setInitStatus($(this).data("id"), 4);
        });*/

        $(".votes_remove").off("click").on("click", function(){
            initiatives.setInitStatus($(this).data("id"), 0);
        });

        $(".votes_statement").off("click").on("click", function(e){
            e.preventDefault();
            initiatives.showStatement($(this).data("id"));
        });

        $("[name=votes_vote]").off("change").on("change", function(){
            let initId = $(this).data("id");

            $.post("/", {ajax: 1, action: "init_vote", id: initId, vote: $(this).val()}, function(data){
                let answer = JSON.parse(data),
                    statValue = $("#tr" + initId + " .description .svg_value"),
                    statCalc = $("#tr" + initId + " .description .svg_calc"),
                    statInfo = $("#tr" + initId + " .description .interes .statInfo");

                $("#tr" + initId + " .description .answer .bar")
                    .css("width", "0%").find("span").html("0");
                $("#tr" + initId + " .description .answer span").html("");

                if(answer.result) {
                    let votes = answer.votes;
                    for(let vid in votes) {
                        let bar = $("#tr" + initId + " .description .answer.choice" + vid + " .bar"),
                            choice = el_tools.el_calcPercent(parseInt(votes[vid]), answer.totalVotes) || 0,
                            voteResult = $("#tr" + initId + " .description .answer .voteResult" + vid);
                        bar.css("width", choice + "%");
                        bar.find("span").html(parseInt(choice));

                        statValue.text(answer.voteStat.percent);
                        voteResult.html((typeof votes[vid] !== "undefined") ? "(" + votes[vid] + ")" : "");
                        statInfo.html(answer.voteStat.votes + " из " + answer.voteStat.total)
                        statCalc.attr("stroke-dasharray", answer.voteStat.percent + " 100");
                    }
                }
                el_tools.notify(answer.result, "Результат", answer.resultText);
            })
        })

        $(".user_profile_link").off("click").on("click", function(e){
            e.preventDefault();
            el_app.dialog_open('user_new', $(this).data("id"));
        });

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
            $.post("/", {ajax: 1, action: "setVoteStatus", value: status, id: id}, function (data) {
                let answer = JSON.parse(data);
                if(answer.result) {
                    initiatives.initListUpdate();
                }
                alert(answer.resultText);
            });
        }
    },

    showStatement: function(id){
        //window.open('/statement?id=' + id, '_blank');

        el_app.dialog_open('vote_participants', id);
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

        initiatives.cloneAnswer();

        $("#post_index, #fpost_index").off("input keyup").on("input keyup", function(){
            if($(this).val().replace(/_/g, "").length >= 5){
                $.post("/", {ajax: 1, action: "getGroups", index: $(this).val(), values: $(this).data("values")},
                    function(data){
                        let answer = JSON.parse(data);

                        if(answer.result) {
                            $("#groups").html(answer.resultText).closest(".item").show();
                        }
                    }
                );
            }
        }).mask('999999');
    },

    cloneAnswer: function(){
        $(".wrap_pop_up .icon.add").off("click").on("click", function(){
            let answer = $(this).closest(".item"),
                clone = answer.clone();

            $(this).removeClass("add").addClass("remove").find("span").text("remove_circle_outline");
            answer.after(clone);
            $(clone).find("input").val("");

            initiatives.answerNumbers();
            initiatives.cloneAnswer();
        });

        $(".wrap_pop_up .icon.remove").off("click").on("click", function(){
            let answer = $(this).closest(".item");
            answer.remove();
            initiatives.answerNumbers();
        });
    },

    answerNumbers: function(){
        let $items = $("#answers .item");
        for(let i = 0; i < $items.length; i++){
            $($items[i]).find("label").html("Ответ " + (i + 1));
        }
    },

    initListUpdate: function(){
        $.post("/", {ajax: 1, action: "getVotes"}, function(data){
            $("#init_table").html(data);
            initiatives.buttons_init();
        });
    }
}

$(document).ready(function(){
    initiatives.buttons_init();
});