const mediaObj = import('../../js/common/uploadmedia.js');
var Posts = (function () {
    var add = function () {
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
                        remote: superadmin_url + "/posts/checkPostExists",
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
                    }
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

    var profanityWords = function () {
        $.ajax({
            type: "POST",
            url: baseurl + "/getProfanityWords",
            data: { action: "bad_words", _token: csrf_token },
            success: function (response) {
                let badWordList = JSON.parse(JSON.stringify(response));
                $.each(badWordList, function (key, value) {
                    badWords.push(value);
                });
            },
        });

        /***** PROFANITY WORD CHECK FOR CONTENT CODE START HERE ******/
        $("body").on("keyup", ".profanity-disabled", function () {
            const description = $(this).val();
            let isInclude = false;

            try {
                badWords.forEach((word) => {
                    var error = 0;
                    if (description.includes(word)) {
                        isInclude = true;
                        let newDesc = description.replace(word, "****");
                        $(this).val(newDesc);
                        $(this).focus();
                        var error = 1;
                    }
                    if (description.includes(toTitleCase(word))) {
                        isInclude = true;
                        let newDesc = description.replace(
                            toTitleCase(word),
                            "****"
                        );
                        $(this).val(newDesc);
                        $(this).focus();
                        var error = 1;
                    }
                    if (description.includes(word.toUpperCase())) {
                        isInclude = true;
                        let newDesc = description.replace(
                            word.toUpperCase(),
                            "****"
                        );
                        $(this).val(newDesc);
                        $(this).focus();
                        var error = 1;
                    }
                    if (error == 1) {
                        callToasterAlert("Bad words are not allowed.", "error");
                    }
                });
            } catch (e) {
                console.log(e);
            }
        });
        /***** PROFANITY WORD CHECK FOR CONTENT CODE END HERE ******/
    };

    function uploadGallery(input, galleryPreview) {
        var existingVideosCount = $(".video").length;

        const videoExtensionArr = [
            "video/mp4",
            "video/quicktime",
            "video/x-matroska",
            "video/webm",
        ];

        const imageExtensionArr = ["image/png", "image/jpg", "image/jpeg"];

        var files = input.files;
        var filesArr = Array.prototype.slice.call(files);

        var countFiles = $("#totalFiles").val();

        var mediaNumber = 1;
        if (countFiles == 0) {
            var n = 1;
        } else {
            var n = parseInt(countFiles) + parseInt(1);
            var mediaNumber = parseInt(countFiles) + parseInt(1);
        }

        var addFilesCount = parseInt(countFiles) + parseInt(filesArr.length);

        if (filesArr.length > 5 || addFilesCount > 5) {
            //Swal.fire('Error','Only 5 files are allowed for post gallery.','error');
            callToasterAlert(
                "Only 5 files are allowed for post gallery.",
                "error"
            );
            return false;
        }

        var countGallery = 0;

        var x = 1;
        filesArr.forEach(function (f, index) {
            /* if (!f.type.match("image.*")) {
				return;
			} */

            if (f.type.match("video.*")) {
                if (x > 1) {
                    callToasterAlert(
                        "Maximum 1 video allowed for post gallery.",
                        "error"
                    );
                    return false;
                } else {
                    x++;
                }
            }

            var fileType = f.type;
            var fileExtension = fileType.toLowerCase();
            var fileSize = f.size;

            var sizeInKb = parseFloat(f.size / 1024).toFixed(2);
            //console.log(sizeInKb); // in binary

            if (sizeInKb >= 1024 && videoExtensionArr.includes(fileExtension)) {
                callToasterAlert(
                    "Video file size should be less than 1MB.",
                    "error"
                );
                return false;
            }

            if (sizeInKb >= 300 && imageExtensionArr.includes(fileExtension)) {
                callToasterAlert(
                    "Image file size should be less than 300KB.",
                    "error"
                );
                return false;
            }

            if (
                imageExtensionArr.includes(fileExtension) == false &&
                videoExtensionArr.includes(fileExtension) == false
            ) {
                callToasterAlert(
                    "Gallery file should be .jpg, .jpeg, .png or .mp4 extension.",
                    "error"
                );
                return false;
            }
            var maxId = 0;
            if (
                videoExtensionArr.includes(fileExtension) ||
                imageExtensionArr.includes(fileExtension)
            ) {
                var filebox = $(".fileboxIndex").map(function (index, el) {
                    return parseInt($(el).val());
                });
                var mediaNo = Math.max.apply(null, filebox);
                if (
                    mediaNo == Number.POSITIVE_INFINITY ||
                    mediaNo == Number.NEGATIVE_INFINITY
                ) {
                    maxId = parseInt(index) + 1;
                } else {
                    maxId = parseInt(mediaNo) + 1;
                }
                $(galleryPreview).append(
                    "<input type='hidden' class='fileboxIndex' id='file_index_" +
                        maxId +
                        "' value='" +
                        maxId +
                        "'>"
                );
            }

            if (videoExtensionArr.includes(fileExtension)) {
                if (existingVideosCount > 1) {
                    callToasterAlert(
                        "Maximum 1 video allowed for post gallery.",
                        "error"
                    );
                    return false;
                } else {
                    var fileReader = new FileReader();
                    fileReader.onload = function (e) {
                        var blob = new Blob([fileReader.result], {
                            type: f.type,
                        });
                        var url = URL.createObjectURL(blob);

                        blobToDataURL(blob, function (dataurl) {
                            $(
                                $.parseHTML(
                                    '<input type="hidden" name="media[' +
                                        maxId +
                                        ']" id="inputMedia_' +
                                        maxId +
                                        '">'
                                )
                            )
                                .attr("value", dataurl)
                                .appendTo($(".store"));
                        });

                        var video = document.createElement("video");
                        var timeupdate = function () {
                            if (snapImage()) {
                                video.removeEventListener(
                                    "timeupdate",
                                    timeupdate
                                );
                                video.pause();
                            }
                        };
                        video.addEventListener("loadeddata", function () {
                            if (snapImage()) {
                                video.removeEventListener(
                                    "timeupdate",
                                    timeupdate
                                );
                            }
                        });
                        var snapImage = function () {
                            var canvas = document.createElement("canvas");
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas
                                .getContext("2d")
                                .drawImage(
                                    video,
                                    0,
                                    0,
                                    canvas.width,
                                    canvas.height
                                );
                            var image = canvas.toDataURL();
                            var success = image.length > 100000;
                            if (success) {
                                var img = document.createElement("img");
                                img.src = image;
                                $(galleryPreview).append(
                                    "<li class='filebox' id='file_id_" +
                                        maxId +
                                        "' data-mid='" +
                                        maxId +
                                        "' style='cursor:move;'><img src='" +
                                        img.src +
                                        "'>" +
                                        "<a href='javascript:void(0)' class='deleteMedia' data-id='" +
                                        maxId +
                                        "'><i class='fa fa-trash'></i></a></li>"
                                );

                                /** Insert video thumb into hidden field code start **/
                                $(
                                    $.parseHTML(
                                        '<input type="hidden" class="video" name="video_thumb[' +
                                            maxId +
                                            ']" id="video_thumb_' +
                                            maxId +
                                            '">'
                                    )
                                )
                                    .attr("value", img.src)
                                    .appendTo($(".store"));
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

                        $("#totalFiles").val(n);
                        n++;
                    };
                    fileReader.readAsArrayBuffer(f);
                    countGallery++;
                    // New code end here
                }
            }

            if (imageExtensionArr.includes(fileExtension)) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var fileContent = e.target.result;
                    mediaArr[maxId] = {};
                    mediaArr[maxId] = URL.createObjectURL(f);
                    $(
                        $.parseHTML(
                            '<input type="hidden" name="media[' +
                                maxId +
                                ']" id="inputMedia_' +
                                maxId +
                                '">'
                        )
                    )
                        .attr("value", fileContent)
                        .appendTo($(".store"));
                    $(galleryPreview).append(
                        "<li class='filebox' id='file_id_" +
                            maxId +
                            "' data-mid='" +
                            maxId +
                            "' style='cursor:move;'><img id='gallery_thumb_"+maxId+"' class='gallery_thumb' src='" +
                            fileContent +
                            "'><a href='javascript:void(0)' class='deleteMedia' data-id='" +
                            maxId +
                            "'><i class='fa fa-trash'></i></a><a href='javascript:void(0)' id='crop_media_"+maxId+"' class='cropMedia' data-id='" +
                            maxId +
                            "'><i class='fa fa-scissors'></i></a></li>"
                    );
                    $("#totalFiles").val(n);
                    n++;
                };
                reader.readAsDataURL(f);
                countGallery++;
            }
            mediaNumber++;
        });

        var postFilesCount = parseInt(countGallery) + parseInt(countFiles);
        if (postFilesCount >= 5) {
            $("#btnUploadGallery").hide();
        } else {
            $("#btnUploadGallery").show();
        }

        $("#gallery_files").val("");
    }

    $("#gallery_files").change(function () {
        $("#totalFiles-error").remove();
        uploadGallery(this, "ul.preview");
    });

    function blobToDataURL(blob, callback) {
        var fileReader = new FileReader();
        fileReader.onload = function (e) {
            callback(e.target.result);
            //console.log(e.target.result);
        };
        fileReader.readAsDataURL(blob);
    }

    function callToasterAlert(title, type) {
        Swal.fire({
            title: title,
            //type: type,
            icon: type,
            toast: true,
            //animation: true,
            showCloseButton: true,
            showCancelButton: true,
            allowEscapeKey: true,
            //allowOutsideClick: true,
            showCancelButton: false,
            showConfirmButton: false,
            timer: 5000,
            position: "top-right",
        });
    }

    function initSortable() {
        $( function() {
            $(".sortable").sortable({
                stop: function( event, ui ) {
                    var $store = $('.store');
                    var media = [];
                    $('.sortable li.filebox').each(function(idx, ele) {
                        var index = $(ele).data('mid');
                        $.each($store, function(key, element){
                            media.push($(element).find('#inputMedia_'+index).clone());
                            media.push($(element).find('#mediaThumb_'+index).clone());
                        });
                    });
                    media.push($store.find('.video').clone());
                    $store.html('').html(media);
                }
            }).disableSelection();
        });
    }

    /***** AUTOTAG CODE START HERE ******/
    $("#post_content").on("keypress", function () {
        $.fn.atwho.debug = true;
        var jeremy = decodeURI("J%C3%A9r%C3%A9my"); // Jérémy

        var at_config = {
            at: "@",
            //data: names,
            data: superadmin_url + "/get-users",
            headerTpl: "<span>Select User</span>",
            insertTpl: "@${name}",
            //displayTpl: "<li class='username'>@${name} <small>${email}</small></li>",
            displayTpl: "<li class='username'>@${name}</li>",
            minLen:2,
            limit: 400000,
        };

        var hashTag_config = {
            at: "#",
            // data: hashTags,
            data: superadmin_url + "/get-hash-tags",
            displayTpl: "<li>${name}</li>",
            //insertTpl: '#${key}',
            insertTpl: "#${name}",
            delay: 400,
            minLen:2,
            limit: 400000,
        };
        $inputor = $("#post_content").atwho(at_config).atwho(hashTag_config);
        //$inputor.caret("pos", 2200);
        $inputor.focus().atwho("run");

        hashTag_config.insertTpl = "";
        $("#editable").atwho(at_config).atwho(hashTag_config);
    });
    /***** AUTOTAG CODE END HERE ******/

    return {
        init: function () {},
        archivePosts: function () {},
        add: function () {
            window.badWords = [];
            window.mediaArr = {};
            add();
            initSortable();
            profanityWords();
        },
        edit: function () {},
    };
})();
