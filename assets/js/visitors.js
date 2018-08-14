$('document').ready(function () {

    // make ajax request to load data from SQL table on page load.
    $("#add-visitor-form").submit(function (e) {

        // clear messages
        $('.visitor-err-msg').remove();
        $('.visitor-error-msg').remove();
        $('.visitor-success-msg').remove();
        $('#add-visitor-form input').css("border", "1px solid #ced4da");

        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "assets/addVisitor.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                ////console.log(data);
                // Loop through and output visitors data.

                $.each(JSON.parse(data), function (index, value) {
                    ////console.log(value);
                    if (value.status == "error") {
                        // grab corresponding input element and output error message below
                        $('#add-visitor-form input[name="' + value.field + '"]').after('<span class="visitor-err-msg">' + value.msg + '</span>');
                        $('#add-visitor-form input[name="' + value.field + '"]').css("border", "solid red");
                    } else {
                        // clear error messages
                        $('#visitor-header').after('<p class="visitor-success-msg">' + value.msg + '</p>');
                        $('#add-visitor-form input').css("border", "1px solid #ced4da");
                        $('#add-edit-select').val('Choose');
                        // call function to add recent visitors name (and id) to the select button for removal.
                        updateVisitors();
                        clearFields();
                    }
                });
            } //success function
        }); //ajax call add visitor
        e.preventDefault();
    }); // add visitor form submit

    $("#edit-visitor-form").submit(function (e) {

        // clear messages
        $('.visitor-err-msg').remove();
        $('.visitor-error-msg').remove();
        $('.visitor-success-msg').remove();
        $('#edit-visitor-form input').css("border", "1px solid #ced4da");
        $('#edit-visitor-form select').css("border", "1px solid #ced4da");

        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "assets/editVisitors.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                ////console.log(data);
                // Loop through and output visitors data.
                $.each(JSON.parse(data), function (index, value) {
                    ////console.log(value);
                    if (value.status == "error") {
                        // grab corresponding input element and output error message below
                        $('#edit-visitor-form input[name="' + value.field + '"]').after('<span class="visitor-err-msg">' + value.msg + '</span>');
                        $('#edit-visitor-form select[name="' + value.field + '"]').after('<span class="visitor-err-msg">' + value.msg + '</span>');
                        $('#edit-visitor-form input[name="' + value.field + '"]').css("border", "solid red");

                        $('#edit-visitor-form select[name="' + value.field + '"]').css("border", "solid red");

                    } else {
                        // clear error messages
                        $('#visitor-header').after('<p class="visitor-success-msg">' + value.msg + '</p>');
                        $('#edit-visitor-form input').css("border", "1px solid #ced4da");
                        $('#edit-visitor-form select').css("border", "1px solid #ced4da");
                        $('#add-edit-select').val('Choose');
                        // call function to add recent visitors name (and id) to the select button for removal.
                        updateVisitors();
                        clearFields();
                    }
                });
            } //success function
        }); //ajax call edit visitor
        e.preventDefault();
    }); // edit visitor form submit 

    $("#remove-visitor-form").submit(function (e) {

        $('.visitor-success-msg').remove();
        $('.visitor-error-msg').remove();
        $('.visitor-err-msg').remove();
        $('#remove-visitor-form select').css("border", "1px solid #ced4da");


        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "assets/removeVisitors.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $.each(JSON.parse(data), function (index, value) {
                    ////console.log(value);
                    if (value.status == "error") {
                        // grab corresponding input element and output error message below
                        $('#remove-visitor-form select[name="' + value.field + '"]').after('<span class="visitor-err-msg">' + value.msg + '</span>');
                        $('#remove-visitor-form select[name="' + value.field + '"]').css("border", "solid red");

                    } else {

                        $('#visitor-header').after('<p class="visitor-success-msg">' + value.msg + '</p>');
                        $('#remove-visitor-form select').css("border", "1px solid #ced4da");
                        $('#add-edit-select').val('Choose');
                        // call function to add recent visitors name (and id) to the select button for removal.
                        updateVisitors();
                        clearFields();
                    }
                });

            } //success function
        }); //ajax call
        e.preventDefault();
    }); // remove visitor form submit

    // if visitor is removed or added, the select option is updated to reflect the remaining visitors.
    // this function is called when a visitor is added, or removed. 
    function updateVisitors() {
        // Removes all select option after the defaul selected option "selected"
        $('#removeVisitorList #visitor-default-select-option').siblings().remove();
        $('#editVisitorsList #edit-visitor-default-select-option').siblings().remove();

        // Remove visitor cards. 
        $('.visitor-card').remove();

        // make ajax get from remove visitors.php which will query the database
        $.ajax({
            url: "assets/updateVisitors.php",
            type: "GET",
            dataType: 'json',
            cache: false,
            success: function (data) {

                $(".no-visitor-msg").remove();

                $.each(data, function (index, value) {
                    $.each(value, function (index, value) {

                        $('#removeVisitorList #visitor-default-select-option').after('<option value="' + index + '">' + value[0] + '</option');

                        $('#editVisitorsList #edit-visitor-default-select-option').after('<option value="' + index + '">' + value[0] + '</option');

                        $('.current-info-container-visitors .current-info-inner').append("<div class='visitor-card'><div class='visitor-card-inner-top'><div class='visitor-heading'><h4>Visitor</h4><p>" + value[0] + "</p></div><div><p>for</p></div><div class='roomie-heading'><h4>Roomie</h4><p>" + value[1] + "</p></div></div><div class='visitor-card-inner-bottom'><p>Staying From: <span style='white-space:nowrap'>" + value[2] + " to " + value[3] + " (" + value[4] + " nights)</span></p></div></div>");

                    }); // each loop
                }); // each loop
            }, // success function
            error: function (error) {
                $(".current-info-inner").append("<p class='no-visitor-msg'>No Visitors Scheduled.</p>");
            } // error function
        }); //ajax get method
    }

    // Updates select option value on page load if dates change...
    $.ajax({
        url: "assets/updateVisitors.php",
        type: "GET",
        dataType: 'json',
        cache: false,
        success: function (data) {

            $(".no-visitor-msg").remove();
            ////console.log(data);
            $.each(data, function (index, value) {
                $.each(value, function (index, value) {
                    $('#removeVisitorList #visitor-default-select-option').after('<option value="' + index + '">' + value[0] + '</option');

                    $('#editVisitorsList #edit-visitor-default-select-option').after('<option value="' + index + '">' + value[0] + '</option');

                    $('.current-info-container-visitors .current-info-inner').append("<div class='visitor-card'><div class='visitor-card-inner-top'><div class='visitor-heading'><h4>Visitor</h4><p>" + value[0] + "</p></div><div><p>for</p></div><div class='roomie-heading'><h4>Roomie</h4><p>" + value[1] + "</p></div></div><div class='visitor-card-inner-bottom'><p>Staying From: <span style='white-space:nowrap'>" + value[2] + " to " + value[3] + " (" + value[4] + " nights)</span></p></div></div>");

                }); // each loop

            }); // each loop
        }, // success function
        error: function (error) {
            $(".current-info-inner").append("<p class='no-visitor-msg'>No Visitors Scheduled.</p>");
        } // error function
    }); //ajax get method

    // Clear input fields after submit
    function clearFields() {
        ////console.log("here");

        $('.visitor-err-msg').remove();
        $('.visitor-error-msg').remove();

        $('#add-visitor-form').find('input').val('');
        $('#add-visitor-form input').css("border", "1px solid #ced4da");
        $('#add-visitor-form select').css("border", "1px solid #ced4da");

        $('#edit-visitor-form').find('input').val('');
        $('#editVisitorsList').val("Select");
        $('#edit-visitor-form input').css("border", "1px solid #ced4da");
        $('#edit-visitor-form select').css("border", "1px solid #ced4da");

        $('#removeVisitorList').val("Select");
        $('#remove-visitor-form select').css("border", "1px solid #ced4da");

        visitorFormDisplay();

    } // function clear fields

    // Prevent dates before the current date from being selected. 
    // current date in ISO format cut to display in mm/dd/yyyy format
    //$currentDate = new Date().toISOString().slice(0, 10);
    // Add min attribute to startDate to restrict selection before the current date. 
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }

    if (mm < 10) {
        mm = '0' + mm;
    }

    today = yyyy + '-' + mm + '-' + dd;
    $('.inputDate').attr('min', today);
    
    //console.log("current date is: " + today);
    $('.inputDate').attr('min', today);


    /*------ Add/Edit Section-----------------------


     For Selection of chore as either: Add or Edit:
     Default 'choose' will set both displays of 'add' or 'edit' to 'none'
     
     */

    function visitorFormDisplay() {

        var add_edit_select = document.querySelector("#add-edit-select");
        var addVisitorContainer = document.querySelector(".add-visitor-container");
        var editVisitorContainer = document.querySelector(".edit-visitor-container");
        var removeVisitorContainer = document.querySelector(".remove-visitor-container");

        if (add_edit_select.value == "Choose") {
            addVisitorContainer.style.display = "none";
            editVisitorContainer.style.display = "none";
            removeVisitorContainer.style.display = "none";
            
        }

        add_edit_select.onchange = function () {
            if (add_edit_select.value == "Choose") {
                addVisitorContainer.style.display = "none";
                editVisitorContainer.style.display = "none";
                removeVisitorContainer.style.display = "none";
                clearFields();

            }
            if (add_edit_select.value == "Add") {
                addVisitorContainer.style.display = "block";
                editVisitorContainer.style.display = "none";
                removeVisitorContainer.style.display = "none";
                clearFields();

            }
            if (add_edit_select.value == "Edit") {
                addVisitorContainer.style.display = "none";
                editVisitorContainer.style.display = "block";
                removeVisitorContainer.style.display = "none";
                clearFields();

            }
            if (add_edit_select.value == "Remove") {
                addVisitorContainer.style.display = "none";
                editVisitorContainer.style.display = "none";
                removeVisitorContainer.style.display = "block";
                clearFields();

            }
        };
    }

    visitorFormDisplay();

    var editVisitorsList = document.querySelector('#editVisitorsList');
    editVisitorsList.addEventListener("change", function () {
        ////console.log(this.value);
        $.ajax({
            url: 'assets/updateEditVisitorsSelect.php',
            data: {
                visitorid: this.value
            },
            type: 'get',
            dataType: 'json',
            cache: false,
            success: function (data) {
                $.each(data, function (index, value) {
                    ////console.log(value);
                    $("#userListEdit option[value=" + value.hostID + "]").prop("selected", true);
                    $(".edit-visitor-container input[name='arrivalDate']").val(value.arrivalDate);
                    $(".edit-visitor-container input[name='leaveDate']").val(value.leaveDate);


                }); // each loop
            } // success function
        }); // ajax get call
    }); // event listener change select visitors

    // Add click event listener to cancel button for add, edit, and remove forms.

    var editVisitorBtnCancel = document.querySelector("#edit-visitor-btn-cancel");
    var addVisitorBtnCancel = document.querySelector("#add-visitor-btn-cancel");
    var removeVisitorBtnCancel = document.querySelector("#remove-visitor-btn-cancel");

    editVisitorBtnCancel.addEventListener("click", function () {
        $("#add-edit-select").val("Choose");
        visitorFormDisplay();
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });
    addVisitorBtnCancel.addEventListener("click", function () {
        $("#add-edit-select").val("Choose");
        visitorFormDisplay();
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');

    });
    removeVisitorBtnCancel.addEventListener("click", function () {
        $("#add-edit-select").val("Choose");
        visitorFormDisplay();
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');

    });


}); //Document ready
