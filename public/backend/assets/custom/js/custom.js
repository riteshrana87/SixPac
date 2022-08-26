function handleDelete() {
    $("body").on("click", ".btndelete", function () {
        if ($(this).attr("data-id") != "" && $(this).attr("data-url") != "") {
            submitcall(
                $(this).attr("data-url"),
                { id: $(this).attr("data-id"), _token: csrf_token },
                function (output) {
                    $("#my_delete_model").modal("hide");
                    handleAjaxResponse(output);
                }
            );
        }
    });
}

function ajaxcall(url, data, callback) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function (result) {
            callback(result);
        },
    });
}

function submitcall(url, data, callback) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function (result) {
            callback(result);
        },
    });
}

function handleAjaxFormSubmit(form, type) {
    /* alert($(form).serialize()); */
    /* $('input[type=file]')[0].files[0].name; */

    if (typeof type === "undefined") {
        submitcall(
            $(form).attr("action"),
            $(form).serialize(),
            function (output) {
                handleAjaxResponse(output);
            }
        );
    } else if (type === true) {
        var options = {
            resetForm: false, // reset the form after successful submit
            success: function (output) {
                handleAjaxResponse(output);
            },
        };
        $(form).ajaxSubmit(options);
    }
    return false;
}

function handleAjaxResponse(output) {
    //output = JSON.parse(output);
    if (output.message != "") {
        toastr.success(output.status, output.message, "");
        $(".btn-sub").prop("disabled", true);
    }
    if (typeof output.redirect !== "undefined" && output.redirect != "") {
        setTimeout(function () {
            window.location.href = output.redirect;
        }, 500);
    }
    if (typeof output.jscode !== "undefined" && output.jscode != "") {
        eval(output.jscode);
    }

    if (typeof output.reload !== "undefined" && output.reload != "") {
        setTimeout(function () {
            window.location = window.location.href;
        }, 500);
    }
}

function handleFormValidate(form, rules, messages, submitCallback, ckeditor) {
    var error = $(".alert-danger", form);
    var success = $(".alert-success", form);
    if (ckeditor) {
        var ignor = [];
    } else {
        var ignor = ":hidden";
    }
    if ($().validate) {
        form.validate({
            errorElement: "div", //default input error message container
            errorClass: "help-block", // default input error message class
            focusInvalid: true, // do not focus the last invalid input
            ignore: ignor,
            rules: rules,
            messages: messages,
            invalidHandler: function (event, validator) {
                //display error alert on form submit
                success.hide();
                error.show();
            },
            showErrors: function (errorMap, errorList) {
                if (typeof errorList[0] != "undefined") {
                    var position = $(errorList[0].element).offset().top - 70;
                    $("html, body").animate(
                        {
                            scrollTop: position,
                        },
                        300
                    );
                }
                this.defaultShowErrors(); // keep error messages next to each input element
            },

            highlight: function (element) {
                // hightlight error inputs
                $(element).closest(".form-group").addClass("has-error"); // set error class to the control group
            },
            unhighlight: function (element) {
                // revert the change done by hightlight
                $(element).closest(".form-group").removeClass("has-error"); // set error class to the control group
            },

            errorPlacement: function (error, element) {
                if (
                    element.is("input[type=checkbox]") ||
                    element.is("input[type=radio]")
                ) {
                    var controls = element.closest('div[class*="col-"]');
                    if (controls.find(":checkbox,:radio").length > 1)
                        controls.append(error);
                    else error.insertAfter(element.nextAll(".lbl:eq(0)").eq(0));
                } else if (element.is(".select2")) {
                    error.insertAfter(
                        element.siblings('[class*="select2-container"]:eq(0)')
                    );
                } else if (element.is(".chosen-select")) {
                    error.insertAfter(
                        element.siblings('[class*="chosen-container"]:eq(0)')
                    );
                } else error.insertAfter(element.parent());
            },
            success: function (label) {
                label.closest(".form-group").removeClass("has-error"); // set success class to the control group
            },
            submitHandler: function (form) {
                if (
                    typeof submitCallback !== "undefined" &&
                    typeof submitCallback == "function"
                ) {
                    submitCallback(form);
                } else {
                    CKupdate();
                    handleAjaxFormSubmit(form, true);
                }
                return false;
            },
        });
    }
}

$("body").on("keyup change", ".req", function () {
    var fieldId = $(this).attr("id");
    $("#error-" + fieldId).html("");
});

$(document).mouseup(function (e) {
    var container = $(".profile-notification");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
        $(".user-profile").removeClass("active");
    }
});

/**** Disable HTML tag for field code start ****/
// $("input,textarea,select").keyup(function (e) {
$("body").on("keyup", "input,textarea,select", function (e) {
    var reg = /<(.|\n)*?>/g;
    if (reg.test($(this).val()) == true) {
        callToaster("error", "HTML/Java Script tags are not allowed.");
        $(this).val("");
        return false;
    }
    e.preventDefault();
});

// $(".onlynumber").keypress(function (event) {
$("body").on("keypress", ".onlynumber", function (event) {
    if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
        event.preventDefault(); //stop character from entering input
    }
});

// $(".onlyalpha").keypress(function (e) {
$("body").on("keypress", ".onlyalpha", function (e) {
    var regex = new RegExp("^[a-zA-Z]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    } else {
        e.preventDefault();
        return false;
    }
});

