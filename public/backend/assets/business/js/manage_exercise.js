$("input[data-bootstrap-switch]").each(function () {
    $(this).bootstrapSwitch("state", $(this).prop("checked"));
});
$("#change_status").on("switchChange.bootstrapSwitch", function (event, state) {
    if (state) {
        $("#status").val(1);
    } else {
        $("#status").val(0);
    }
});

$(document).on("change", ".video-category", function () {
    var videoCategory = $.trim($(this).val());
    if (videoCategory === "1") {
        $("#openVimeoContainer").hide();
        $("#openVideoContainer").fadeIn(500);
    } else {
        $("#openVideoContainer").hide();
        $("#openVimeoContainer").fadeIn(500);
    }
});

$("body").on("click", ".viewRecord", function () {
    $(".full_details").html("");
    $("#viewDetails").modal({ backdrop: "static", keyboard: false }, "show");
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    submitcall(url, { object_id: id }, function (output) {
        $(".full_details").html(output);
        $(".viewRecord").trigger("blur");
    });
});

$("body").on("click", ".delete", function () {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $("#deleteForm").attr("action", url + "/" + id);
    $("#DeleteModal").modal({ backdrop: "static", keyboard: false }, "show");
});

$("body").on("click", "#yesBtn", function () {
    $("#deleteForm").submit();
});

$("body").on("click", "#pf_edit, #pf_upload_icon", function () {
    $("#poster_image").trigger("click");
});

$("body").on("click", "#delete_image", function () {
    $(".post_image_preview").hide();
    $("#poster_image").val("");
    $("#old_image").val("");
    $("#poster_image").next(".custom-file-label").html("Choose file");
});

$("body").on("click", "#delete_video", function () {
    $("#previewImg").attr("src", "");
    $("#video_thumb").val("");
    $("#exerciseVideo").val("");
    $(".video_preview").hide();
    $("#video_name").val("");
    $("#old_video").val("");
    $("#video_name").next(".custom-file-label").html("Choose file");
});

$("#poster_image").on("change", function (e) {
    $("#old_image").val("");
    var fileName = e.target.files[0].name;
    var imgPath = $(this)[0].value;
    var $self = $(this);
    $(this).next(".custom-file-label").html("Choose file");
    var extn = imgPath.substring(imgPath.lastIndexOf(".") + 1).toLowerCase();
    if (extn == "png" || extn == "jpg" || extn == "jpeg") {
        if (typeof FileReader != "undefined") {
            var uploadedFile = document.getElementById("poster_image");
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
                        if (width < 200 || width > 1000) {
                            callToaster(
                                "error",
                                "Poster image width should be between 200px to 1000px."
                            );
                            $(".post_image_preview").html("");
                            $("#poster_image").val("");
                            $("#old_image").val("");
                            return false;
                        } else {
                            $(".post_image_preview").show();
                            $self.next(".custom-file-label").html(fileName);
                            $(".post_image_preview").css({
                                display: "flex",
                                "justify-content": "center",
                            });
                            var editBtn =
                                '<a href="javascript:void(0)" id="delete_image" class="deleteMedia"><i class="fa fa-trash"></i></a>';
                            $(".post_image_preview").html(
                                "<img src='" +
                                    e.target.result +
                                    "' id='post_image' style='height:100%'>" +
                                    editBtn
                            );
                        }
                    };
                };
            } else {
                callToaster("error", "Please select a file less than 1 MB.");
                $(".post_image_preview").html("");
                $("#poster_image").val("");
                $("#old_image").val("");
                return false;
            }
        } else {
            alert("This browser does not support FileReader.");
            return false;
        }
    } else {
        callToaster("error", "Please upload a JPG or PNG image.");
        $(".post_image_preview").html("");
        $("#poster_image").val("");
        $("#old_image").val("");
        return false;
    }
});

function blobToDataURL(blob, callback) {
    var fileReader = new FileReader();
    fileReader.onload = function (e) {
        callback(e.target.result);
    };
    fileReader.readAsDataURL(blob);
}

$("#video_name").on("change", function (e) {
    $("#old_video").val("");
    var fileName = e.target.files[0].name;
    var imgPath = $(this)[0].value;
    var $self = $(this);
    $(this).next(".custom-file-label").html("Choose file");
    var extn = imgPath.substring(imgPath.lastIndexOf(".") + 1).toLowerCase();
    if (["mp4", "mov", "mkv", "webm"].includes(extn)) {
        if (typeof FileReader != "undefined") {
            var f = $(this)[0].files[0];
            var fileReader = new FileReader();
            fileReader.onload = function (e) {
                var blob = new Blob([fileReader.result], {
                    type: f.type,
                });
                var url = URL.createObjectURL(blob);

                $(".video_preview").show();
                blobToDataURL(blob, function (dataurl) {
                    $("#exerciseVideo").val(dataurl);
                });

                var video = document.createElement("video");
                var timeupdate = function () {
                    if (snapImage()) {
                        video.removeEventListener("timeupdate", timeupdate);
                        video.pause();
                    }
                };
                video.addEventListener("loadeddata", function () {
                    if (snapImage()) {
                        video.removeEventListener("timeupdate", timeupdate);
                    }
                });
                var snapImage = function () {
                    var canvas = document.createElement("canvas");
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas
                        .getContext("2d")
                        .drawImage(video, 0, 0, canvas.width, canvas.height);
                    var image = canvas.toDataURL();
                    var success = image.length > 100000;
                    if (success) {
                        var img = document.createElement("img");
                        img.src = image;
                        $(".video_preview")
                            .find("#previewImg")
                            .attr("src", img.src);
                        /** Insert video thumb into hidden field code start **/
                        $("#video_thumb").val(img.src);
                        /** Insert video thumb into hidden field code end **/
                        URL.revokeObjectURL(url);
                    }
                    return success;
                };
                video.addEventListener("timeupdate", timeupdate);
                video.preload = "metadata";
                video.src = url;
                // Load video in Safari / IE11
                video.muted = true;
                video.playsInline = true;
                video.play();
                $self.next(".custom-file-label").html(fileName);
            };
            fileReader.readAsArrayBuffer(f);
        } else {
            alert("This browser does not support FileReader.");
            return false;
        }
    } else {
        callToaster("error", "Please upload a mp4, mov, mkv, webm video.");
        $(".video_preview").html("");
        $("#video_name").val("");
        $("#old_video").val("");
        return false;
    }
});

