function manage_error(xhr, ajaxOptions, thrownError) {
    if (xhr.status == 401) {
        // alert("401");
        location.reload();
    }

    console.log(xhr.status);
    console.log(xhr.responseText);
    console.log(thrownError);
}


function show_swal_success() {
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
    // alert("HELLO");
    // return;
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
                // show_swal_error();
                setTimeout(function () {
                    location.reload();
                }, 1500);
            }
        },
        fail: function (data) {
            show_swal_error();
        },
        error: manage_error,
    });
    // AJAX END
}

// $(document).on("submit", "#add-customer", handle_submission);
$('#add_customer').submit(handle_submission);
