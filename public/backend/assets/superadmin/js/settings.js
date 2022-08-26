const mediaObj = import('../../js/common/uploadmedia.js');

var Settings = (function () {
    $("#date_of_birth")
        .datepicker({
            format: "dd/mm/yyyy",
            endDate: "-18y",
        })
        .on("changeDate", function (e) {
            $(this).datepicker("hide");
        });

    var editProfile = function () {
        var form = $("form[name='frmEditProfile']");
        var user_id = $("#user_id").val();

        $("#btnSaveProfile").click(function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    name: {
                    	required: true,
                    	minlength : 3,
                    	maxlength: 40,
                    },
                    user_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 50,
                        noSpace: true
                    },
                    email: {
                        required: true,
                        email: true,
                        remote:
                            superadmin_url +
                            "/settings/validateUserEmail?id=" +
                            user_id,
                    },
                    phone: {
                        required: true,
                        minlength: 10,
                        maxlength: 15,
                        remote:
                            superadmin_url +
                            "/settings/validateUserPhone?id=" +
                            user_id,
                    },
                    date_of_birth: {
                        required: true,
                    },
                    profile_photo: {
                        required: function (element) {
                            if (
                                $("#profile_photo").val() == "" &&
                                $("#pf_img").val() != ""
                            ) {
                                return false;
                            } else {
                                return true;
                            }
                        },
                    },
                },
                messages: {
                    name: {
                    	required:	'Please enter your name.',
                    	minlength:	'Your name minimum length should be 3 character.',
                    	maxlength:	'Your name maximum length should be 40 character.',
                    },
                    user_name: {
                        required: "Please enter username.",
                        minlength:
                            "User name minimum length should be 4 character.",
                        maxlength:
                            "User name maximum length should be 50 character.",
                    },
                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter correct email address.",
                        remote: "Email already exist.",
                    },
                    phone: {
                        required: "Please enter your phone number.",
                        minlength:
                            "Phone number minimum length should be 10 digit.",
                        maxlength:
                            "Admin name maximum length should be 15 digit.",
                        remote: "Phone number already exist.",
                    },
                    profile_photo: {
                        required: "Please select your avtar.",
                    },
                    date_of_birth: {
                        required: "Please select your birth date.",
                    },
                },
            });
            if (form.valid()) {
                form.submit();
            }
        });

        /** Save admin profile picture code start here **/
        $("#profile_photo").hide();
        $("body").on("click", "#pf_edit, #pf_upload_icon", function () {
            $("#profile_photo").trigger("click");
        });
        $("#profile_photo").on("change", function () {
            $("#profile_photo-error").html("");
            $("#pf_img").val("");
            $("#profile-photo-img").hide();
            $("#profile-photo-preview").show();
            var imgPath = $(this)[0].value;
            var extn = imgPath
                .substring(imgPath.lastIndexOf(".") + 1)
                .toLowerCase();
            if (extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof FileReader != "undefined") {
                    var uploadedFile = document.getElementById("profile_photo");
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
                                        "Profile photo width should be beetween 100px to 800px."
                                    );
                                    $("#profile-photo-img").show();
                                    $("#profile-photo-preview").html("");
                                    $("#profile_photo").val("");
                                    $("#pf_img").val("");
                                    return false;
                                } else {
                                    var editBtn =
                                        '<a href="javascript:void(0);" id="pf_edit" class="hovericon lc_edit"><i class="fa fa-pencil"></i></a><a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a><a href="javascript:void(0);" id="pf_crop" class="hovericon cropMedia" data-id="1"><i class="fa fa-scissors"></i></a>';
                                    $("#profile-photo-preview").html(
                                        "<img src='" +
                                            e.target.result +
                                            "' id='profile_photo_placeholder' width='185px' height='185px'>" +
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
                        $("#profile_photo").val("");
                        $("#pf_img").val("");
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
                $("#profile_photo").val("");
                $("#pf_img").val("");
                return false;
            }
        });

        $("body").on("click", "#pf_delete", function () {
            $("#profile-photo-img").show();
            $("#profile-photo-preview").hide();
            $("#profile_photo").val("");
            $("#pf_img").val("");
        });
        /** Save admin profile picture code end here **/
    };

    var changePassword = function () {
        $.validator.addMethod(
            "nowhitespace",
            function (value, element) {
                return this.optional(element) || /^\S+$/i.test(value);
            },
            "Blank space not allowed."
        );

        $("#btnChangePassword").attr("disabled", true);
        $("form[name='frmPassword']").validate({
            rules: {
                current_password: {
                    required: true,
                    nowhitespace: true,
                },
                new_password: {
                    required: true,
                    minlength: 6,
                    maxlength: 15,
                    nowhitespace: true,
                },
                confirm_password: {
                    required: true,
                    equalTo: '[name="new_password"]',
                    minlength: 6,
                    maxlength: 15,
                    nowhitespace: true,
                },
            },
            messages: {
                current_password: {
                    required: "Please enter current password.",
                },
                new_password: {
                    required: "Please enter new password.",
                    minlength:
                        "New password minimum legnth should be 8 character.",
                    maxlength:
                        "New password maximum legnth should be 15 character.",
                },
                confirm_password: {
                    required: "Please enter confirm password.",
                    equalTo: "Password and confirm password does not match.",
                    minlength:
                        "New password minimum legnth should be 8 character.",
                    maxlength:
                        "New password maximum legnth should be 15 character.",
                },
            },
            errorElement: "span",
            wrapper: "p",
            errorPlacement: function (error, element) {
                error.insertAfter(element); // default function
                $("#btnChangePassword").attr("disabled", "disabled");
            },
            success: function (error) {
                error.removeClass("error");
                if ($("#frmPassword").validate()) {
                    $("#btnChangePassword:disabled").removeAttr("disabled");
                } else {
                    $("#btnChangePassword").attr("disabled", "disabled");
                }
            },
            submitHandler: function (form) {
                form.submit();
            },
        });
    };

    function loadAutocomplete(superadmin_url) {
        mediaObj.then(media => {
            media.initAutocomplete(superadmin_url);
        });
    }

    return {
        init: function () {
            window.mediaArr = {};
            editProfile();
            loadAutocomplete(superadmin_url);
        },
        changePassword: function () {
            changePassword();
        },
    };
})();