function add() {
    $("#change_is_public").on(
        "switchChange.bootstrapSwitch",
        function (event, state) {
            if (state) {
                $("#is_public").val(1);
            } else {
                $("#is_public").val(0);
            }
        }
    );

    var form = $("form[name='frmAddEditExercise']");

    $("body").on("click", "#btnSubmit", function (e) {
        e.preventDefault();
        form.validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    minlength: 4,
                    maxlength: 70,
                    // remote: business_url + "/posts/checkPostExists",
                },
                overview: {
                    required: true,
                    minlength: 4,
                    maxlength: 500,
                },
                poster_image: {
                    required: function (element) {
                        if (
                            $("#poster_image").val() == "" &&
                            $("#old_image").val() != ""
                        ) {
                            return false;
                        } else {
                            return true;
                        }
                    },
                },
                video_name: {
                    required: function (element) {
                        if (
                            $("#video_name").val() == "" &&
                            $("#old_video").val() != ""
                        ) {
                            return false;
                        } else {
                            return true;
                        }
                    },
                },
                "interests[]": {
                    required: true,
                },
                workout_type_id: {
                    required: true,
                },
                duration_id: {
                    required: true,
                },
                "equipment[]": {
                    required: true,
                },
                "body_part[]": {
                    required: true,
                },
                "age_group[]": {
                    required: true,
                },
                "fitness_level[]": {
                    required: true,
                },
                gender: {
                    required: true,
                },
                location: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "Please enter exercise title.",
                    minlength:
                        "exercise title minimum length should be 4 character.",
                    maxlength:
                        "exercise title maximum length should be 200 character.",
                },
                overview: {
                    required: "Please enter exercise overview.",
                    minlength:
                        "exercise overview minimum length should be 4 character.",
                    maxlength:
                        "exercise overview maximum length should be 500 character.",
                },
                poster_image: {
                    required: "Please select poster image.",
                },
                video_name: {
                    required: "Please select exercise video.",
                },
                "interests[]": {
                    required: "Please select exercise interests.",
                },
                workout_type_id: {
                    required: "Please select workout type.",
                },
                duration_id: {
                    required: "Please select duration.",
                },
                "equipment[]": {
                    required: "Please select equipment.",
                },
                "body_part[]": {
                    required: "Please select body parts.",
                },
                "age_group[]": {
                    required: "Please select age group.",
                },
                "fitness_level[]": {
                    required: "Please select fitness level.",
                },
                gender: {
                    required: "Please select gender.",
                },
                location: {
                    required: "Please select location.",
                },
            },
            errorPlacement: function (error, element) {
                console.log(element.attr("data-name"))
                if (
                    [
                        "interests[]",
                        "workout_type_id",
                        "duration_id",
                        "equipment[]",
                        "body_part[]",
                        "age_group[]",
                        "fitness_level[]",
                        "gender",
                        "location",
                    ].includes(element.attr("name"))
                ) {
                    var attrName = element.attr("data-name");
                    error.css("padding-left", "12px");
                    error.insertAfter($("#err_" + attrName));
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function(form, validator) {

        if (!validator.numberOfInvalids())
            return;

        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-120
        }, 2000);

    }
        });

        if (form.valid()) {
            form.submit();
        }
    });

    /** Post gallery code start **/
    $("#btnUploadGallery").click(function () {
        $("#gallery_files").trigger("click");
    });

    $("body").on("click", ".deleteMedia", function () {
        var mediaId = $(this).attr("data-id");
        $("#file_id_" + mediaId).remove();
        $("#inputMedia_" + mediaId).remove();
        $("#mediaThumb_" + mediaId).remove();
        $("#video_thumb_" + mediaId).remove();
        $("#file_index_" + mediaId).remove();
        var totalMedia = $("#totalFiles").val();
        $("#totalFiles").val(parseInt(totalMedia) - parseInt(1));

        if ($("#totalFiles").val() < 5) {
            $("#btnUploadGallery").show();
        } else {
            $("#btnUploadGallery").hide();
        }
        //$(this).remove();
    });
    /** Post gallery code end **/
}

function manage() {
    var table = $("#tblExercises").DataTable({
        processing: true,
        serverSide: true,
        ajax: business_url + "/get-fit/exercises",
        order: [[0, "desc"]],
        columns: [
            { data: "id", name: "id" },
            { data: "icon_file", name: "icon_file" },
            { data: "name", name: "name" },
            { data: "workout_type_id", name: "workout_type_id" },
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
            { targets: 1, searchable: false, className: "text-center" },
            { targets: 2, searchable: true, className: "text-left" },
            { targets: 3, searchable: true, className: "text-left" },
            { targets: 4, searchable: false, className: "text-left" },
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
}
