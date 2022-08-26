function updateMediaIndex() {
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
    });
}

function updateMediaOrder() {
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
}
export function addOrUpdateProduct(type) {
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
       var formName = (type==='add') ? 'frmAddProduct' : 'frmEditProduct';
       var form = $("form[name='"+formName+"']");
       var product_id = $("#product_id").val();     

        $("body").on("click", "#btnSubmit", function (e) {
            e.preventDefault();
            form.validate({
                ignore: [],
                rules: {
                    product_title: {
                        required: true,
                        minlength: 4,
                        maxlength: 70,
                        remote: business_url+"/products/checkProductExists?id="+product_id,
                    },
                    category_name: {
                        required: true,
                    },
                    product_description: {
                        required: true,
                        minlength: 4,
                        maxlength: 255,
                    },
                    sku: {
                        required: true,
                        minlength: 2,
                        maxlength: 15,
                        remote:
                            business_url + "/products/checkProductSkuExists?id="+product_id,
                    },
                    quantity: {
                        required: true,
                        minlength: 1,
                        maxlength: 10,
                    },
                    cost_price: {
                        required: true,
                        minlength: 1,
                        maxlength: 10,
                    },
                    sell_price: {
                        required: true,
                        minlength: 1,
                        maxlength: 10,
                    },
                },
                messages: {
                    product_title: {
                        required: "Please enter product title.",
                        minlength:
                            "Product title minimum length should be 4 character.",
                        maxlength:
                            "Product title maximum length should be 70 character.",
                        remote: "Product title already exist.",
                    },
                    category_name: {
                        required: "Please select category.",
                    },
                    product_description: {
                        required: "Please enter product description.",
                        minlength:
                            "Product description minimum length should be 4 character.",
                        maxlength:
                            "Product description maximum length should be 255 character.",
                    },
                    sku: {
                        required: "Please enter product SKU.",
                        minlength:
                            "Product SKU minimum length should be 2 character.",
                        maxlength:
                            "Product SKU maximum length should be 15 character.",
                        remote: "Product SKU already exist.",
                    },
                    quantity: {
                        required: "Please enter product quantity.",
                        minlength:
                            "Product quantity minimum length should be 1 digit.",
                        maxlength:
                            "Product quantity maximum length should be 10 digit.",
                    },
                    cost_price: {
                        required: "Please enter product cost price.",
                        minlength:
                            "Product cost price minimum length should be 1 digit.",
                        maxlength:
                            "Product cost price maximum length should be 10 digit.",
                    },
                    sell_price: {
                        required: "Please enter product sell price.",
                        minlength:
                            "Product sell price minimum length should be 1 digit.",
                        maxlength:
                            "Product sell price maximum length should be 10 digit.",
                    },
                },
            });
            if (form.valid()) {
            	if (type==='update') {
            		updateMediaOrder();
            	}
                form.submit();
            }
        });

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
    }

    $("#gallery_files").change(function () {
        $("#totalFiles-error").remove();
        uploadGallery(this, "ul.preview");
    });


