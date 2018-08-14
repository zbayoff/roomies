$('document').ready(function () {

    // Grab userdata to display in the inputs for account info
    function fetchUserData() {
        $(".phone-msg").remove();
        $.ajax({
            url: "assets/fetchUserData.php",
            type: "GET",
            dataType: 'json',
            cache: false,
            success: function (data) {
                ////console.log(data);
                $.each(data, function (index, value) {
                    ////console.log(value.fName);
                    $("#first-name-edit").val(value.fName);
                    $("#userFname strong").text(value.fName);
                    $("#last-name-edit").val(value.lName);
                    $("#email-edit").val(value.email);
                    if (value.phone == 0) {
                        $("#phone-edit").after("<p class='phone-msg'>Add your phone number to receive text alerts at the time of your choosing.</p>");
                    } else {
                        $(".user-err-msg").remove();
                        $(".phone-msg").remove();
                        $("#phone-edit").val(value.phone);
                        $("#phone-carrier-edit").val(value.phone_carrier);
                    }

                    ////console.log(value.item_alert_status);

                    if (value.item_alert_status == 0) {
                        ////console.log("here");
                        $("#item-status").prop("checked", false);
                    } else {
                        $("#item-status").prop("checked", true);
                        $("#item-time-msg").show();
                    }

                    if (value.chore_alert_status == 0) {
                        ////console.log("here");
                        $("#chore-status").prop("checked", false);
                        $("#chore-alert-time").val("Select");
                    } else {
                        $("#chore-status").prop("checked", true);
                        $("#chore-notification-wrapper").show();
                        $("#chore-time-msg").show();
                        $("#chore-alert-time").val((value.chore_alert_time).slice(0, -3));
                        ////console.log((value.chore_alert_time).slice(0, -3));
                    }
                    if (value.visitor_alert_status == 0) {
                        $("#visitor-status").prop("checked", false);
                        $("#visitor-alert-time").val("Select");
                    } else {
                        $("#visitor-status").prop("checked", true);
                        $("#visitor-notification-wrapper").show();
                        $("#visitor-time-msg").show();
                        $("#visitor-alert-time").val((value.visitor_alert_time).slice(0, -3));
                        ////console.log((value.chore_alert_time).slice(0, -3));
                    }
                    //$("#visitor-alert-time").val(value.visitor_alert_time);
                }); // each loop
            }, // success function
            complete: function (data) {
                //console.log("fetch user data ajax complete");
                displayFields();
            }, // complete function
            error: function (error) {
                //console.log("no data");
            } // error function
        }); // ajax update get
    } // function fetchUserData

    fetchUserData();

    $("#user-info-form").submit(function (e) {
        var formData = new FormData($(this)[0]);

        $(".user-success-msg").remove();
        $(".user-err-msg").remove();
        $('#user-info-form input').css("border", "1px solid #ced4da");

        $.ajax({
            url: "assets/updateUserInfo.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                ////console.log(data);
                $.each(JSON.parse(data), function (index, value) {
                    ////console.log(value);
                    if (value.status == "error") {
                        // grab corresponding input element and output error message below
                        $('#user-info-form input[name="' + value.field + '"]').after('<span class="user-err-msg">' + value.msg + '</span>');
                        $('#user-info-form input[name="' + value.field + '"]').css("border", "solid red");
                    } else {
                        // clear error messages
                        $(".phone-msg").remove();
                        $('.user-info-inner').prepend('<p class="user-success-msg">' + value.msg + '</p>');
                        $('#user-info-form input[name="' + value.field + '"]').css("border", "1px solid #ced4da");
                        fetchUserData();
                        //clearFields();
                        //updateChores();
                    };
                });
            } // success function
        }); // ajax add chores post
        e.preventDefault();
    }); // user info form submit

    $("#notification-form").submit(function (e) {
        var formData = new FormData($(this)[0]);

        $(".user-success-msg").remove();
        $(".user-err-msg").remove();
        $('#notification-form select').css("border", "1px solid #ced4da");

        $.ajax({
            url: "assets/updateUserPref.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                ////console.log(data);
                $.each(JSON.parse(data), function (index, value) {
                    ////console.log(value);
                    if (value.status == "error") {
                        // grab corresponding input element and output error message below
                        $('#notification-form select[name="' + value.field + '"]').after('<span class="user-err-msg">' + value.msg + '</span>');
                        $('#notification-form select[name="' + value.field + '"]').css("border", "solid red");
                    } else {
                        // clear error messages
                        //$(".phone-msg").remove();
                        $('.notification-inner').prepend('<p class="user-success-msg">' + value.msg + '</p>');
                        $('#notification-form select[name="' + value.field + '"]').css("border", "1px solid #ced4da");
                        fetchUserData();
                        //clearFields();
                        //updateChores();
                    };
                });
            } // success function
        }); // ajax add chores post
        e.preventDefault();
    }); // notification form submit



    function displayFields() {
        var phoneNumberField = document.querySelector("#phone-edit");
        if (phoneNumberField.value == "") {
            
            $("#notification-form").prepend("<p class='phone-number-msg'>Add your phone number to enable notifications.</p>");
            
            //console.log("no phone number");
            itemAlertCheck.checked = false;
            choreAlertCheck.checked = false;
            visitorAlertCheck.checked = false;
            itemAlertMsg.style.display = "none";
            choreAlertMsg.style.display = "none";
            choreAlertWrapper.style.display = "none";
            visitorAlertMsg.style.display = "none";
            visitorAlertWrapper.style.display = "none";
            
            itemAlertCheck.disabled = true;
            choreAlertCheck.disabled = true;
            visitorAlertCheck.disabled = true;
            
        } else {
            $("#notification-form .phone-number-msg").remove();
            //console.log("There IS phone number");
            itemAlertCheck.disabled = false;
            choreAlertCheck.disabled = false;
            visitorAlertCheck.disabled = false;
        }
    }


    var itemAlertCheck = document.querySelector("#item-status");
    var itemAlertMsg = document.querySelector("#item-time-msg");

    if (itemAlertCheck.checked == true) {
        itemAlertMsg.style.display = "block";
    } else {
        itemAlertMsg.style.display = "none";
    }

    itemAlertCheck.addEventListener("click", function () {
        if (this.checked == true) {
            itemAlertMsg.style.display = "block";
        } else {
            itemAlertMsg.style.display = "none";
        }
    });

    var choreAlertCheck = document.querySelector("#chore-status");
    var choreAlertMsg = document.querySelector("#chore-time-msg");
    var choreAlertTime = document.querySelector("#chore-alert-time");
    var choreAlertWrapper = document.querySelector("#chore-notification-wrapper");

    if (choreAlertCheck.checked == true) {
        choreAlertMsg.style.display = "block";
        choreAlertWrapper.style.display = "block";
    } else {
        choreAlertMsg.style.display = "none";
        choreAlertWrapper.style.display = "none";
    }

    choreAlertCheck.addEventListener("click", function () {
        if (this.checked == true) {
            choreAlertMsg.style.display = "block";
            choreAlertTime.value = "Select";
            choreAlertWrapper.style.display = "block";

        } else {
            choreAlertMsg.style.display = "none";
            choreAlertWrapper.style.display = "none";
        }
    });

    var visitorAlertCheck = document.querySelector("#visitor-status");
    var visitorAlertMsg = document.querySelector("#visitor-time-msg");
    var visitorAlertWrapper = document.querySelector("#visitor-notification-wrapper")
    var visitorAlertTime = document.querySelector("#visitor-alert-time")

    if (visitorAlertCheck.checked == true) {
        visitorAlertMsg.style.display = "block";
        visitorAlertWrapper.style.display = "block";
    } else {
        visitorAlertMsg.style.display = "none";
        visitorAlertWrapper.style.display = "none";
    }

    visitorAlertCheck.addEventListener("click", function () {
        if (this.checked == true) {
            visitorAlertMsg.style.display = "block";
            visitorAlertWrapper.style.display = "block";
            visitorAlertTime.value = "Select";
        } else {
            visitorAlertMsg.style.display = "none";
            visitorAlertWrapper.style.display = "none";
        }
    });




}); //document ready
