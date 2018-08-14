//$(document).ready(function () {



$(document).click(function () {
    $(".item-row").css("background-color", "transparent");
    ////console.log("you clicked the docuemtn");
})

// current date
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

today = yyyy + '-' + mm;
////console.log("today is " + today);


// check current date on load, the select month should always display the current month.
// need to append the select options with previous months once the month has passed.
var startDate = moment('2017-11-01');
var currentDate = moment();
var monthAfter = startDate;

while (monthAfter < currentDate) {
    $('#item-month-select').prepend("<option value=" + monthAfter.clone().format('YYYY-MM') + ">" + monthAfter.clone().format('MMM YYYY') + "</option>");
    monthAfter.add(1, 'month');
    ////console.log('month after is: ' + monthAfter.format('YYYY-MM-DD'));
}

// set selected option to current month
$('#item-month-select option').each(function () {
    if ($(this).val() == currentDate.format('YYYY-MM')) {
        $(this).prop('selected', true);
    }
});

// Restrict dates chosen for item date to be only for the current month.
var addItemDateInput = $("#add-item-row input[name=timeAdded]");
var currentDay = moment().format('DD');
var firstDayofMonth = moment().subtract('' + (currentDay - 1) + '', 'days');
var lastDayofMonth = firstDayofMonth.clone().add('1', 'month').subtract('1', 'days');
addItemDateInput.attr('min', firstDayofMonth.format('YYYY-MM-DD'));
addItemDateInput.attr('max', lastDayofMonth.format('YYYY-MM-DD'));

// make current user be the selected option for the Paid By column
var currentUserId = $('#userNameHeader h2').attr('class').substr(14);
////console.log(currentUserId);
$("#add-item-row select[name='addUserID']").find("option[value=" + currentUserId + "]").prop('selected', true);

// function to fetch users IDs and names in case edeit button is clicked, need to have a select button with options to select any user.

var usersSelect = "";

getUserInfo();

function getUserInfo() {
    $.ajax({
        url: "assets/fetchUsers.php",
        type: "GET",
        dataType: 'json',
        cache: false,
        success: function (data) {
            ////console.log(data);
            usersSelect = "<select name='userID' class='editInput form-control user-select'>";
            $.each(data, function (index, value) {
                usersSelect += "<option value='" + value.user_id + "'>" + value.fName + "</option>";
            }); // each loop
            usersSelect += "</select>";
            ////console.log(usersSelect);
        }, // success function
        complete: function (data) {
            //console.log("get users ajax is complete");
            fetchItems();
        }, // complete call back
        error: function (error) {
            //$(".current-info-inner").append("<p class='no-chore-msg'>No Chores Scheduled.</p>");
        } // error function
    }); // ajax user info get

}

