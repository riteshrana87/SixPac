 $("input[data-bootstrap-switch]").each(function () {
    $(this).bootstrapSwitch("state", $(this).prop("checked"));
});
$("#change_status").on(
    "switchChange.bootstrapSwitch",
    function (event, state) {
        if (state) {
            $("#status").val(1);
        } else {
            $("#status").val(0);
        }
    }
);

$(document).on('change','.video-category',function() {
    var videoCategory = $.trim($(this).val());
    if (videoCategory==='1') {
        $('#openVimeoContainer').hide();
        $('#openVideoContainer').fadeIn(500);
    } else {
        $('#openVideoContainer').hide();
        $('#openVimeoContainer').fadeIn(500);
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


function add () {
       
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

        var form = $("form[name='frmAddPosts']");

        $("body").on("click", "#btnSubmit", function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    post_title: {
                        required: false,
                        minlength: 4,
                        maxlength: 200,
                        remote: business_url + "/posts/checkPostExists",
                    },
                    post_content: {
                        required: true,
                        minlength: 4,
                        maxlength: 2200,
                    },
                    totalFiles: {
                        required: true,
                        min: 1,
                    },
                    notes: {
                        required: false,
                        minlength: 4,
                        maxlength: 300,
                    },
                },
                messages: {
                    post_title: {
                        //required:	'Please enter post title.',
                        minlength:
                            "Post title minimum length should be 4 character.",
                        maxlength:
                            "Post title maximum length should be 200 character.",
                        remote: "Post title already exist.",
                    },
                    post_content: {
                        required: "Please enter post content.",
                        minlength:
                            "Post content minimum length should be 4 character.",
                        maxlength:
                            "Post content maximum length should be 2200 character.",
                    },
                    totalFiles: {
                        required: "Please select post media.",
                        min: "Please select atleast 1 media.",
                    },
                    notes: {
                        //required:	'Please enter notes.',
                        minlength:
                            "Notes minimum length should be 4 character.",
                        maxlength:
                            "Notes maximum length should be 300 character.",
                    },
                },
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
    };

function manage() {
    var table = $("#tblExercises").DataTable({
        processing: true,
        serverSide: true,
        ajax: business_url + "/get-fit/workout-plan",
        order: [[0, "desc"]],
        columns: [
            { data: "id", name: "id" },
            { data: "icon_file", name: "icon_file" },
            { data: "name", name: "name" },
            { data: "plan_day", name: "plan_day" },
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

    