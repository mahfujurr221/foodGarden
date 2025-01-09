function manage_error(xhr, ajaxOptions, thrownError) {
    if (xhr.status == 401) {
        // alert("401");
        location.reload();
    }

    console.log(xhr.status);
    console.log(xhr.responseText);
    console.log(thrownError);
}

$(document).on("click", ".edit", function (e) {
    // alert("CLIXCKED");
    // login_check();
    // Set the title right
    // e.preventDefault();
    // alert("HELLO");
    // return;
    $("#edit .modal-title").text(this.id);
    // var id=this.id;
    $("#edit .dynamic-content").html(""); // leave it blank before ajax call
    $("#edit .modal-loader").show(); // load ajax loader
    var url = $(this).attr("href");
    $.ajax({
        // url: "/back/subject.edit/"+this.id,
        url: url,
        type: "GET",
        dataType: "html",
        error: manage_error,
    })
        .done(function (data) {
            $("#edit .dynamic-content").html("");
            $("#edit .dynamic-content").html(data); // load response
            $("#edit .modal-loader").hide(); // hide ajax loader
        })
        .fail(function (data) {
            // console.log(data.statusCode);
            // $('#edit .dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
            // $('#edit .modal-loader').hide();
        });
});

function show_swal_success() {
    // Swal.fire({
    //     position: 'center',
    //     type: "success",
    //     title: "Successful!",
    //     showConfirmButton: false,
    //     timer: 1500
    // });
    swal({
        position: "center",
        type: "success",
        icon: "success",
        text: "Successfull",
        timer: 1500,
        buttons: false,
    });
}

function show_swal_error() {
    // Swal.fire({
    //     position: 'center',
    //     type: "error",
    //     title: "OOps Something Went Wrong!",
    //     showConfirmButton: false,
    //     timer: 1500
    // });

    swal({
        position: "center",
        type: "error",
        icon: "error",
        text: "Oops Something Went Wrong!",
        timer: 1500,
        buttons: false,
    });
}

function clear_error(form) {
    // $("#create_form .errors").html("");
    form.children(".errors").html("");
}

function clear_form(form) {
    clear_error(form);
    form.trigger("reset");
}

function handle_submission(e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this);

    clear_error(form);

    var url = form.attr("action");

    // AJAX START
    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function (data) {
            // console.log(data);
            if (data.hasOwnProperty("error")) {
                var error_data = "";
                $.each(data.error, function (key, value) {
                    error_data +=
                        '<div class="alert alert-danger">' + value + "</div>";
                });
                form.find(".errors").html(error_data);
            } else {
                //sweet ae
                clear_form(form);
                show_swal_success();

                $("#edit").modal("toggle");
                
                // show_swal_error();
                // setTimeout(function () {
                //     location.reload();
                // }, 1500);
            }
        },
        fail: function (data) {
            show_swal_error();
        },
        error: manage_error,
    });
    // AJAX END
}

$(document).on("submit", "#edit_form", handle_submission);