$(document).on('click', '#addBtn', function () {

    $(".item-row").css("background-color", "transparent");

    var itemName = $("#items-table input[name='addItemname']").val();
    var itemCost = $("#items-table input[name='addItemCost']").val();
    var userID = $("#items-table select[name='addUserID']").val();
    var timeAdded = $("#items-table input[name='timeAdded']").val();
    var optionElement = $(this).closest("tr").find('[name=addUserID]').find('[value=' + userID + ']');
    var fName = optionElement.text();
    ////console.log(optionElement);
    ////console.log(fName);

    $('.item-success-msg').remove();

    $.ajax({
        url: "assets/addItems.php",
        type: "POST",
        data: {
            "itemName": itemName,
            "itemCost": itemCost,
            "userID": userID,
            "fName": fName,
            "timeAdded": timeAdded
        },
        success: function (data) {
            ////console.log(data);
            $.each(JSON.parse(data), function (index, value) {

                if (value.status == "error") {
                    // grab corresponding input element and output error message below
                    $('.item-error-msg').remove();
                    $('#select-month-container').after('<p class="item-error-msg">' + value.msg + '</p>');
                    $('input[name="' + value.field + '"]').css("border", "solid red");

                } else {

                    if ($(".no-items-msg")) {
                        $(".no-items-msg").remove();
                    }

                    // call user Select function, wait until it executes and returns userSelect



                    $("#items-table #add-item-row").after("<tr class='item-row'><td><span class='editSpan itemName'>" + value.data.itemName + "</span><input id='item-" + value.data.itemID + "' type='text' class='form-control editInput' name='item_name' value='" + value.data.itemName + "'></td><td><span class='editSpan cost'>" + Number(Math.round(value.data.itemCost + 'e2') + 'e-2').toFixed(2) + "</span><input type='number' step='any' class='form-control editInput cost-input' name='item_cost' value='" + Number(Math.round(value.data.itemCost + 'e2') + 'e-2').toFixed(2) + "'></td><td><span data-userID ='" + value.data.userID + "' class='editSpan userID'>" + value.data.fName + "</span>" + usersSelect + "</td><td><span class='editSpan time'>" + (value.data.itemTime).substr(0, 10) + "</span><input type='date' onfocus=this.type='date' class='form-control editInput' name='time_created' value='" + (value.data.itemTime).substr(0, 10) + "'></td><td><button class='btn editBtn action-column' type='submit' value='Edit'><span class='fa fa-edit'></span></button><button class='btn saveBtn action-column' type='submit' value='Save'><span class='fa fa-save'></span></button><button class='btn deleteBtn action-column' type='submit' value='Delete'><span class='fa fa-trash'></span></button><button class='btn btn-danger confirmBtn action-column' type='submit' value='Confirm'><span class='fa fa-check'></span></button><button class='btn cancelEditBtn action-column' type='submit' value='Cancel'><span class='fa fa-remove'></span></button></td></tr>");



                    $("#items-table #add-item-row").next().css("background-color", "lightgreen");

                    // hide editable inputs on load
                    $("#items-table .editInput").hide();
                    $("#items-table .editSpan").show();
                    $("#items-table .cancelEditBtn").hide();
                    $("#items-table .saveBtn").hide();
                    $("#items-table .confirmBtn").hide();
                    $("#items-table .editBtn").show();
                    $("#items-table .deleteBtn").show();

                    // clear error messages
                    $('.item-error-msg').remove();
                    $('input').css("border", "1px solid #ced4da");
                    //$('#select-month-container').after('<p class="item-success-msg">' + value.msg + '</p>');
                    // call function to add recent visitors name (and id) to the select button for removal.
                    //updateVisitors();
                    clearFields();
                    //fetchItems();
                }
            })

        }, // success function
        complete: function (data) {
            //console.log("add item ajax is complete");
        }, // complete call back
        error: function () {

        } // error function
    }); // ajax add item

    return false;
}); // add onclick event function

$(document).on('click', '.editBtn', function () {

    $(".item-row").css("background-color", "transparent");

    var userID = $(this).closest("tr").find('.editSpan.userID').attr('data-userid');
    ////console.log(userID);


    var optionElement = $(this).closest("tr").find('[name=userID]').find('[value=' + userID + ']');

    optionElement.prop('selected', true);

    $('.item-success-msg').remove();
    $('input').css("border", "1px solid #ced4da");

    $(this).closest("tr").find(".editSpan").hide();
    $(this).closest("tr").find(".editInput").show();
    $(this).closest("tr").find(".editBtn").hide();
    $(this).closest("tr").find(".saveBtn").show();
    $(this).closest("tr").find(".cancelEditBtn").show();
    $(this).closest("tr").find(".deleteBtn").hide();
    $(this).closest("tr").find(".confirmBtn").hide();


    return false;
});

$(document).on('click', '.cancelEditBtn', function () {

    $(".item-row").css("background-color", "transparent");

    $(this).closest("tr").find(".editBtn").show();
    $(this).closest("tr").find(".saveBtn").hide();
    $(this).closest("tr").find(".editSpan").show();
    $(this).closest("tr").find(".editInput").hide();
    $(this).closest("tr").find(".cancelEditBtn").hide();
    $(this).closest("tr").find(".deleteBtn").show();
    $(this).closest("tr").find(".confirmBtn").hide();

    $('.item-error-msg').remove();
    $('.item-success-msg').remove();

    return false;
});

