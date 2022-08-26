var ProductGallery = (function () {
    
    /* Add product gallery data form vlidation code start here */
    var add = function () {
		
		$("#btnUploadGallery").click(function(){
			$("#files").trigger("click");
		})
	
		var multiImgPreview = function(input, imgPreviewPlaceholder) {
			if (input.files) {
				var filesAmount = input.files.length;
				/* for (i = 0; i < filesAmount; i++) {
					var reader = new FileReader();
					reader.onload = function(event) {
						
						$($.parseHTML('<img>')).attr('src', event.target.result).appendTo(imgPreviewPlaceholder);
					}
					reader.readAsDataURL(input.files[i]);
				} */
				
				/*************/
				//e.preventDefault();  
				  var formData = new FormData(document.getElementById("frmAddProductGallery"));
				  let totalFiles = $('#files')[0].files.length; //Total files
				  let files = $('#files')[0];
				  for (let i = 0; i < totalFiles; i++) {
					  formData.append('files' + i, files.files[i]);
				  }
				  formData.append('totalFiles', totalFiles);
				  
				  var totalCountFiles = 0;
				  
				  if(totalFiles > 0){
					  var totalCountFiles = parseInt($('#total_files').val()) + parseInt(totalFiles);
				  }
				  
				  if(totalCountFiles > 5){
					  //alert("Only 3 files are allowed for product gallery.");
					  //Swal.fire('Warning','Only 5 files are allowed for product gallery.','warning');
					  callToasterAlert('Only 5 files are allowed for product gallery.','warning');
					  return false;
				  }
				  
				  $('#total_files').val(totalCountFiles);
				  
				  
			  
				 $.ajax({
					type:'POST',
					url: business_url+ "/products/gallery/save-gallery",
					data: formData,
					cache:false,
					contentType: false,
					processData: false,
					beforeSend: function(){
					 $("#loading").show();
				    },
					complete: function(){
					 $("#loading").hide();
				    },
					success: (data) => {
					   $('#files').val("");
					   $(".preview-image").html(data);
					   //Swal.fire('Success','Gallery uploaded successfully!','success');
					   callToasterAlert('Product files uploaded successfully!','success');
					},
					error: function(data){
					   console.log(data);
					 }
				   });
			   
				/*************/				
			}
		};
		$('#files').on('change', function() {
			multiImgPreview(this, 'div.preview-image');
		});
		
		$('body').on('click', '.gallery_trash', function () {
			var countFiles = $('#total_files').val();
			var totalFiles = (parseInt(countFiles) - parseInt(1));
			$('#total_files').val(totalFiles);
			var galleryId = $(this).attr('data-id');
			var productId = $("#productId").val();
			var url = business_url+'/products/delete-product-gallery';
			$("#loading").show();
			submitcall(url, {'gallery_id': galleryId,'product_id':productId}, function (output) {
				$("#loading").hide();
				callToasterAlert('Product file deleted successfully!','success');
				$(".preview-image").html(output);				
			});
		});
		
	};
	/* Add product gallery data form vlidation code end here */
	
	function callToasterAlert(title,type){
		Swal.fire({
			title: title,
			type: type,
			toast: true,
			animation: true,
			showCloseButton: true,
			showCancelButton: true,
			allowEscapeKey: true,
			allowOutsideClick: true,
			showCancelButton: false,
			showConfirmButton: false,
			timer: 5000,
			position: 'top-right',
	  });
	}
	
    return {
		add: function () {
            add();
        }
    };
})();