/**** Disable links for text field and text area start ****/
// $('input,textarea,select').keyup(function(e){
// 	var message = $('.input,textarea').val();
// 	if(/(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($(this).val())){
// 		callToaster('error','Links are not allowed.');
// 		$(this).val("");
// 		e.preventDefault();
// 	}
// 	else if (/^[a-zA-Z0-9\-\.]+\.(com|org|net|mil|edu|COM|ORG|NET|MIL|EDU)$/i.test($(this).val())) {
// 		callToaster('error','Links are not allowed.');
// 		$(this).val("");
// 		e.preventDefault();
// 	}
// });

function checkAlphabets(inputtxt) {
    if (inputtxt != "") {
        var letters = "/^[A-Za-z]+$/";
        if (inputtxt.value.match(letters)) {
            return true;
        } else {
            callToaster("error", "Please enter alphabet characters only.");
            return false;
        }
    }
}

// $(".numerordecimal").keyup(function () {
$("body").on("keyup", ".numerordecimal", function () {
    this.value = this.value.replace(/[^0-9\.]/g, "");
});

// $(".alphanum").keyup(function (e) {
$("body").on("keyup", ".alphanum", function (e) {
    if (this.value.match(/[^a-zA-Z0-9 ]/g)) {
        this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, "");
    }
});

// $(".characterlimit").keyup(function () {
$("body").on("keyup", ".characterlimit", function () {
    var field_id = $(this).attr("id");
    var text_max = $(this).attr("max-character");
    var text_length = $(this).val().length;
    var text_remaining = text_max - text_length;
    $("#cm_" + field_id).html(text_length + " / " + text_max);
    if (text_length === text_max) {
        return false;
    }
});

// $(".USphone").on("keydown keyup", function () {
$("body").on("keydown keyup", ".USphone", function () {
    var numbers = $(this).val().replace(/\D/g, ""),
        char = { 0: "(", 3: ") ", 6: "-" };
    var usNumber = "";
    for (var i = 0; i < numbers.length; i++) {
        usNumber += (char[i] || "") + numbers[i];
        $(this).val(usNumber);
    }
});

// $(".alphaNotAllow").on("keydown keyup", function (e) {
$("body").on("keydown keyup", ".alphaNotAllow", function (e) {
    var regExp = /[a-z]/i;
    var value = String.fromCharCode(e.which) || e.key;

    if (regExp.test(value)) {
        e.preventDefault();
        return false;
    }
});

/**** Disable links for text field and text area end ****/
function postResetVideoFile() {
    $("#video_file-error").html("");
    $("#mp4file-name").html("Upload a file");
    $("#btnUploadVideo").html("Upload .mp4 file");
    $("#video_duration").val("");
    $("#video_file").val("");
    $("#video_preview").addClass("hide");
    $("#video_preview").hide();
    if ($("#image_or_video").val() == 1) {
        $("#image_or_video").val(2);
    } else {
        $("#image_or_video").val(1);
    }
    return false;
}

function postResetPhotoFile() {
    $("#post-photo-img").show();
    $("#post_error").html("");
    $("#post-photo-preview").html("");
    $("#photo").val("");
    $("#old_icon").val("");
    if ($("#image_or_video").val() == 1) {
        $("#image_or_video").val(2);
    } else {
        $("#image_or_video").val(1);
    }
    return false;
}

/** Sweet alert dialog code start here **/
function callToaster(alertType, alertDescription) {
    Swal.fire({
        title: alertDescription,
        type: alertType,
        icon: "error",
        showCancelButton: false,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "OK",
        cancelButtonText: "OK",
        //closeOnConfirm: true,
        //closeOnCancel: true,
        allowOutsideClick: false,
    });
    // sweetAlert("Error", alertDescription, alertType);
}
/** Sweet alert dialog code end here **/

/** AJAX: Get state from country id code start here **/
$("body").on("change", "#country", function () {
    $("#state").html("");
    var id = $(this).val();
    var url = $(this).attr("data-url");
    submitcall(url, { country_id: id }, function (output) {
        $("#state").html(output);
        $("#city").html('<option value="">Select City</option>');
    });
});
/** AJAX: Get state from country id code end here **/

/** AJAX: Get state from country id code start here **/
$("body").on("change", "#state", function () {
    $("#city").html("");
    var c_id = $("#country").val();
    var s_id = $(this).val();
    var url = $(this).attr("data-url");
    submitcall(url, { country_id: c_id, state_id: s_id }, function (output) {
        $("#city").html(output);
    });
});
/** AJAX: Get state from country id code end here **/

/** Phone number format code start here **/

//$("#yourphone2").usPhoneFormat();
/** Phone number format code end here **/
function toTitleCase(str) {
    var lcStr = str.toLowerCase();
    return lcStr.replace(/(?:^|\s)\w/g, function (match) {
        return match.toUpperCase();
    });
}

function showToaster(type, messages) {
    $.toast({
        heading: type.charAt(0).toUpperCase() + type.slice(1),
        text: messages,
        showHideTransition: 'fade',
        position: 'top-right',
        icon: type,
        loaderBg:'#ffffff',
        hideAfter: 5000
    });
}
