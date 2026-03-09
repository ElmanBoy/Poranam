function showOrder(orderId){
    $.post("/", {ajax: 1, action: "getOrder", id: orderId}, function(data){
        $("#favore .title").html(data.title);
        $("#favor_content").html(data.cart_content);
        cartButtonInit("#favor_content ");
        titleInit();

        $("#favore").css({"opacity": "0", "display": "flex"}).animate(
            {
                opacity: 1
            }, 50, "linear", function () {
                $("#favore .wrap").addClass("show");
            }
        );
    });
}

function lkInit(){
    $(".show_history").on("click", function (e) {
        e.preventDefault();
        showOrder($(this).data("value"));
        return false;
    });
}

$(document).ready(function () {
    lkInit();
});