$(document).on('click', '.deleteBtn', function () {

    $(".item-row").css("background-color", "transparent");

    $(this).closest("tr").find(".editBtn").hide();
    $(this).closest("tr").find(".saveBtn").hide();
    $(this).closest("tr").find(".cancelEditBtn").show();
    $(this).closest("tr").find(".deleteBtn").hide();
    $(this).closest("tr").find(".confirmBtn").show();

    $('.item-error-msg').remove();
    $('.item-success-msg').remove();
    return false;
});

$(document).on('click', '.confirmBtn', function () {

    $(".item-row").css("background-color", "transparent");

    $(this).closest("tr").find(".editBtn").show();
    $(this).closest("tr").find(".saveBtn").hide();
    $(this).closest("tr").find(".cancelEditBtn").hide();
    $(this).closest("tr").find(".deleteBtn").show();
    $(this).closest("tr").find(".confirmBtn").hide();

    $('.item-error-msg').remove();
    $('.item-success-msg').remove();

    var trObj = $(this).closest("tr");

    var itemID = $(this).closest("tr").find('[name=item_name]').attr('id').substr(5);
    var itemName = $(this).closest("tr").find('[name=item_name]').val();
    ////console.log(itemID);
    ////console.log(itemName);

    $.ajax({
        url: "assets/removeItems.php",
        type: "POST",
        data: {
            "itemID": itemID,
            "itemName": itemName,
        },
        success: function (data) {
            ////console.log(data);
            $.each(JSON.parse(data), function (index, value) {

                if (value.status == "error") {
                    // grab corresponding input element and output error message below
                    $('.item-error-msg').remove();
                    $('#select-month-container').after('<p class="item-error-msg">' + value.msg + '</p>');
                    $('input[name="' + value.field + '"]').css("border", "solid red");

                } else {



                    trObj.fadeTo("slow", 0.00, function () { //fade
                        $(this).slideUp("slow", function () { //slide up
                            $.when($(this).remove()).then(function () {
                                if ($("#items-table .item-row").length == 0) {
                                    $('#items-table #add-item-row').after("<p class='no-items-msg'>No items have been added for this month.</p>");
                                }
                            });
                        });
                    });

                    // clear error messages
                    $('.item-error-msg').remove();
                    $('input').css("border", "1px solid #ced4da");
                    //$('#select-month-container').after('<p class="item-success-msg">' + value.msg + '</p>');
                    // call function to add recent visitors name (and id) to the select button for removal.
                    //updateVisitors();
                    clearFields();

                }
            })



        }, // success function
        error: function () {} // error function
    }); // ajax remove item


    return false;
}); // event confirm delete