export function uploadGallery(input, galleryPreview) {
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
                var productId = $('#product_id').val();
                if (productId && productId!==undefined) {
	                for(var i=1; i<=5; i++) {
	                    if(!mediaIdArr.includes(i)){
	                        mediaIdArr.push(i);
	                        maxId = i;
	                        break;
	                    }
	                }
	            }
               $('.store').append(
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
                                        '" class="product-media" data-index="'+maxId+'">'
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
                                        "' data-mid='" +
                                        maxId +
                                        "' data-id='" +
                                        maxId +
                                        "' data-index='" +
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
                                '" class="product-media" data-index="'+maxId+'">'
                        )
                    )
                        .attr("value", fileContent)
                        .appendTo($(".store"));
                    $(galleryPreview).append(
                        "<li class='filebox new-gallery' id='file_id_" +
                            maxId +
                            "' data-mid='" +
                            maxId +
                            "' data-id='"+maxId+"' data-index='"+maxId+"' style='cursor:move;'><img id='gallery_thumb_"+maxId+"' class='gallery_thumb' src='" +
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

export function blobToDataURL(blob, callback) {
    var fileReader = new FileReader();
    fileReader.onload = function (e) {
        callback(e.target.result);
    };
    fileReader.readAsDataURL(blob);
}

$("body").on("click", ".old_deleteMedia", function () {
    var mediaId = $(this).attr("data-id");
    var dataIndex = $(this).parent('.filebox').attr("data-index");
    $("#old_gallery_id_" + mediaId).remove();
    $("#old_inputMedia_" + mediaId).remove();
    $("#media_order_" + mediaId).remove();
    $('#existingMedia_'+mediaId).remove();
    var totalMedia = $("#totalFiles").val();
    $("#totalFiles").val(parseInt(totalMedia) - parseInt(1));

    if ($("#totalFiles").val() < 5) {
        $("#btnUploadGallery").show();
    } else {
        $("#btnUploadGallery").hide();
    }

    if (mediaIdArr.includes(parseInt(dataIndex))) {
        mediaIdArr = mediaIdArr.filter(function(mediaIndex){
           return mediaIndex!=dataIndex; 
        });
    }
    if (mediaArr[dataIndex]) {
       delete mediaArr[dataIndex]; 
    }
});

export function callToasterAlert(title, type) {
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

export function addSortable(element) {
    $(element).sortable({
        stop: function( event, ui ) {
            var $store = $('.store');
            var media = [];
            $('.sortable li.filebox').each(function(idx, ele) {
                var index = $(ele).data('mid');
                $.each($store, function(key, element){
                    media.push($(element).find('#inputMedia_'+index).clone());
                });
            });
            // media.push($store.find('.video').clone());
            $store.html('').html(media);
        }
    }).disableSelection();
}

export function updateSortable(element) {
    $(element).sortable().disableSelection();
}

$(document).on('keyup keypress blur change','.city-autocomplete', function() {
    if (!$(this).val()) {
        $('#city_name').val('');
        $('#city').val('');
        $('#state_name').val('');
        $('#state').val('');
        $('#country_name').val('');
        $('#country').val('');
    }
});
export function initAutocomplete(base_url) {
        $('.city-autocomplete').autoComplete({
            resolverSettings: {
                url: $('.city-autocomplete').data('url')
            },
            autoSelect:true,
            noResultsText:'No city found',
            bootstrapVersion:'auto',
        }).on('autocomplete.select',function (evt, item) {
            if (item===undefined) {
                $('#city_name').val('');
                $('#city').val('');
                $('#state_name').val('');
                $('#state').val('');
                $('#country_name').val('');
                $('#country').val('');
            } else {
                $('#city').val(item.value);
                $.ajax({
                    url: base_url+"/settings/getCityData",
                    type:'POST',
                    data: {'id':item.value},
                    dataType:'json',
                    error : function(xhr, textStatus, errorThrown) {
                        $('#state_name').val('');
                        $('#state').val('');
                        $('#country_name').val('');
                        $('#country').val('');
                        callToasterAlert('Something went wrong!','error');
                    },
                    success:function(response){
                        if (response.status===true) {
                            $('#state_name').val(response.state_name);
                            $('#state').val(response.state_id);
                            $('#country_name').val(response.country_name);
                            $('#country').val(response.country_id);
                        } else {
                            $('#state_name').val('');
                            $('#state').val('');
                            $('#country_name').val('');
                            $('#country').val('');
                            callToasterAlert('Something went wrong!','error');
                        }
                    }
                });
            }
        });
    }

    // Convert existing image URL to blob for crop //
    if ($('.existingMedia').length > 0) {
        $('.existingMedia').each(function(key, ele) {
            var index = $(ele).attr('data-order');
            var imageUrl = $(ele).val();
            fetch(imageUrl).then(res => res.blob()).then(blob => {
                mediaArr[index] = {};
                let imgBlob = URL.createObjectURL(blob);
                mediaArr[index] = imgBlob;
            });
        });
    }
    // Image cropping : Strt //
    var $modal = $('#modal');
    var cropper;
    var canvas;
    var image = document.getElementById('image'); 
    $('body').on("click", ".cropMedia", function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var mediaId = $(this).data('id');
        if (mediaId) {
            var imageSrc = ($('#inputMedia_'+mediaId).length > 0) ? $('#inputMedia_'+mediaId).val() : mediaArr[mediaId]; 
            // $('#image').attr('src', imageSrc);
            image.src = imageSrc;
            $('#cropMediaId').val(mediaId);
        }
        $modal.modal('show');      
    });

    $modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            viewMode: 3,
            preview: '.preview-cropped-img',
            crop(event) {
                $('#cropHeight').val(event.detail.height);
                $('#cropWidth').val(event.detail.width);
            },
        });
    }).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });

   $("body").on("click", "#crop", function(e){
        e.preventDefault();  
        var cropMediaId = $('#cropMediaId').val();
        var cropperHeight = $('#cropHeight').val();
        var cropperWidth = $('#cropWidth').val();
        if (cropperHeight < 250 || cropperWidth < 250) {
            callToasterAlert('Image dimension should not less than (250 X 250)','error');
            return false;
        }
        canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function(blob) {
             console.log('blob',blob)       
             var url = URL.createObjectURL(blob);
             var reader = new FileReader();
             reader.readAsDataURL(blob); 
             reader.onloadend = function() {
                 var base64data = reader.result;
                 $modal.modal('hide');
                 // mediaArr[cropMediaId] = url;
                $('#gallery_thumb_'+cropMediaId).attr('src',base64data);
                $('#old_gallery_thumb_'+cropMediaId).attr('src',base64data);
                var mediaCls = ($('#old_gallery_thumb_'+cropMediaId).length > 0) ? 'oldThumb' : 'newThumb';
                var dataIndex = ($('#old_gallery_thumb_'+cropMediaId).length > 0) ? $('#old_gallery_thumb_'+cropMediaId).data('id') : cropMediaId;
                // $('#inputMedia_'+cropMediaId).val(base64data);
                // $('#crop_media_'+cropMediaId).remove();
                $('#avtar_placeholder,#profile_photo_placeholder').attr('src',base64data);
                if ($('#mediaThumb_'+cropMediaId).length > 0) {
                    $('#mediaThumb_'+cropMediaId).val(base64data);
                } else {                    
                    $(".store").append('<input type="hidden" name="mediaThumb['+cropMediaId+']" class="croppedThumb '+mediaCls+'" id="mediaThumb_'+cropMediaId+'" value="'+base64data+'" data-id="'+dataIndex+'">');
                }
                // $('#pf_crop').remove();
                $('#avtarImg').val(base64data);
             }
        });
    });
    // Image cropping : End //
    
    if ($.validator!==undefined) {
        $.validator.addMethod("noSpace", function(value, element) { 
          return value.indexOf(" ") < 0 && value != ""; 
        }, "Space not allowed.");
    }