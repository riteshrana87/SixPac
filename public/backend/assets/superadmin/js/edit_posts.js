const mediaObj = import('../../js/common/uploadmedia.js');
var Posts = (function () {
    var edit = function () {
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

        var form = $("form[name='frmEditPosts']");
        var postId = $("#post_id").val();

        $("body").on("click", "#btnSubmit", function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    post_title: {
                        required: false,
                        minlength: 4,
                        maxlength: 200,
                        remote:
                            superadmin_url +
                            "/posts/checkPostExists?id=" +
                            postId,
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
                var oldMediaArr = {};
                var newMediaArr = [];
                var indexArr = [];
                updateMediaIndex();
                $('.filebox').each(function(key,ele){
                    var index = parseInt(key)+1;
                    var dataIndex = $(ele).data('mid');
                    var mediaId = $(ele).data('id');
                    if($(ele).hasClass('old-gallery')){
                        oldMediaArr[mediaId] = {};                    
                        oldMediaArr[mediaId] = index;
                    }
                    if($(ele).hasClass('new-gallery')){ 
                        var dataId = $(ele).attr('data-id');
                        var dataKey = $(ele).attr('data-index');
                        newMediaArr[dataId] = {};                    
                        newMediaArr[dataId] = dataKey;                       
                    }
                });
                $.each(oldMediaArr,function(key,index){
                    $('.store').append('<input type="hidden" id="old_media_order_'+key+'" name="oldMediaOrder['+key+']" value="'+index+'">');
                });
                console.log(newMediaArr)
                $.each(newMediaArr,function(key,index){
                    if (index!==undefined) {
                        $('#inputMedia_'+key).attr('name', 'media['+index+']');
                        $('#video_thumb_'+key).attr('name','video_thumb['+index+']');
                        $('#mediaThumb_'+key).attr('name', 'mediaThumb['+index+']').attr('data-id',index);
                    }
                });
                $('.croppedThumb').each(function(key, ele) {
                    var index = $(ele).data('id');
                    var mediaUrl = $(ele).val();
                    if ($(ele).hasClass('oldThumb')) {
                        $('.store').append('<input type="hidden" id="cropped_thumb_'+key+'" name="oldThumb['+index+']" value="'+mediaUrl+'">');
                    } else {
                        $('.store').append('<input type="hidden" id="cropped_thumb_'+key+'" name="newThumb['+index+']" value="'+mediaUrl+'">');
                    }
                });
                form.submit();
            }
        });

        /** Post gallery code start **/
        $("#btnUploadGallery").click(function () {
            $("#gallery_files").trigger("click");
        });

        $("body").on("click", ".deleteMedia", function () {
            var mediaId = $(this).attr("data-id");
            //console.log(mediaId);
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
            if (mediaIdArr.includes(parseInt(mediaId))) {
                mediaIdArr = mediaIdArr.filter(function(mediaIndex){
                   return mediaIndex!=mediaId; 
                });
            }
        });
        /** Post gallery code end **/
    };

    function updateMediaIndex()
    {
        var i = 1;
        $('.filebox').each(function(key, ele) {
            $(ele).attr('data-index',i);
            i++;
        });
        $('.filebox').each(function(key, ele) {
            var dataId = $(ele).attr('data-id');
            var dataIndex = $(ele).attr('data-index');
            if ($(ele).hasClass('new-gallery')) {
                $('#inputMedia_'+dataId).attr('name', 'media['+dataIndex+']');
            }
            // $('#video_thumb_'+dataId).attr('name','video_thumb['+dataIndex+']');
        });
    }

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
            //Swal.fire('Error','Only 5 files are allowed for product gallery.','error');
            callToasterAlert(
                "Only 5 files are allowed for product gallery.",
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
                        "Maximum 1 video allowed for product gallery.",
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
                for(i=1; i<=5; i++) {
                    if(!mediaIdArr.includes(i)){
                        mediaIdArr.push(i);
                        maxId = i;
                        break;
                    }
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
                        "Maximum 1 video allowed for product gallery.",
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
                                    "<li class='filebox new-gallery' id='file_id_" +
                                        maxId +
                                        "' data-id='" +
                                        maxId +
                                        "' data-index='"+maxId+"' style='cursor:move;'><img src='" +
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
                        "<li class='filebox new-gallery' id='file_id_" +
                            maxId +
                            "' data-id='" +
                            maxId +
                            "' data-index='"+maxId+"' style='cursor:move;'><img id='gallery_thumb_"+maxId+"' class='gallery_thumb' src='" +
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

        var productFilesCount = parseInt(countGallery) + parseInt(countFiles);
        if (productFilesCount >= 5) {
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
            limit: 400000,
            minLen:2
        };

        var hashTag_config = {
            at: "#",
            // data: hashTags,
            data: superadmin_url + "/get-hash-tags",
            displayTpl: "<li>${name}</li>",
            //insertTpl: '#${key}',
            insertTpl: "#${name}",
            delay: 400,
            limit: 400000,
            minLen:2
        };
        $inputor = $("#post_content").atwho(at_config).atwho(hashTag_config);
        // $inputor.caret("pos", 2200);
        $inputor.focus().atwho("run");

        hashTag_config.insertTpl = "";
        $("#editable").atwho(at_config).atwho(hashTag_config);
    });

    /***** AUTOTAG CODE END HERE ******/

    return {
        init: function () {},
        archivePosts: function () {},
        add: function () {},
        edit: function () {
            window.mediaIdArr = [];
            window.mediaArr = {}; 
            $('.sortable').sortable().disableSelection();
            edit();
            $('.filebox').each(function(key,ele){
                mediaIdArr.push(parseInt($(ele).attr('data-mid')));
            });
        },
    };
})();