$(document).on('click', '.saveBtn', function () {

    $(".item-row").css("background-color", "transparent");

    $('.item-error-msg').remove();
    $('.item-success-msg').remove();
    $('input').css("border", "1px solid #ced4da");

    var trObj = $(this).closest("tr");

    var itemID = $(this).closest("tr").find('[name=item_name]').attr('id').substr(5);
    var itemName = $(this).closest("tr").find('[name=item_name]').val();
    var itemCost = $(this).closest("tr").find('[name=item_cost]').val();
    var userID = $(this).closest("tr").find('[name=userID]').val();
    var optionElement = $(this).closest("tr").find('[name=userID]').find('[value=' + userID + ']');
    var fName = optionElement.text();
    var itemDate = $(this).closest("tr").find('[name=time_created]').val();

    ////console.log(itemCost);

    $.ajax({
        url: "assets/editItems.php",
        type: "POST",
        data: {
            "itemID": itemID,
            "itemName": itemName,
            "itemCost": itemCost,
            "userID": userID,
            "fName": fName,
            "itemDate": itemDate
        },
        success: function (data) {
            ////console.log(data);
            $.each(JSON.parse(data), function (index, value) {

                if (value.status == "error") {
                    // grab corresponding input element and output error message below
                    $('.item-error-msg').remove();
                    $('#select-month-container').after('<p class="item-error-msg">' + value.msg + '</p>');
                    $('input[name="' + value.field + '"]').css("border", "solid red");

                } else {
                    // clear error messages

                    ////console.log(value.data.itemCost);

                    trObj.find('.editSpan.itemName').text(value.data.itemName);
                    trObj.find('.editSpan.cost').text(Number(Math.round(value.data.itemCost + 'e2') + 'e-2'));
                    trObj.find('.editSpan.userID').attr('data-userid', value.data.userID);
                    trObj.find('.editSpan.userID').text(value.data.fName);
                    trObj.find('.editSpan.time').text(value.data.itemTime);

                    trObj.css("background-color", "lightgreen");

                    // hide editable inputs on load
                    $("#items-table .editInput").hide();
                    $("#items-table .editSpan").show();
                    $("#items-table .cancelEditBtn").hide();
                    $("#items-table .saveBtn").hide();
                    $("#items-table .confirmBtn").hide();
                    $("#items-table .editBtn").show();
                    $("#items-table .deleteBtn").show();


                    //$('.item-error-msg').remove();
                    //$('input').css("border", "1px solid #ced4da");
                    //$('#select-month-container').after('<p class="item-success-msg">' + value.msg + '</p>');
                    // call function to add recent visitors name (and id) to the select button for removal.

                    clearFields();
                    //fetchItems();
                }
            }); // each loop

        }, // success function
        error: function () {

        } // error function
    }); // ajax save item
    return false;
});

$(document).on('click', '#item-summary-btn', function () {

    $(".item-row").css("background-color", "transparent");

    if ($("#item-totals-table tbody tr").length || $("#user-owes-table p").length || $(".item-summary-inner p").length) {
        ////console.log("the elements already exist.");
    } else {
        var itemMonthSelect = document.querySelector("#item-month-select");
        $(".item-summary-inner h4").html("Item Summary Totals for " + itemMonthSelect.options[itemMonthSelect.selectedIndex].text);

        $.ajax({
            url: "assets/itemSummary.php",
            data: {
                itemDate: itemMonthSelect.value
            },
            type: "GET",
            dataType: 'json',
            cache: false,
            success: function (data) {
                //console.log(data);
                $(".item-summary-inner").slideDown();
                // item total
                
                var numUsers = data[0].length;
                ////console.log('num users is: ' + numUsers);
                var itemSummaryTableBody = $("#item-totals-table tbody");

                var TotalSpent = 0;
                $.each(data[0], function (index, value) {

                    TotalSpent += value.totalPaid;
                    ////console.log(value.user_id);
                    itemSummaryTableBody.append("<tr><td data-userid=" + value.user_id + ">" + value.fName + "</td><td>$" + Number(Math.round(value.totalPaid + 'e2') + 'e-2').toFixed(2) + "</td></tr>");


                }); // each loop

                if (numUsers > 1) {
                    $("#item-totals-table tbody tr:last-child").after("<tr><td>Total</td><td>$" + Number(Math.round(TotalSpent + 'e2') + 'e-2').toFixed(2) + "</td></tr>");

                    var userOwesTableBody = $("#user-owes-table");

                    $("#item-totals-table").after("<p>Items Total Split by " + numUsers + " Roomies: $" + (Number(Math.round((TotalSpent / numUsers) + 'e2') + 'e-2')).toFixed(2) + "</p>");
                    //console.log(data[1]);
                    $.each(data[1], function (index, value) {

                        userOwesTableBody.append("<tr><td><span data-userIDToPay=" + value.userIDToPay + ">" + value.userToPay + "</span> owes <span data-userIDToBePaid=" + value.userIDToBePaid + ">" + value.userToBePaid + "</span> $" + Number(Math.round(value.amountToPay + 'e2') + 'e-2').toFixed(2) + "</td><td>");

                    }); // each loop
                } else if (numUsers == 1) {
                    $("#item-totals-table tbody tr:last-child").after("<tr><td>Total</td><td>$" + Number(Math.round(TotalSpent + 'e2') + 'e-2').toFixed(2) + "</td></tr>");
                } else {
                    $("#item-totals-table").after("<p>No items were added for this month.</p>");
                }

                // item split dues
                //console.log(data[1]);
            }, // success function
            error: function (error) {
                ////console.log('error abound');
                $(".item-summary-inner").show();
                $("#item-totals-table").after("<p>No items were added for this month.</p>");
            } // error function
        }); // ajax update get

    }

});

