function pop_up_warning() {
    var x = document.getElementById("notify_show");
    x.style.display = "flex";
}

function notify_close() {
    $("#notify_show").hide();
}