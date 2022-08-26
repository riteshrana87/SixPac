$("body").on("click", ".delete", function () {
    var url = $(this).attr("data-url");
    $("#deleteForm").attr("action", url);
    $("#DeleteModal").modal({ backdrop: "static", keyboard: false }, "show");
});

$("body").on("click", "#yesBtn", function () {
    $("#deleteForm").submit();
});

$("body").on("click", ".viewRecord", function () {
    $(".full_details").html("");
    $("#viewDetails").modal({ backdrop: "static", keyboard: false }, "show");
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    submitcall(url, { object_id: id }, function (output) {
        $(".full_details").html(output);
    });
});

$("#service_icon").hide();
$("body").on("click", "#pf_edit, #pf_upload_icon", function () {
    $("#service_icon").trigger("click");
});

$("#service_icon").on("change", function () {
    $("#interest_icon-error").html("");
    $("#old_icon").val("");
    $("#interest-icon-img").hide();
    $("#interest-icon-preview").show();
    var imgPath = $(this)[0].value;
    var extn = imgPath.substring(imgPath.lastIndexOf(".") + 1).toLowerCase();
    if (extn == "png" || extn == "jpg" || extn == "jpeg") {
        if (typeof FileReader != "undefined") {
            var uploadedFile = document.getElementById("service_icon");
            var size = parseFloat(uploadedFile.files[0].size / 1024).toFixed(2);
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
                                "Category icon width should be beetween 100px to 800px."
                            );
                            $("#interest-icon-img").show();
                            $("#interest-icon-preview").html("");
                            $("#service_icon").val("");
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
                callToaster("error", "Please select a file less than 1 MB.");
                $("#interest-icon-img").show();
                $("#interest-icon-preview").html("");
                $("#service_icon").val("");
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
        $("#service_icon").val("");
        $("#old_icon").val("");
        return false;
    }
});

$("body").on("click", "#pf_delete", function () {
    $("#interest-icon-img").show();
    $("#interest-icon-preview").hide();
    $("#workout_icon").val("");
    $("#old_icon").val("");
});

function manage() {
    var table = $("#tblonDemandServices").DataTable({
        processing: true,
        serverSide: true,
        ajax: superadmin_url + "/get-fit/on-demand-services",
        order: [[0, "desc"]],
        columns: [
            { data: "id", name: "id" },
            { data: "service", name: "service" },
            { data: "getfit_category", name: "getfit_category" },
            { data: "user_id", name: "user_id" },
            { data: "created_at", name: "created_at" },
            { data: "status", name: "status" },
            { data: "action", name: "action" },
        ],
        columnDefs: [
            {
                targets: 0,
                searchable: true,
                className: "text-center",
                visible: false,
            },
            { targets: 1, searchable: true, className: "text-left" },
            { targets: 2, searchable: true, className: "text-left" },
            { targets: 3, searchable: true, className: "text-left" },
            { targets: 4, searchable: true, className: "text-left" },
            { targets: 5, searchable: true, className: "text-center" },
            {
                targets: "no-sort",
                orderable: false,
                order: [],
                className: "text-left",
            },
        ],
    });
}

function add() {
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

    var form = $("form[name='frmAddEditService']");
    $("#btnSubmit").click(function (e) {
        e.preventDefault();
        form.validate({
            ignore: [],
            rules: {
                service: {
                    required: true,
                    minlength: 4,
                    maxlength: 70,
                },
                getfit_id: {
                    required: true,
                },
            },
            messages: {
                service: {
                    required: "The service is required.",
                    minlength: "Service minimum length should be 4 character.",
                    maxlength: "Service maximum length should be 40 character.",
                },
                getfit_id: {
                    required: "The getfit category is required.",
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
}