// Fetch items to display on browser on page onload
var itemMonthSelect = document.querySelector("#item-month-select");

itemMonthSelect.addEventListener("change", function () {

    $(".item-row").css("background-color", "transparent");

    clearFields();
    $('.item-row').remove();
    $('.item-success-msg').remove();
    $('input').css("border", "1px solid #ced4da");

    $.ajax({
        url: "assets/fetchItems.php",
        data: {
            itemDate: this.value
        },
        type: "GET",
        dataType: 'json',
        cache: false,
        success: function (data) {
            ////console.log(itemMonthSelect.value);
            if (data.length == 0) {
                if (itemMonthSelect.value == today) {
                    //$("#items-table thead tr:last-child").append("<th id='action-table-header'>Action</th>");
                    $('#items-table .no-items-msg').remove();
                    $('#items-table #add-item-row').after("<p class='no-items-msg'>No items have been added for this month.</p>");
                    $('#items-table #add-item-row').show();
                } else {
                    $('#items-table .no-items-msg').remove();
                    $('#items-table #add-item-row').after("<p class='no-items-msg'>No items were added for this month.</p>");
                    $('#items-table #add-item-row').hide();
                }
            } else {
                $('#items-table .no-items-msg').remove();
                $.each(data, function (index, value) {
                    var timeCreatedsubstr = (value.time_created).substring(0, 7);

                    // if time created is less than today date, remove add-item row and append to after tbody.
                    if (timeCreatedsubstr == today) {

                        $('#items-table #add-item-row').show();
                        $('#items-table .no-items-msg').remove();
                        $('.action-column').show();
                        $("#action-table-header").show();

                        $("#items-table #add-item-row").after("<tr class='item-row'><td><span class='editSpan itemName'>" + value.item_name + "</span><input id='item-" + value.item_id + "' type='text' class='form-control editInput' name='item_name' value='" + value.item_name + "'></td><td><span class='editSpan cost'>" + Number(Math.round(value.item_cost + 'e2') + 'e-2').toFixed(2) + "</span><input type='number' step='any' class='form-control editInput cost-input' name='item_cost' value='" + Number(Math.round(value.item_cost + 'e2') + 'e-2').toFixed(2) + "'></td><td><span data-userID ='" + value.user_id + "' class='editSpan userID'>" + value.fName + "</span>" + usersSelect + "</td><td><span class='editSpan time'>" + (value.time_created).substr(0, 10) + "</span><input type='date' onfocus=this.type='date' class='form-control editInput' name='time_created' value='" + (value.time_created).substr(0, 10) + "'></td><td><button class='btn editBtn action-column' type='submit' value='Edit'><span class='fa fa-edit'></span></button><button class='btn saveBtn action-column' type='submit' value='Save'><span class='fa fa-save'></span></button><button class='btn deleteBtn action-column' type='submit' value='Delete'><span class='fa fa-trash'></span></button><button class='btn btn-danger confirmBtn action-column' type='submit' value='Confirm'><span class='fa fa-check'></span></button><button class='btn cancelEditBtn action-column' type='submit' value='Cancel'><span class='fa fa-remove'></span></button></td></tr>");

                        // hide editable inputs on load
                        $("#items-table .editInput").hide();
                        $("#items-table .cancelEditBtn").hide();
                        $("#items-table .saveBtn").hide();
                        $("#items-table .confirmBtn").hide();

                    } else {
                        //$("#action-table-header").detach();
                        $('#items-table #add-item-row').hide();
                        $("#items-table #add-item-row").after("<tr class='item-row'><td>" + value.item_name + "</td><td>" + Number(Math.round(value.item_cost + 'e2') + 'e-2').toFixed(2) + "</td><td>" + value.fName + "</td><td>" + (value.time_created).substr(0, 10) + "</td></tr>");
                        $('.action-column').hide();
                        $("#action-table-header").hide();

                    }
                }) // each loop
            }
        }, // success function
        error: function (error) {
            //console.log("no dataaaaaaaaa");
        } // error function
    }); // ajax update get
}); // event listener change

