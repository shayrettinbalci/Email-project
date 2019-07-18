

// Use jquery ajax for sending email

$("#send-button").click(function (event) {
    event.preventDefault();
    var form = $('#email-form')[0];
    var data = new FormData(form);
    $("#email-form").prop("disabled", true);
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "index.php?r=email/send-email",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data) {
            console.log("SUCCESS : ", data);
            $("#email-form").prop("disabled", false);
        },
        error: function (e) {
            console.log("ERROR : ", e);
            $("#email-form").prop("disabled", false);
        }
    });
});
