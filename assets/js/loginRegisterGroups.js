// Login error message formatting

$.each($("#login-form input"), function () {
    if ($(this).siblings("span").text() != "") {
        $(this).css("border", "solid red");
    }
});

// Register error message formatting

$.each($("#register-form input"), function () {
    if ($(this).siblings("span").text() != "") {
        $(this).css("border", "solid red");
    }
});

////console.log($("#join-group-wrapper span").text());

// Group error message formatting

$.each($("#create-group-wrapper input"), function () {
    if ($(this).siblings("span").text() != "") {
        $(this).css("border", "solid red");
        $("#create-group-wrapper").show();
        $("#join-group-wrapper").hide();
        $("#leave-group-wrapper").hide();
        $("#launch-group-wrapper").hide();
    }
});

$.each($("#join-group-wrapper input"), function () {
    if ($(this).siblings("span").text() != "") {
        $(this).css("border", "solid red");
        $("#create-group-wrapper").hide();
        $("#join-group-wrapper").show();
        $("#leave-group-wrapper").hide();
        $("#launch-group-wrapper").hide();
    }
});

$.each($("#leave-group-wrapper input"), function () {
    if ($(this).siblings("span").text() != "") {
        $(this).css("border", "solid red");
        $("#create-group-wrapper").hide();
        $("#join-group-wrapper").hide();
        $("#leave-group-wrapper").show();
        $("#launch-group-wrapper").hide();
    }
});

$.each($("#launch-group-wrapper input"), function () {
    if ($(this).siblings("span").text() != "") {
        $(this).css("border", "solid red");
        $("#create-group-wrapper").hide();
        $("#join-group-wrapper").hide();
        $("#leave-group-wrapper").hide();
        $("#launch-group-wrapper").show();
    }
});

if ($("#create-group-wrapper").css("display") == "block") {
    $("#create-group-link").parent().css("background-color", "#007bff");
    $("#create-group-link").css("color", "white");
}

if ($("#join-group-wrapper").css("display") == "block") {
    $("#join-group-link").parent().css("background-color", "#007bff");
    $("#join-group-link").css("color", "white");
}
if ($("#launch-group-wrapper").css("display") == "block") {
    $("#launch-group-link").parent().css("background-color", "#007bff");
    $("#launch-group-link").css("color", "white");
}

if ($("#leave-group-wrapper").css("display") == "block") {
    $("#leave-group-link").parent().css("background-color", "#007bff");
    $("#leave-group-link").css("color", "white");
}

$("#create-group-link").on("click", function (e) {
    $(".group-succ-msg").remove();
    $(".group-menu .nav-item").css("background-color", "transparent");
    $(".group-menu .nav-link").css("color", "#007bff");
    $(this).css("color", "white");
    $(this).parent().css("background-color", "#007bff");

    $('input').css("border", "1px solid #ced4da");
    $(".error-msg").text("");
    
    $("input[name='group-name']").val("");
    $("input[name='group-password']").val("");

    $("#create-group-wrapper").show();
    $("#join-group-wrapper").hide();
    $("#leave-group-wrapper").hide();
    $("#launch-group-wrapper").hide();

    e.preventDefault();
})

$("#join-group-link").on("click", function (e) {
    $(".group-succ-msg").remove();
    $(".group-menu .nav-item").css("background-color", "transparent");
    $(".group-menu .nav-link").css("color", "#007bff");
    $(this).css("color", "white");
    $(this).parent().css("background-color", "#007bff");


    $('input').css("border", "1px solid #ced4da");
    $(".error-msg").text("");
    
    $("input[name='group-name-join']").val("");
    $("input[name='group-password-join']").val("");

    $("#create-group-wrapper").hide();
    $("#join-group-wrapper").show();
    $("#leave-group-wrapper").hide();
    $("#launch-group-wrapper").hide();

    e.preventDefault();
    //return false;
})

$("#launch-group-link").on("click", function (e) {
    $(".group-succ-msg").remove();
    $(".group-menu .nav-item").css("background-color", "transparent");
    $(".group-menu .nav-link").css("color", "#007bff");
    $(this).css("color", "white");
    $(this).parent().css("background-color", "#007bff");


    $('input').css("border", "1px solid #ced4da");
    $(".error-msg").text("");
    
    $("input[name='group-name-launch']").val("");

    $("#create-group-wrapper").hide();
    $("#join-group-wrapper").hide();
    $("#leave-group-wrapper").hide();
    $("#launch-group-wrapper").show();

    e.preventDefault();
})

$("#leave-group-link").on("click", function (e) {
    $(".group-succ-msg").remove();
    $(".group-menu .nav-item").css("background-color", "transparent");
    $(".group-menu .nav-link").css("color", "#007bff");
    $(this).css("color", "white");
    $(this).parent().css("background-color", "#007bff");
    
    
    $("input[name='group-name-leave']").val("");

    $('input').css("border", "1px solid #ced4da");
    
    
    $(".error-msg").text("");

    $("#create-group-wrapper").hide();
    $("#join-group-wrapper").hide();
    $("#leave-group-wrapper").show();
    $("#launch-group-wrapper").hide();
    e.preventDefault();
})