function fetchItems() {

    $('.item-row').remove();

    $.ajax({
        url: "assets/fetchItems.php",
        data: {
            itemDate: today
        },
        type: "GET",
        dataType: 'json',
        cache: false,
        success: function (data) {

            $(".table-container").slideDown();
            ////console.log(data);
            if (data.length == 0) {
                $('#items-table #add-item-row').after("<p class='no-items-msg'>No items have been added for this month.</p>");
            } else {
                $('#items-table .no-items-msg').remove();
                $.each(data, function (index, value) {

                    $("#items-table #add-item-row").after("<tr class='item-row'><td><span class='editSpan itemName'>" + value.item_name + "</span><input id='item-" + value.item_id + "' type='text' class='form-control editInput' name='item_name' value='" + value.item_name + "'></td><td><span class='editSpan cost'>" + Number(Math.round(value.item_cost + 'e2') + 'e-2').toFixed(2) + "</span><input type='number' step='any' class='form-control editInput cost-input' name='item_cost' value='" + Number(Math.round(value.item_cost + 'e2') + 'e-2').toFixed(2) + "'></td><td><span data-userID ='" + value.user_id + "' class='editSpan userID'>" + value.fName + "</span>" + usersSelect + "</td><td><span class='editSpan time'>" + (value.time_created).substr(0, 10) + "</span><input type='date' onfocus=this.type='date' class='form-control editInput' name='time_created' value='" + (value.time_created).substr(0, 10) + "'></td><td><button class='btn editBtn action-column' type='submit' value='Edit'><span class='fa fa-edit'></span></button><button class='btn saveBtn action-column' type='submit' value='Save'><span class='fa fa-save'></span></button><button class='btn deleteBtn action-column' type='submit' value='Delete'><span class='fa fa-trash'></span></button><button class='btn btn-danger confirmBtn action-column' type='submit' value='Confirm'><span class='fa fa-check'></span></button><button class='btn cancelEditBtn action-column' type='submit' value='Cancel'><span class='fa fa-remove'></span></button></td></tr>");


                    // hide editable inputs on load
                    $("#items-table .editInput").hide();
                    $("#items-table .cancelEditBtn").hide();
                    $("#items-table .saveBtn").hide();
                    $("#items-table .confirmBtn").hide();
                })
            }
        }, // success function
        complete: function (data) {
            //console.log("fetch items ajax is complete");
        }, // complete call back
        error: function (error) {
            //$(".current-info-inner").append("<p class='no-chore-msg'>No Chores Scheduled.</p>");
        } // error function
    }); // ajax update get
}
// Add item ajax post


function clearFields() {
    $("#add-item-row input[name='addItemname']").val("");
    $("#add-item-row input[name='addItemCost']").val("");

    $(".item-summary-inner").slideUp();
    $(".item-summary-inner table tbody tr").remove();
    $(".item-summary-inner table tbody td").remove();
    $(".item-summary-inner h4").html("");
    $(".item-summary-inner p").remove();
    $("#user-owes-table td").remove();
    $("#user-owes-table tr").remove();

}

//}); // document ready
