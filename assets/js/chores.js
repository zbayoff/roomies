$('document').ready(function () {

    $('#add-chore-form').submit(function (e) {
        // clear messages
        $('.chore-err-msg').remove();
        $('.chore-error-msg').remove();
        $('.chore-success-msg').remove();
        $('.add-chore-order-container select').css("border", "1px solid #ced4da");
        $('input').css("border", "1px solid #ced4da");

        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "assets/addChores.php",
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
                        $('#add-chore-form input[name="' + value.field + '"]').after('<span class="chore-err-msg">' + value.msg + '</span>');
                        $('#add-chore-form input[name="' + value.field + '"]').css("border", "solid red");
                        if (value.field == "numDaysRadioAdd") {
                            ////console.log(value);
                            $('#add-chore-form label[for="' + value.field + '"]').after('<p class="chore-err-msg">' + value.msg + '</p>');
                            $('#add-chore-form input[name="numDays"]').css("border", "solid red");
                        }
                        if (value.field == "userOrder") {
                            $(".add-chore-order-container").after('<p class="chore-err-msg">' + value.msg + '</p>');
                            $('.add-chore-order-container select:enabled').css("border", "solid red");
                            $('.add-chore-order-container select:disabled').css("border", "1px solid #ced4da");
                        }
                        if (value.field == "userDayOfWeek") {
                            $(".add-day-of-week-container").after('<p class="chore-err-msg">' + value.msg + '</p>');
                            $('.add-day-of-week-container select:enabled').css("border", "solid red");
                            $('.add-day-of-week-container select:disabled').css("border", "1px solid #ced4da");
                        }
                    } else {
                        // clear error messages
                        $('#chore-header').after('<p class="chore-success-msg">' + value.msg + '</p>');
                        $('#add-chore-form input').css("border", "1px solid #ced4da");
                        $('#add-edit-select').val('Choose');
                        $('.add-chore-order-container select').css("border", "1px solid #ced4da");
                        $('.add-day-of-week-container select').css("border", "1px solid #ced4da");

                        // call function to add recent visitors name (and id) to the select button for removal.
                        //addChoretoRemovalSelect();
                        
                        clearFields();
                        updateChores();
                    };
                });
            } // success function
        }); // ajax add chores post
        e.preventDefault();
    }); // add form submit function

    $('#remove-chore-form').submit(function (e) {

        // clear messages
        $('.chore-err-msg').remove();
        $('.chore-error-msg').remove();
        $('.chore-success-msg').remove();
        $('select').css("border", "1px solid #ced4da");

        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "assets/removeChores.php",
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
                        $('#remove-chore-form select[name="' + value.field + '"]').after('<span class="chore-err-msg">' + value.msg + '</span>');
                        $('#remove-chore-form select[name="' + value.field + '"]').css("border", "solid red");

                    } else {
                        // clear error messages
                        $('#chore-header').after('<p class="chore-success-msg">' + value.msg + '</p>');
                        $('#remove-chore-form select').css("border", "1px solid #ced4da");
                        $('#add-edit-select').val('Choose');
                        
                        // call function to add recent visitors name (and id) to the select button for removal.
                        clearFields();
                        updateChores();
                    };
                });
            } // success function
        }); // ajax remove chores post
        e.preventDefault();
    }); // remove form submit

    $('#edit-chore-form').submit(function (e) {

        // clear messages
        $('.chore-err-msg').remove();
        $('.chore-error-msg').remove();
        $('.chore-success-msg').remove();
        $('input').css("border", "1px solid #ced4da");
        $('select').css("border", "1px solid #ced4da");
        $('.edit-chore-order-container select').css("border", "1px solid #ced4da");


        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "assets/editChores.php",
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
                        $('#edit-chore-form select[name="' + value.field + '"]').after('<span class="chore-err-msg">' + value.msg + '</span>');
                        $('#edit-chore-form select[name="' + value.field + '"]').css("border", "solid red");
                        $('#edit-chore-form input[name="' + value.field + '"]').after('<span class="chore-err-msg">' + value.msg + '</span>');
                        $('#edit-chore-form input[name="' + value.field + '"]').css("border", "solid red");
                        if (value.field == "numDaysRadioEdit") {
                            ////console.log(value);
                            $('#edit-chore-form label[for="' + value.field + '"]').after('<p class="chore-err-msg">' + value.msg + '</p>');
                            $('#edit-chore-form input[name="numDays"]').css("border", "solid red");
                        }
                        if (value.field == "userOrder") {
                            $(".edit-chore-order-container").after('<p class="chore-err-msg">' + value.msg + '</p>');
                            $('.edit-chore-order-container select:enabled').css("border", "solid red");
                            $('.edit-chore-order-container select:disabled').css("border", "1px solid #ced4da");
                        }
                        if (value.field == "userDayOfWeek") {
                            $(".edit-day-of-week-container").after('<p class="chore-err-msg">' + value.msg + '</p>');
                            $('.edit-day-of-week-container select:enabled').css("border", "solid red");
                            $('.edit-day-of-week-container select:disabled').css("border", "1px solid #ced4da");
                        }
                    } else {
                        // clear error messages
                        $('#chore-header').after('<p class="chore-success-msg">' + value.msg + '</p>');
                        $('#edit-chore-form input').css("border", "1px solid #ced4da");
                        $('#edit-chore-form select').css("border", "1px solid #ced4da");
                        $('#add-edit-select').val('Choose');
                        $('.edit-chore-order-container select').css("border", "1px solid #ced4da");
                        $('.edit-day-of-week-container select').css("border", "1px solid #ced4da");

                        // call function to add recent visitors name (and id) to the select button for removal.
                        clearFields();
                        updateChores();
                    };
                });
            } // success function
        }); // ajax remove chores post
        e.preventDefault();
    }); // edit form submit

    // add Chore to Remove and Edit list.
    // Display all chores and user dates in table
    function updateChores() {
        // Removes all select option after the defaul selected option "selected"
        $('#removeChoresList #chore-default-select-option').siblings().remove();
        $('#editChoresList #edit-chore-default-select-option').siblings().remove();

        // Remove chore cards. 
        $('.chore-card').remove();

        $.ajax({
            url: "assets/updateChores.php",
            type: "GET",
            dataType: 'json',
            cache: false,
            success: function (data) {
                //console.log(data);
                var users = [];
                var choreName = "";

                $(".no-chore-msg").remove();

                $.each(data, function (indexNo, choreObject) {
                    ////console.log(choreObject)
                    $.each(choreObject, function (choreID, choreArray) {
                        ////console.log(choreArray);

                        $('#removeChoresList #chore-default-select-option').after('<option value="' + choreID + '">' + choreArray.choreName + '</option');

                        $('#editChoresList #edit-chore-default-select-option').after('<option value="' + choreID + '">' + choreArray.choreName + '</option');

                        // Append to chore name, the frequency, num days or days of week the chore is performed. 

                        if (choreArray.choreFreq == "numDays") {
                            if (choreArray.choreNumDays == 1) {
                                var numDaysMsg = ('<span class="chore-freq-msg">performed every day</span>');
                            } else if (choreArray.choreNumDays == 7) {
                                var numDaysMsg = ('<span class="chore-freq-msg">performed every week</span>');
                            } else if (choreArray.choreNumDays == 30) {
                                var numDaysMsg = ('<span class="chore-freq-msg">performed every month</span>');
                            } else if (choreArray.choreNumDays == 31) {
                                var numDaysMsg = ('<span class="chore-freq-msg">performed every month</span>');
                            } else if (choreArray.choreNumDays == 365) {
                                var numDaysMsg = ('<span class="chore-freq-msg">performed every year</span>');
                            } else {
                                var numDaysMsg = ("<span class='chore-freq-msg'>performed every  " + choreArray.choreNumDays + "  days</span>");
                            }

                            ////console.log(numDaysMsg);

                            $('.current-info-container-chores .current-info-inner').append("<div id='" + choreID + "' class= 'chore-card " + choreArray.choreFreq + "'><h3>" + choreArray.choreName + "</h3>" + numDaysMsg + "<table class='table'><thead><tr><th>Roomie</th><th>Day</th></tr></thead><tbody></tbody></table></div>");



                        }
                        var dayOfWeek = [];
                        var dayOfWeekPos = 0;
                        var dayOfWeekPosArr = [];
                        var newDayofWeekAbbrv = "";

                        if (choreArray.choreFreq == "dayOfWeek") {
                            $.each(choreArray.users, function (index, value) {
                                if ((value.choreDate).slice(0, 2) == "Su") {
                                    newDayofWeekAbbrv = "Su";
                                    dayOfWeekPos = 0;
                                } else if ((value.choreDate).slice(0, 2) == "Mo") {
                                    newDayofWeekAbbrv = "M";
                                    dayOfWeekPos = 1;
                                } else if ((value.choreDate).slice(0, 2) == "Tu") {
                                    newDayofWeekAbbrv = "Tu";
                                    dayOfWeekPos = 2;
                                } else if ((value.choreDate).slice(0, 2) == "We") {
                                    newDayofWeekAbbrv = "W";
                                    dayOfWeekPos = 3;
                                } else if ((value.choreDate).slice(0, 2) == "Th") {
                                    newDayofWeekAbbrv = "Th";
                                    dayOfWeekPos = 4;
                                } else if ((value.choreDate).slice(0, 2) == "Fr") {
                                    newDayofWeekAbbrv = "F";
                                    dayOfWeekPos = 5;
                                } else if ((value.choreDate).slice(0, 2) == "Sa") {
                                    newDayofWeekAbbrv = "Sa";
                                    dayOfWeekPos = 6;
                                }
                                dayOfWeek.push(newDayofWeekAbbrv);
                                dayOfWeekPosArr.push(dayOfWeekPos);

                            }); // each loop

                            ////console.log(dayOfWeek);
                            ////console.log(dayOfWeekPosArr);

                            //Zip the users and day of week arrays together so that sorting one array will sort the other respectively 

                            zipped = [];

                            for (var i = 0; i < dayOfWeekPosArr.length; i++) {
                                zipped.push({
                                    dayOfWeek: dayOfWeek[i],
                                    dayOfWeekPosArr: dayOfWeekPosArr[i]
                                });
                            }

                            // Sort user day of weeks by day
                            zipped.sort(function (a, b) {
                                return a.dayOfWeekPosArr - b.dayOfWeekPosArr;
                            });


                            var z;

                            for (var i = 0; i < zipped.length; i++) {
                                z = zipped[i];
                                dayOfWeek[i] = z.dayOfWeek;
                                dayOfWeekPosArr[i] = z.dayOfWeekPosArr;
                            }

                            ////console.log(dayOfWeek);
                            ////console.log(dayOfWeekPosArr);

                            var dayofWeekmsg = ('<span class="chore-freq-msg">performed every ' + dayOfWeek + ' </span>');

                            $('.current-info-container-chores .current-info-inner').append("<div id='" + choreID + "' class= 'chore-card " + choreArray.choreFreq + "'><h3>" + choreArray.choreName + "</h3>" + dayofWeekmsg + "<table class='table'><thead><tr><th>Roomie</th><th>Day</th></tr></thead><tbody></tbody></table></div>");
                        }

                        ////console.log(this);

                        $.each(choreArray.users, function (index, value) {

                            var user = "<td>" + value.userName + "</td>";
                            var choreDate = "<td>" + value.choreDate + "</td>";
                            $("#" + choreID + " tbody").append("<tr>" + user + choreDate + "</tr>");

                        }); // each loop

                    }); // each loop
                }); // each loop
            }, // success function
            error: function (error) {
                $(".current-info-inner").append("<p class='no-chore-msg'>No Chores Scheduled.</p>");
            } // error function
        }); // ajax get
    }; // update chores function

    $.ajax({
        url: "assets/updateChores.php",
        type: "GET",
        dataType: 'json',
        cache: false,
        success: function (data) {
            
            $(".no-chore-msg").remove();
            var users = [];
            var choreName = "";

            $.each(data, function (indexNo, choreObject) {
                ////console.log(choreObject)
                $.each(choreObject, function (choreID, choreArray) {
                    ////console.log(choreArray);

                    $('#removeChoresList #chore-default-select-option').after('<option value="' + choreID + '">' + choreArray.choreName + '</option');

                    $('#editChoresList #edit-chore-default-select-option').after('<option value="' + choreID + '">' + choreArray.choreName + '</option');

                    // Append to chore name, the frequency, num days or days of week the chore is performed. 

                    if (choreArray.choreFreq == "numDays") {
                        if (choreArray.choreNumDays == 1) {
                            var numDaysMsg = ('<span class="chore-freq-msg">performed every day</span>');
                        } else if (choreArray.choreNumDays == 7) {
                            var numDaysMsg = ('<span class="chore-freq-msg">performed every week</span>');
                        } else if (choreArray.choreNumDays == 30) {
                            var numDaysMsg = ('<span class="chore-freq-msg">performed every month</span>');
                        } else if (choreArray.choreNumDays == 31) {
                            var numDaysMsg = ('<span class="chore-freq-msg">performed every month</span>');
                        } else if (choreArray.choreNumDays == 365) {
                            var numDaysMsg = ('<span class="chore-freq-msg">performed every year</span>');
                        } else {
                            var numDaysMsg = ("<span class='chore-freq-msg'>performed every  " + choreArray.choreNumDays + "  days</span>");
                        }

                        ////console.log(numDaysMsg);

                        $('.current-info-container-chores .current-info-inner').append("<div id='" + choreID + "' class= 'chore-card " + choreArray.choreFreq + "'><h3>" + choreArray.choreName + "</h3>" + numDaysMsg + "<table class='table'><thead><tr><th>Roomie</th><th>Day</th></tr></thead><tbody></tbody></table></div>");

                    }

                    var dayOfWeek = [];
                    var dayOfWeekPos = 0;
                    var dayOfWeekPosArr = [];
                    var newDayofWeekAbbrv = "";

                    if (choreArray.choreFreq == "dayOfWeek") {
                        $.each(choreArray.users, function (index, value) {
                            if ((value.choreDate).slice(0, 2) == "Su") {
                                newDayofWeekAbbrv = "Su";
                                dayOfWeekPos = 0;
                            } else if ((value.choreDate).slice(0, 2) == "Mo") {
                                newDayofWeekAbbrv = "M";
                                dayOfWeekPos = 1;
                            } else if ((value.choreDate).slice(0, 2) == "Tu") {
                                newDayofWeekAbbrv = "Tu";
                                dayOfWeekPos = 2;
                            } else if ((value.choreDate).slice(0, 2) == "We") {
                                newDayofWeekAbbrv = "W";
                                dayOfWeekPos = 3;
                            } else if ((value.choreDate).slice(0, 2) == "Th") {
                                newDayofWeekAbbrv = "Th";
                                dayOfWeekPos = 4;
                            } else if ((value.choreDate).slice(0, 2) == "Fr") {
                                newDayofWeekAbbrv = "F";
                                dayOfWeekPos = 5;
                            } else if ((value.choreDate).slice(0, 2) == "Sa") {
                                newDayofWeekAbbrv = "Sa";
                                dayOfWeekPos = 6;
                            }
                            dayOfWeek.push(newDayofWeekAbbrv);
                            dayOfWeekPosArr.push(dayOfWeekPos);

                        }); // each loop

                        ////console.log(dayOfWeek);
                        ////console.log(dayOfWeekPosArr);

                        //Zip the users and day of week arrays together so that sorting one array will sort the other respectively 

                        zipped = [];

                        for (var i = 0; i < dayOfWeekPosArr.length; i++) {
                            zipped.push({
                                dayOfWeek: dayOfWeek[i],
                                dayOfWeekPosArr: dayOfWeekPosArr[i]
                            });
                        }

                        // Sort user day of weeks by day
                        zipped.sort(function (a, b) {
                            return a.dayOfWeekPosArr - b.dayOfWeekPosArr;
                        });

                        var z;

                        for (var i = 0; i < zipped.length; i++) {
                            z = zipped[i];
                            dayOfWeek[i] = z.dayOfWeek;
                            dayOfWeekPosArr[i] = z.dayOfWeekPosArr;
                        }

                        ////console.log(dayOfWeek);
                        ////console.log(dayOfWeekPosArr);

                        var dayofWeekmsg = ('<span class="chore-freq-msg">performed every ' + dayOfWeek + ' </span>');

                        $('.current-info-container-chores .current-info-inner').append("<div id='" + choreID + "' class= 'chore-card " + choreArray.choreFreq + "'><h3>" + choreArray.choreName + "</h3>" + dayofWeekmsg + "<table class='table'><thead><tr><th>Roomie</th><th>Day</th></tr></thead><tbody></tbody></table></div>");
                    }

                    ////console.log(this);

                    $.each(choreArray.users, function (index, value) {

                        var user = "<td>" + value.userName + "</td>";
                        var choreDate = "<td>" + value.choreDate + "</td>";
                        $("#" + choreID + " tbody").append("<tr>" + user + choreDate + "</tr>");

                    }); // each loop

                }); // each loop
            }); // each loop
        }, // success function
        error: function (error) {
            $(".current-info-inner").append("<p class='no-chore-msg'>No Chores Scheduled.</p>");
        } // error function
    }); // ajax update get

    // Clear input fields after submit
    function clearFields() {

        $('.chore-err-msg').remove();
        $('.chore-error-msg').remove();

        ////console.log("here");
        $("#add-chore-form input[name='choreName']").val('');
        $("#add-chore-form input[name='numDays']").val('');
        $("#add-chore-form input[name='startDate']").val('');
        $(".add-day-of-week-container .add-checkbox").prop("checked", false);
        $(".add-chore-order-container .add-checkbox").prop("checked", false);
        $(".add-day-of-week-container select").prop("disabled", false);
        $(".add-chore-order-container select").prop("disabled", false);
        $(".add-chore-order-container select").val("1");
        $(".add-day-of-week-container select").val("Mon");
        $('#add-chore-form input').css("border", "1px solid #ced4da");
        $('#add-chore-form select').css("border", "1px solid #ced4da");

        $("#edit-chore-form input[name='newChoreName']").val('');
        $('#editChoresList').val("Select");
        $("#edit-chore-form input[name='numDays']").val('');
        $("#edit-chore-form input[name='startDate']").val('');
        $(".edit-day-of-week-container .edit-checkbox").prop("checked", false);
        $(".edit-chore-order-container .edit-checkbox").prop("checked", false);
        $(".edit-day-of-week-container select").prop("disabled", false);
        $(".edit-chore-order-container select").prop("disabled", false);
        $(".edit-chore-order-container select").val("1");
        $(".edit-day-of-week-container select").val("Mon");
        $('#edit-chore-form input').css("border", "1px solid #ced4da");
        $('#edit-chore-form select').css("border", "1px solid #ced4da");

        $('#removeChoresList').val('Select');
        $('#remove-chore-form select').css("border", "1px solid #ced4da");

        choreFormDisplay();

    }; // function clear fields

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

    function choreFormDisplay() {

        var add_edit_select = document.querySelector("#add-edit-select");
        var editChoreContainer = document.querySelector(".edit-chore-container");
        var addChoreContainer = document.querySelector(".add-chore-container");
        var removeChoreContainer = document.querySelector(".remove-chore-container");

        if (add_edit_select.value == "Choose") {
            editChoreContainer.style.display = "none";
            addChoreContainer.style.display = "none";
            removeChoreContainer.style.display = "none";
        }

        add_edit_select.onchange = function () {
            if (add_edit_select.value == "Choose") {
                editChoreContainer.style.display = "none";
                addChoreContainer.style.display = "none";
                removeChoreContainer.style.display = "none";
                clearFields();
            }
            if (add_edit_select.value == "Add") {
                editChoreContainer.style.display = "none";
                addChoreContainer.style.display = "block";
                removeChoreContainer.style.display = "none";
                clearFields();
            }
            if (add_edit_select.value == "Edit") {
                editChoreContainer.style.display = "block";
                addChoreContainer.style.display = "none";
                removeChoreContainer.style.display = "none";
                clearFields();
            }
            if (add_edit_select.value == "Remove") {
                editChoreContainer.style.display = "none";
                addChoreContainer.style.display = "none";
                removeChoreContainer.style.display = "block";
                clearFields();
            }
        };

        var addDayofWeekContainer = document.querySelector('.add-day-of-week-container');
        var addChoreOrderContainer = document.querySelector('.add-chore-order-container');
        var numDaysRadioAdd = document.querySelector('#numDaysRadioAdd');
        var dayOfWeekRadioAdd = document.querySelector('#dayOfWeekRadioAdd');

        if (numDaysRadioAdd.checked == true) {
            addDayofWeekContainer.style.display = "none";
            addChoreOrderContainer.style.display = "block";
        }

        $("#add-chore-form input[name=numDays]").focus(function () {
            ////console.log("num days is focused");
            $("#numDaysRadioAdd").prop("checked", true);
            $("#dayOfWeekRadioAdd").prop("checked", false);
            addDayofWeekContainer.style.display = "none";
            addChoreOrderContainer.style.display = "block";
        })

        numDaysRadioAdd.onclick = function () {
            $("#add-chore-form input[name=numDays]").focus();
            $("#numDaysRadioAdd").prop("checked", true);
            $("#dayOfWeekRadioAdd").prop("checked", false);

            if (numDaysRadioAdd.checked == true) {
                addDayofWeekContainer.style.display = "none";
                addChoreOrderContainer.style.display = "block";

            }
        }

        dayOfWeekRadioAdd.onclick = function () {

            $("#dayOfWeekRadioAdd").prop("checked", true);
            $("#numDaysRadioAdd").prop("checked", false);
            $("#add-chore-form input[name='numDays']").val('');
            if (dayOfWeekRadioAdd.checked == true) {
                $('#add-chore-form input[name="numDays"]').css("border", "1px solid #ced4da");
                addDayofWeekContainer.style.display = "block";
                addChoreOrderContainer.style.display = "none";
            }
        }

        var editDayofWeekContainer = document.querySelector('.edit-day-of-week-container');
        var editChoreOrderContainer = document.querySelector('.edit-chore-order-container');
        var numDaysRadioEdit = document.querySelector('#numDaysRadioEdit');
        var dayOfWeekRadioEdit = document.querySelector('#dayOfWeekRadioEdit');

        if (numDaysRadioEdit.checked == true) {
            editDayofWeekContainer.style.display = "none";
            editChoreOrderContainer.style.display = "block";
        }

        $("#edit-chore-form input[name=numDays]").focus(function () {
            ////console.log("num days is focused");
            $("#numDaysRadioEdit").prop("checked", true);
            $("#dayOfWeekRadioEdit").prop("checked", false);
            editDayofWeekContainer.style.display = "none";
            editChoreOrderContainer.style.display = "block";
        })

        numDaysRadioEdit.onclick = function () {
            $("#edit-chore-form input[name=numDays]").focus();
            $("#numDaysRadioEdit").prop("checked", true);
            $("#dayOfWeekRadioEdit").prop("checked", false);

            if (numDaysRadioEdit.checked == true) {
                $('#edit-chore-form input[name="numDays"]').css("border", "1px solid #ced4da");
                editDayofWeekContainer.style.display = "none";
                editChoreOrderContainer.style.display = "block";

            }
        }

        dayOfWeekRadioEdit.onclick = function () {
            $("#dayOfWeekRadioEdit").prop("checked", true);
            $("#numDaysRadioEdit").prop("checked", false);
            $("#edit-chore-form input[name='numDays']").val('');

            if (dayOfWeekRadioEdit.checked == true) {
                editDayofWeekContainer.style.display = "block";
                editChoreOrderContainer.style.display = "none";
            }
        }
    }

    choreFormDisplay();

    // Function to insert data to edit chore form when a chore is selected to be edited
    // so that user doesn't have to fill out repetitive data. 

    var editChoresList = document.querySelector('#editChoresList');
    editChoresList.addEventListener("change", function () {

        $("#edit-chore-form input[name='newChoreName']").val('');
        $("#edit-chore-form input[name='numDays']").val('');
        $("#edit-chore-form input[name='startDate']").val('');
        $(".edit-day-of-week-container .edit-checkbox").prop("checked", false);
        $(".edit-chore-order-container .edit-checkbox").prop("checked", false);
        $(".edit-day-of-week-container select").prop("disabled", false);
        $(".edit-chore-order-container select").prop("disabled", false);
        $(".edit-chore-order-container select").val("1");

        // ajax get request, directs to php script passing the chore_id to be retrieved. 
        $.ajax({
            url: 'assets/updateEditChoresSelect.php',
            data: {
                choreid: this.value
            },
            type: 'get',
            dataType: 'json',
            cache: false,
            success: function (data) {

                var userIDchosen = [];
                var userIDAll = [];
                var choreFreq = "";

                $.each(data, function (index, value) {
                    ////console.log(value.user.userOrder);
                    if (value.user.userOrder) {
                        choreFreq = 'numDays';
                        ////console.log('its num days');
                        $("#edit-chore-form input[name='numDays']").val(value.numDays);
                        $("#edit-chore-form input[id='numDaysRadioEdit']").prop("checked", true).trigger("click");
                        $('#edit-chore-form select[id="' + 'user' + value.user.userID + '-order' + '"]').val(value.user.userOrder);
                        $("#edit-chore-form input[name='startDate']").val(value.startDate);
                        $("#edit-chore-form input[name='newChoreName']").focus();

                        // Loop through selects for user order and if one doesnt match userID, then trigger checkbutton to disable it.

                        userIDchosen.push(value.user.userID);
                        ////console.log("the user id to remain active is: " + userIDchosen);

                    } else {
                        ////console.log('its day of week');
                        choreFreq = "dayofweek";
                        $("#edit-chore-form input[id='dayOfWeekRadioEdit']").prop("checked", true).trigger("click");
                        $("#edit-chore-form input[name='numDays']").val('');
                        $('#edit-chore-form select[id="' + 'user' + value.user.userID + '-dayOfWeek' + '"]').val(value.user.userDayofWeek);
                        $("#edit-chore-form input[name='startDate']").val(value.startDate);
                        $("#edit-chore-form input[name='newChoreName']").focus();
                        userIDchosen.push(value.user.userID);

                    }
                }); // each loop

                // find difference between two arrays and make the difference (the user ids) select options disabled.

                if (choreFreq == "numDays") {
                    $(".edit-chore-order-container select").each(function () {
                        userIDAll.push((this.id).substring(4, (this.id).indexOf("-")));
                    });
                    let difference = userIDAll.filter(x => !userIDchosen.includes(x));
                    for (var i = 0; i < difference.length; i++) {
                        $(".edit-chore-order-container #edit-checkbox-num-" + difference[i] + "").prop("checked", true);
                        $(".edit-chore-order-container #user" + difference[i] + "-order").prop("disabled", true);
                    }
                }
                if (choreFreq == "dayofweek") {
                    $(".edit-day-of-week-container select").each(function () {
                        userIDAll.push((this.id).substring(4, (this.id).indexOf("-")));
                    });
                    let difference = userIDAll.filter(x => !userIDchosen.includes(x));

                    for (var i = 0; i < difference.length; i++) {
                        $(".edit-day-of-week-container #edit-checkbox-dow-" + difference[i] + "").prop("checked", true);
                        $(".edit-day-of-week-container #user" + difference[i] + "-dayOfWeek").prop("disabled", true);
                    }
                }

            } // success function
        }); // ajax get call
    }); // event listener on change

    // Function to disable select field when Exempt checkbox is selected.
    // function will also reassign order number values for those select fields that are not disabled. 

    $('.add-checkbox').click(function () {
        if ($(this).prop('checked')) {
            ////console.log('checkbox is checked');
            $(this).parent().parent().parent().find('select').prop('disabled', true).css("border", "1px solid #ced4da");
            // Loop through select options and renumber users order
            var numSelects = $('.add-chore-order-container select:not(:disabled)').length;
            $('.add-chore-order-container select:not(:disabled) option').remove();
            for (var i = 1; i <= numSelects; i++) {
                $('.add-chore-order-container select:not(:disabled)').append("<option value=" + i + ">" + i + "</option>");
            }
        } else {
            ////console.log('checkbox is UN-checked');
            $(this).parent().parent().parent().find('select').prop('disabled', false);
            // Loop through select options and renumber users order
            var numSelects = $('.add-chore-order-container select:not(:disabled)').length;
            $('.add-chore-order-container select:not(:disabled) option').remove();
            for (var i = 1; i <= numSelects; i++) {
                $('.add-chore-order-container select:not(:disabled)').append("<option value=" + i + ">" + i + "</option>");
            }
        }

    });

    $('.edit-checkbox').click(function () {
        if ($(this).prop('checked')) {
            ////console.log('checkbox is checked');
            $(this).parent().parent().parent().find('select').prop('disabled', true).css("border", "1px solid #ced4da");
            // Loop through select options and renumber users order
            var numSelects = $('.edit-chore-order-container select:not(:disabled)').length;
            $('.edit-chore-order-container select:not(:disabled) option').remove();
            for (var i = 1; i <= numSelects; i++) {
                $('.edit-chore-order-container select:not(:disabled)').append("<option value=" + i + ">" + i + "</option>");
            }
        } else {
            ////console.log('checkbox is UN-checked');
            $(this).parent().parent().parent().find('select').prop('disabled', false);
            // Loop through select options and renumber users order
            var numSelects = $('.edit-chore-order-container select:not(:disabled)').length;
            $('.edit-chore-order-container select:not(:disabled) option').remove();
            for (var i = 1; i <= numSelects; i++) {
                $('.edit-chore-order-container select:not(:disabled)').append("<option value=" + i + ">" + i + "</option>");
            }
        }

    });

    // Add click event listener to cancel button for add, edit, and remove forms.
    var editChoreBtnCancel = document.querySelector("#edit-chore-btn-cancel");
    var addChoreBtnCancel = document.querySelector("#add-chore-btn-cancel");
    var removeChoreBtnCancel = document.querySelector("#remove-chore-btn-cancel");

    editChoreBtnCancel.addEventListener("click", function () {
        $("#add-edit-select").val("Choose");
        choreFormDisplay();
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });
    addChoreBtnCancel.addEventListener("click", function () {
        $("#add-edit-select").val("Choose");
        choreFormDisplay();
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });
    removeChoreBtnCancel.addEventListener("click", function () {
        $("#add-edit-select").val("Choose");
        choreFormDisplay();
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });


}); //Document ready
