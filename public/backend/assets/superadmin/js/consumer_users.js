const mediaObj = import('../../js/common/uploadmedia.js');

var ConsumerUsers = (function () {
    var manage = function () {
        $("#importUsers").hide();
        var table = $("#tblConsumerUsers").DataTable({
            processing: true,
            serverSide: true,
            ajax: superadmin_url + "/users/consumer-users",
            fnDrawCallback: function () {
                $(".status_switch").bootstrapSwitch();
            },
            order: [[0, "desc"]],
            columns: [
                { data: "id", name: "id" },
                { data: "name", name: "name" },
                { data: "email", name: "email" },
                { data: "phone", name: "phone" },
                { data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    className: "text-left",
                    visible: false,
                },
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: true, className: "text-left" },
                { targets: 4, searchable: true, className: "text-left" },
                { targets: 5, searchable: true, className: "text-center" },
                { targets: 6, searchable: false, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-left",
                },
            ],
        });

        $("#exportConsumerUser").click(function () {
            $("#exportConsumerUser").html("Loading...").addClass("disabled");
            var url =
                superadmin_url + "/users/consumer-users/exportConsumerUsers";
            $.ajax({
                type: "POST",
                url: url,
                data: { action: "exportConsumerUser" },
                dataType: "binary",
                xhrFields: {
                    responseType: "blob",
                },
                success: function (result) {
                    $("#exportConsumerUser")
                        .html('<i class="fa fa-download mr-2"></i> Export')
                        .removeClass("disabled");
                    //console.log(result);
                    $("#loader").hide();
                    var link = document.createElement("a");
                    link.download = "consumer_users.csv";
                    link.href = $("#downloadCsv").attr("data-href");
                    link.click();
                },
            });
        });
    };

    /*** Change status code start here ***/
    $("body").on(
        "switchChange.bootstrapSwitch",
        'input[class="status_switch"]',
        function (event, state) {
            var id = $(this).attr("data-id");
            var url = superadmin_url + "/users/consumer-users/changeStatus/";
            if (state) {
                $("#status_" + id).val(1);
            } else {
                $("#status_" + id).val(0);
            }
            $("#statusForm").attr("action", url);
            $("#StatusModal").modal({
                backdrop: "static",
                keyboard: false,
            });
            $("#user_id").val(id);
            if (state) {
                $("#new_status").val(1);
            } else {
                $("#new_status").val(0);
            }
        }
    );

    $("body").on("click", "#user_yesBtn", function () {
        $("#statusForm").submit();
    });

    $("body").on("click", "#user_closeBtn, #StausModalClose", function () {
        var id = $("#user_id").val();
        if ($("#status_" + id).val() == 1) {
            $("#status_" + id).bootstrapSwitch("state", false);
        } else {
            $("#status_" + id).bootstrapSwitch("state", true);
        }
    });
    /*** Change status code end here ***/

    var add = function () {
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch("state", $(this).prop("checked"));
        });

        $(".active-status").on(
            "switchChange.bootstrapSwitch",
            function (event, state) {
                if (state) {
                    $("#status").val(1);
                } else {
                    $("#status").val(0);
                }
            }
        );

        var form = $("form[name='frmAddConsumerUser']");
        $("#btnSubmit").click(function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        minlength: 4,
                        maxlength: 40,
                    },
                    user_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 50,
                        noSpace: true
                    },
                    phone: {
                        required: true,
                        minlength: 10,
                        maxlength: 15,
                        remote: superadmin_url + "/settings/validateUserPhone",
                    },
                    /* city: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					state: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					country: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					zipcode: {
						required: true,
						minlength : 4,
						maxlength: 10,
					}, */
                    avtar: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                        remote: superadmin_url + "/settings/validateUserEmail",
                    },
                    password: {
                        required: true,
                        minlength: 6,
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#password",
                    },
                },
                messages: {
                    name: {
                        required: "Please enter consumer name.",
                        minlength:
                            "Consumer name minimum length should be 4 character.",
                        maxlength:
                            "Consumer name maximum length should be 40 character.",
                    },
                    user_name: {
                        required: "Please enter username.",
                        minlength:
                            "User name minimum length should be 4 character.",
                        maxlength:
                            "User name maximum length should be 50 character.",
                    },
                    phone: {
                        required: "Please enter phone number.",
                        minlength:
                            "Phone number minimum length should be 10 digit.",
                        maxlength:
                            "Consumer name maximum length should be 15 digit.",
                        remote: "Phone number already exist.",
                    },
                    /* city: {
						required:	'Please enter city.',
						minlength:	'City minimum length should be 3 character.',
						maxlength:	'City maximum length should be 50 character.',
					},
					state: {
						required:	'Please enter state.',
						minlength:	'State minimum length should be 3 character.',
						maxlength:	'State maximum length should be 50 character.',
					},
					country: {
						required:	'Please enter country.',
						minlength:	'Country minimum length should be 3 character.',
						maxlength:	'Country maximum length should be 50 character.',
					},
					zipcode: {
						required:	'Please enter zipcode.',
						minlength:	'Zipcode minimum length should be 4 character.',
						maxlength:	'Zipcode maximum length should be 10 character.',
					}, */
                    avtar: {
                        required: "Please select avtar.",
                    },
                    email: {
                        required: "Please enter email address.",
                        email: "Please enter correct email address.",
                        remote: "Email already exist.",
                    },
                    password: {
                        required: "Please enter password.",
                        minlength:
                            "Password minimum length should be 6 character.",
                    },
                    confirm_password: {
                        required: "Please enter confirm password.",
                        equalTo:
                            "Password and confirm password does not match.",
                    },
                },
            });
            if (form.valid()) {
                form.submit();
            }
        });

        /** Change icon code start here **/
        $("#avtar").hide();
        $("body").on("click", "#pf_edit, #pf_upload_icon", function () {
            $("#avtar").trigger("click");
        });
        /** Change icon code end here **/
    };

    /** Edit record code start here **/
    var edit = function () {
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch("state", $(this).prop("checked"));
        });

        $(".active-status").on(
            "switchChange.bootstrapSwitch",
            function (event, state) {
                if (state) {
                    $("#status").val(1);
                } else {
                    $("#status").val(0);
                }
            }
        );

        /* $("body").on('click', '#btnSubmit', function () {
			return validateForm();
		}); */

        var form = $("form[name='frmEditConsumerUser']");
        var userId = $("#user_id").val();
        $("#btnSubmit").click(function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        minlength: 4,
                        maxlength: 40,
                    },
                    user_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 50,
                        noSpace: true
                    },
                    phone: {
                        required: true,
                        minlength: 10,
                        maxlength: 15,
                        remote:
                            superadmin_url +
                            "/settings/validateUserPhone?id=" +
                            userId,
                    },
                    /* city: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					state: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					country: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					zipcode: {
						required: true,
						minlength : 4,
						maxlength: 10,
					}, */
                    avtar: {
                        required: function (element) {
                            if (
                                $("#avtar").val() == "" &&
                                $("#old_avtar").val() != ""
                            ) {
                                return false;
                            } else {
                                return true;
                            }
                        },
                    },
                    email: {
                        required: true,
                        email: true,
                        remote:
                            superadmin_url +
                            "/settings/validateUserEmail?id=" +
                            userId,
                    },
                    /* password : {
						required: true,
						minlength : 6
					},
					confirm_password : {
						required: true,
						equalTo : "#password"
					} */
                },
                messages: {
                    name: {
                        required: "Please enter consumer name.",
                        minlength:
                            "Consumer name minimum length should be 4 character.",
                        maxlength:
                            "Consumer name maximum length should be 40 character.",
                    },
                    user_name: {
                        required: "Please enter username.",
                        minlength:
                            "User name minimum length should be 4 character.",
                        maxlength:
                            "User name maximum length should be 50 character.",
                    },
                    phone: {
                        required: "Please enter phone number.",
                        minlength:
                            "Phone number minimum length should be 10 digit.",
                        maxlength:
                            "Consumer name maximum length should be 15 digit.",
                        remote: "Phone number already exist.",
                    },
                    /* city: {
						required:	'Please enter city.',
						minlength:	'City minimum length should be 3 character.',
						maxlength:	'City maximum length should be 50 character.',
					},
					state: {
						required:	'Please enter state.',
						minlength:	'State minimum length should be 3 character.',
						maxlength:	'State maximum length should be 50 character.',
					},
					country: {
						required:	'Please enter country.',
						minlength:	'Country minimum length should be 3 character.',
						maxlength:	'Country maximum length should be 50 character.',
					},
					zipcode: {
						required:	'Please enter zipcode.',
						minlength:	'Zipcode minimum length should be 4 character.',
						maxlength:	'Zipcode maximum length should be 10 character.',
					}, */
                    avtar: {
                        required: "Please select avtar.",
                    },
                    email: {
                        required: "Please enter email address.",
                        email: "Please enter correct email address.",
                        remote: "Email already exist.",
                    },
                    /* password : {
						required: 'Please enter password.',
						minlength : 'Password minimum length should be 6 character.'
					},
					confirm_password : {
						required : 'Please enter confirm password.',
						equalTo : "Password and confirm password does not match."
					} */
                },
            });
            if (form.valid()) {
                form.submit();
            }
        });

        /** Save consumer profile picture code start here **/
        $("#avtar").hide();
        $("body").on("click", "#pf_edit, #pf_upload_icon", function () {
            $("#avtar").trigger("click");
        });
        /** Save consumer profile picture code end here **/
    };
    /** Edit record code end here **/

    /*** Common javascript code start here  **/
    $("#avtar").on("change", function () {
        $("#old_avtar").val("");
        $("#avtar-error").html("");
        $("#profile-photo-img").hide();
        $("#profile-photo-preview").show();
        $('#avtarImg').val("");
        var imgPath = $(this)[0].value;
        var extn = imgPath
            .substring(imgPath.lastIndexOf(".") + 1)
            .toLowerCase();
        if (extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof FileReader != "undefined") {
                var uploadedFile = document.getElementById("avtar");
                var size = parseFloat(
                    uploadedFile.files[0].size / 1024
                ).toFixed(2);
                if (size < 1024) {
                    var reader = new FileReader();
                    reader.readAsDataURL($(this)[0].files[0]);
                    var file = $(this)[0].files[0] || null;
                    reader.onload = function (e) {
                        mediaArr[1] = {};
                        mediaArr[1] = URL.createObjectURL(file);
                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function () {
                            var height = this.height;
                            var width = this.width;
                            if (width < 100 || width > 800) {
                                callToaster(
                                    "error",
                                    "Avtar width should be beetween 100px to 800px."
                                );
                                $("#profile-photo-img").show();
                                $("#profile-photo-preview").html("");
                                $("#avtar").val("");
                                $("#old_avtar").val("");
                                return false;
                            } else {
                                var editBtn =
                                    '<a href="javascript:void(0);" id="pf_edit" class="hovericon lc_edit"><i class="fa fa-pencil"></i></a><a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a><a href="javascript:void(0);" id="pf_crop" class="hovericon cropMedia" data-id="1"><i class="fa fa-scissors"></i></a>';
                                $("#profile-photo-preview").html(
                                    "<img src='" +
                                        e.target.result +
                                        "' id='avtar_placeholder' width='185px' height='185px'>" +
                                        editBtn
                                );
                            }
                        };
                    };
                } else {
                    callToaster(
                        "error",
                        "Please select a file less than 1 MB."
                    );
                    $("#profile-photo-img").show();
                    $("#profile-photo-preview").html("");
                    $("#avtar").val("");
                    $("#old_avtar").val("");
                    return false;
                }
            } else {
                alert("This browser does not support FileReader.");
                return false;
            }
        } else {
            callToaster("error", "Please upload a JPG or PNG image.");
            $("#profile-photo-img").show();
            $("#profile-photo-preview").html("");
            $("#avtar").val("");
            $("#old_avtar").val("");
            return false;
        }
    });

    $("body").on("click", "#pf_delete", function () {
        $("#profile-photo-img").show();
        $("#profile-photo-preview").hide();
        $("#avtar").val("");
        $("#old_avtar").val("");
        $('#avtarImg').val("");
    });
    /*** Common javascript code end here  **/

    /** View details code start here **/
    var view = function () {
        $("body").on("click", ".viewRecord", function () {
            $(".full_details").html("");
            $("#viewDetails").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            submitcall(url, { object_id: id }, function (output) {
                $(".full_details").html(output);
            });
        });
    };
    /** View details code end here **/

    /** Delete record code start here **/
    var delete_record = function () {
        $("body").on("click", ".delete", function () {
            var id = $(this).attr("data-id");
            var url = superadmin_url + "/users/consumer-users/destroy/" + id;
            $("#deleteForm").attr("action", url);
            $("#DeleteModal").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
        });
        $("body").on("click", "#yesBtn", function () {
            $("#deleteForm").submit();
        });
    };
    /** Delete record code end here **/

    /** Import csv file for consumer user code start here **/
    $("body").on("click", "#importConsumerUser", function () {
        $("#csv_file").trigger("click");
    });

    $("#csv_file").on("change", function () {
        $("#importConsumerUser").html("Loading...").addClass("disabled");
        $("#importUsers").submit();
    });

    $("#importUsers").on("submit", function (e) {
        e.preventDefault(); //form will not submitted
        $.ajax({
            url: $("#importUsers").attr("action"),
            method: "POST",
            data: new FormData(this),
            contentType: false, // The content type used when sending data to the server.
            cache: false, // To unable request pages to be cached
            processData: false, // To send DOMDocument or non processed data file it is set to false
            success: function (data) {
                $("#importConsumerUser")
                    .html('<i class="fa fa-file-excel-o mr-2"></i> Import')
                    .removeClass("disabled");
                $("#importUsers")[0].reset();

                if (data == 1) {
                    Swal.fire({
                        title: "Success!",
                        icon: "success",
                        text: "Consumer user data has been imported successfully!",
                        type: "success",
                        timer: 5000,
                    }).then((okay) => {
                        if (okay) {
                            window.location.href =
                                superadmin_url + "/users/consumer-users/";
                        }
                    });
                }
                if (data == 0) {
                    Swal.fire({
                        title: "Error!",
                        icon: "error",
                        text: "File too large. File must be less than 2MB.",
                        type: "error",
                        timer: 5000,
                    }).then((okay) => {
                        if (okay) {
                            window.location.href =
                                superadmin_url + "/users/consumer-users/";
                        }
                    });
                }

                if (data == 3) {
                    Swal.fire({
                        title: "Error!",
                        icon: "error",
                        text: "Invalid File Extension.",
                        type: "error",
                        timer: 5000,
                    }).then((okay) => {
                        if (okay) {
                            window.location.href =
                                superadmin_url + "/users/consumer-users/";
                        }
                    });
                }
            },
        });
    });
    /** Import csv file for consumer user code end here **/
    function loadAutocomplete(superadmin_url) {
        mediaObj.then(media => {
            media.initAutocomplete(superadmin_url);
        });
    }

    return {
        init: function () {
            manage();
            view();
            delete_record();
        },
        add: function () {
            window.mediaArr = {};
            add();
            loadAutocomplete(superadmin_url);

        },
        edit: function () {
            window.mediaArr = {};
            edit();
            loadAutocomplete(superadmin_url);
        },
    };
})();
