var Interests = (function () {
    var manage = function () {
        var table = $("#tblInterests").DataTable({
            processing: true,
            serverSide: true,
            ajax: superadmin_url + "/interests",
            order: [[0, "desc"]],
            columns: [
                { data: "id", name: "id" },
                { data: "icon_file", name: "icon_file" },
                { data: "interest_name", name: "interest_name" },
                { data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    className: "text-center",
                    visible: false,
                },
                { targets: 1, searchable: false, className: "text-center" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: true, className: "text-left" },
                { targets: 4, searchable: true, className: "text-center" },
                { targets: 5, searchable: false, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-left",
                },
            ],
        });
    };

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

        var form = $("form[name='frmAddInterest']");
        $("#btnSubmit").click(function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    interest_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 70,
                        remote:
                            superadmin_url + "/interests/checkInterstExists",
                    },
                    interest_icon: {
                        required: true,
                    },
                },
                messages: {
                    interest_name: {
                        required: "Please enter interest name.",
                        minlength:
                            "Interest name minimum length should be 4 character.",
                        maxlength:
                            "Interest name maximum length should be 70 character.",
                        remote: "Interest name already exist.",
                    },
                    interest_icon: {
                        required: "Please select interest icon.",
                    },
                },
            });
            if (form.valid()) {
                form.submit();
            }
        });

        /** Change icon code start here **/
        $("#interest_icon").hide();
        $("body").on("click", "#pf_edit, #pf_upload_icon", function () {
            $("#interest_icon").trigger("click");
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

        var form = $("form[name='frmEditInterest']");
        var interestId = $("#interest_id").val();

        $("body").on("click", "#btnSubmit", function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    interest_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 70,
                        remote:
                            superadmin_url +
                            "/interests/checkInterstExists?id=" +
                            interestId,
                    },
                    interest_icon: {
                        required: function (element) {
                            if (
                                $("#interest_icon").val() == "" &&
                                $("#old_icon").val() != ""
                            ) {
                                return false;
                            } else {
                                return true;
                            }
                        },
                    },
                },
                messages: {
                    interest_name: {
                        required: "Please enter interest name.",
                        minlength:
                            "Interest name minimum length should be 4 character.",
                        maxlength:
                            "Interest name maximum length should be 70 character.",
                        remote: "Interest name already exist.",
                    },
                    interest_icon: {
                        required: "Please select interest icon.",
                    },
                },
            });
            if (form.valid()) {
                form.submit();
            }
        });

        /** Save admin profile picture code start here **/
        $("#interest_icon").hide();
        $("body").on("click", "#pf_edit, #pf_upload_icon", function () {
            $("#interest_icon").trigger("click");
        });
        /** Save admin profile picture code end here **/
    };
    /** Edit record code end here **/

    /*** Common javascript code start here  **/
    $("#interest_icon").on("change", function () {
        $("#interest_icon-error").html("");
        $("#old_icon").val("");
        $("#interest-icon-img").hide();
        $("#interest-icon-preview").show();
        var imgPath = $(this)[0].value;
        var extn = imgPath
            .substring(imgPath.lastIndexOf(".") + 1)
            .toLowerCase();
        if (extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof FileReader != "undefined") {
                var uploadedFile = document.getElementById("interest_icon");
                var size = parseFloat(
                    uploadedFile.files[0].size / 1024
                ).toFixed(2);
                if (size < 1024) {
                    var reader = new FileReader();
                    reader.readAsDataURL($(this)[0].files[0]);
                    reader.onload = function (e) {
                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function () {
                            var height = this.height;
                            var width = this.width;
                            if (width < 100 || width > 800) {
                                callToaster(
                                    "error",
                                    "Interest icon width should be beetween 100px to 800px."
                                );
                                $("#interest-icon-img").show();
                                $("#interest-icon-preview").html("");
                                $("#interest_icon").val("");
                                $("#old_icon").val("");
                                return false;
                            } else {
                                var editBtn =
                                    '<a href="javascript:void(0);" id="pf_edit" class="hovericon lc_edit"><i class="fa fa-pencil"></i></a><a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a>';
                                $("#interest-icon-preview").html(
                                    "<img src='" +
                                        e.target.result +
                                        "' id='interest_icon_placeholder' width='185px' height='185px'>" +
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
                    $("#interest-icon-img").show();
                    $("#interest-icon-preview").html("");
                    $("#interest_icon").val("");
                    $("#old_icon").val("");
                    return false;
                }
            } else {
                alert("This browser does not support FileReader.");
                return false;
            }
        } else {
            callToaster("error", "Please upload a JPG or PNG image.");
            $("#interest-icon-img").show();
            $("#interest-icon-preview").html("");
            $("#interest_icon").val("");
            $("#old_icon").val("");
            return false;
        }
    });

    $("body").on("click", "#pf_delete", function () {
        $("#interest-icon-img").show();
        $("#interest-icon-preview").hide();
        $("#interest_icon").val("");
        $("#old_icon").val("");
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
            var url = superadmin_url + "/interests/destroy/" + id;
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

    return {
        init: function () {
            manage();
            view();
            delete_record();
        },
        add: function () {
            add();
        },
        edit: function () {
            edit();
        },
    };
})();
