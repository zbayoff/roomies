$('document').ready(function () {

    var roomieDashboardSelect = document.querySelector("#roomie-dashboard-select");
    // make ajax request to load data from SQL table users when user changes value of select Roomie option
    roomieDashboardSelect.addEventListener("change", function () {
        $(".choreData").remove();
        $(".visitorData").remove();
        // ajax get request, directs to userData.php passing the user's id
        $.ajax({
            url: 'assets/userData.php',
            data: {
                userid: this.value
            },
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                // Each loop through chores
                ////console.log(data.user.chores);
                $('.roomie-card h3').html("Roomie: " + data.user.fName + "");

                if ($.isEmptyObject(data.user.chores)) {
                    $('.chore-section .chore-inner').append("<p class='choreData'>No Chores Scheduled.</p>");
                } else {
                    $.each(data.user.chores, function (index, value) {
                        $('.chore-section .chore-inner').append("<p class='choreData'>" + value.choreName + ": " + value.choreDate + "</p>");
                    })
                }

                // Each loop through visitors
                if ($.isEmptyObject(data.user.visitors)) {
                    $('.visitor-section .visitor-inner').append("<p class='visitorData'>No Visitors Scheduled.</p>");
                } else {
                    $.each(data.user.visitors, function (index, value) {
                        ////console.log(value);
                        $('.visitor-section .visitor-inner').append("<p class='visitorData'>" + value.visitorName + " from " + value.arrivalDate + " to " + value.leaveDate + " (" + value.numNights + " nights)</p>");
                    });
                }

            } // success function
        }); // ajax get call
    }); // on change event function

    updateChoreDates();
    
    function updateChoreDates() {
        $.ajax({
            url: "assets/updateChores.php",
            type: "GET",
            dataType: 'json',
            cache: false,
            success: function (data) {
                ////console.log("chores updated");
            }, // success function
            complete: function (data){
                getUserData();
            },
            error: function (error) {
//                //console.log("There was ajax error: ");
//                //console.log(error);
            } // error function
        }); // ajax get
    }
    
    updateVisitorDates();
    
    function updateVisitorDates() {
        $.ajax({
            url: "assets/updateVisitors.php",
            type: "GET",
            dataType: 'json',
            cache: false,
            success: function (data) {
                //console.log("visitors updated");
            }, // success function
            complete: function (data){
                //getUserData();
            },
            error: function (error) {
//                //console.log("There was ajax error: ");
//                //console.log(error);
            } // error function
        }); // ajax get
    }
    

    function getUserData() {

        $.ajax({
            url: 'assets/userData.php',
            data: {
                userid: roomieDashboardSelect.value
            },
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                ////console.log(data);
                // Each loop through chores
                $('.roomie-card h3').html("Roomie: " + data.user.fName + "");

                if ($.isEmptyObject(data.user.chores)) {
                    $('.chore-section .chore-inner').append("<p class='choreData'>No Chores Scheduled.</p>");
                } else {
                    $.each(data.user.chores, function (index, value) {
                        $('.chore-section .chore-inner').append("<p class='choreData'>" + value.choreName + ": " + value.choreDate + "</p>");
                    })
                }


                // Each loop through visitors
                if ($.isEmptyObject(data.user.visitors)) {
                    $('.visitor-section .visitor-inner').append("<p class='visitorData'>No Visitors Scheduled.</p>");
                } else {
                    $.each(data.user.visitors, function (index, value) {
                        ////console.log(value);
                        $('.visitor-section .visitor-inner').append("<p class='visitorData'>" + value.visitorName + " from " + value.arrivalDate + " to " + value.leaveDate + " (" + value.numNights + " nights)</p>");
                    });
                }

            } // success function
        }); // ajax get call


    }


}); //document ready
