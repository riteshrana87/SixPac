var Products = (function () {
	
    /* Import products code start here */
    var importProducts = function () {
		
		$('#csv_file').on('change', function() {  
		    const size = (this.files[0].size / 1024 / 1024).toFixed(2);
			var ext = $(this).val().split('.').pop().toLowerCase();
			if($.inArray(ext, ['csv']) == -1) {
				callToasterAlert('Only CSV file allowed.','error');
				$(this).val("");
				return false;
			}
			else
			{
				if (size > 2) {
					callToasterAlert('Product CSV file size must be less than 2MB.','error');
					$(this).val("");
					return false;
				}
			}
        });
		
		$('#zip_file').on('change', function() {
		    const size = (this.files[0].size / 1024 / 1024).toFixed(2);
            
			var ext = $(this).val().split('.').pop().toLowerCase();
			if($.inArray(ext, ['zip']) == -1) {
				callToasterAlert('Only ZIP file allowed.','error');
				$(this).val("");
				return false;
			}
			else
			{
				if (size > 125) {
					callToasterAlert('Product media ZIP file size must be less than 125MB.','error');
					$(this).val("");
					return false;
				}
			}
        });
		
		$('.mfile').on('change', function() {
			$('#process').css('display', 'none');
			var totalRows = $("#total_rows").val();
			var width = Math.round((0 * 100) / (totalRows));
			$('#process_data').text(0);
			$('.progress .progress-bar').css('width', '0%');
        });
		
		
		/** Check validation code start here **/
		var form = $("form[name='frmImportProduct']");
		
		$("body").on("click", "#btnRunImporter", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					csv_file:{required: true},
					zip_file:{required: false},
				},
				messages: {
					csv_file: {
						required: 'Please upload product CSV file.',
					},
					zip_file: {
						required: 'Please upload product media ZIP file.',
					},
				}
			});
			if (form.valid()) {
				form.submit();
			}
		});
		/** Check validation code end here **/		
		
		// First step for import product - end
		var clear_timer;
		$('#frmImportProduct').on('submit', function(event){
			
			$('#message').html('');
			$("#loading").show();
			event.preventDefault();
			$.ajax({
				url: business_url+"/products/upload-csv",
				method:"POST",
				data: new FormData(this),
				dataType:"json",
				contentType:false,
				cache:false,
				processData:false,
				beforeSend:function(){
					$('#btnRunImporter').attr('disabled','disabled');
					$('#btnRunImporter').val('Importing...');
				},
				success:function(data){
					if(data.success){
						$('#total_data').text(data.total_line);
						$('#total_rows').val(data.total_line);
						$('#csv_path').val(data.csv_path);
						$('#gallery_folder').val(data.zip_folder);
						start_import(0, 1, 0); // 0 = Current Row(Default 0), 1 = Continue next row, 0 = If not abort than 0 else 1. 
					}
					if(data.error){
						$('#message').html('<div class="alert alert-danger">'+data.error+'</div>');
						$('#btnRunImporter').attr('disabled',false);
						$('#btnRunImporter').val('Submit & Run Importer');
						$("#loading").hide();
					}
				}
			})
		});
		// First step for import product - start
		
	};
	
	function start_import(index_key, checkAjax, isAbort, product_id = 0, action = ''){
		
		$("#loading").show();
		
		/* if(action != ''){
			console.log(action)
		} */
		
		// index_key 	= Current Row Number.
		// checkAjax 	= 1 means continue, 0 means cancel import.
		// isAbort 		= 1 means abort import, 0 means continue with next row.
		
		if(checkAjax == 0 && isAbort == 1){
			$('#process').css('display', 'block');
			$.ajax({
				url: business_url+"/products/import-csv-data",
				method:"POST",
				dataType:"json",
				data:{
					is_abort: isAbort,
					product_ids: $("#product_ids").val(),
					total_rows: $("#total_rows").val(),
					csv_path: $("#csv_path").val(),
					gallery_folder: $("#gallery_folder").val(),
					index_key: index_key,
					action: action,
					old_product_id: product_id,
					inserted_id: $('#createdProductId').val()
				},
				success:function(response){
					if(response.msg_type == "success"){				
						$('#process').css('display', 'none');
						$('#process_data').text(0);
						$('.progress .progress-bar').css('width', '0%');
						$('#frmImportProduct')[0].reset();
						$('.import-hide').val('');
						$('#btnRunImporter').attr('disabled',false);
						$('#btnRunImporter').val('Submit & Run Importer');
						$('#importOldProductId').val('');
						$('#importIndex').val('');
						$('#importProductId').val('');
						callToasterAlert(response.msg,'info');
						return false;
					}					
					$("#loading").hide();
				}
			})
		}
		
		if(checkAjax == 1 && isAbort == 0){
			$('#process').css('display', 'block');
			$.ajax({
				url: business_url+"/products/import-csv-data",
				method:"POST",
				dataType:"json",
				data:{
					is_abort: isAbort,
					product_ids: $("#product_ids").val(),
					total_rows: $("#total_rows").val(),
					csv_path: $("#csv_path").val(),
					gallery_folder: $("#gallery_folder").val(),
					index_key: index_key,
					action: action,
					old_product_id: product_id,
					OldProductIds : $('#importOldProductId').val(),
					importIndex : $('#importIndex').val(),
					importProductId : $('#importProductId').val(),
					updateRowCnt : $('#updateRowCnt').val(),
					inserted_id: $('#createdProductId').val()
				},
				success:function(response){
					//console.log(response);
					if(response.msg_type == "success"){
						index_key = parseInt(response.row_number) + parseInt(1);
						var totalRows = $("#total_rows").val();
						var width = Math.round((index_key * 100) / (totalRows));
						$('#process_data').text(index_key);
						$('.progress .progress-bar').css('width', width + '%');
						
						if(totalRows >= index_key){
							$("#next_row").val(index_key);
							start_import(index_key, 1, 0);
							var lastId = $("#product_ids").val();
							var latestProductId = response.product_id;
							if(lastId == ""){
								$("#product_ids").val(latestProductId);
							}
							else
							{
								$("#product_ids").val(lastId+','+latestProductId);
							}							
						}

						if(response.oldProductId!==null && response.oldProductId!==undefined) {
							var oldProductIds = $('#importOldProductId').val();
							if (oldProductIds) {
								var oldProductIdStr = oldProductIds.split(",");
								var arr = oldProductIdStr.push(response.oldProductId);
								$('#importOldProductId').val(oldProductIdStr.join(','));
							} else {							
								$('#importOldProductId').val(response.oldProductId);
							}
						}

						if(response.importKey!==null && response.importKey!==undefined) {
							var importIndex = $('#importIndex').val();
							if (importIndex) {
								var importIndexStr = importIndex.split(",");
								importIndexStr.push(response.importKey);
								$('#importIndex').val(importIndexStr.join(','));
							} else {							
								$('#importIndex').val(response.importKey);
							}
						}	

						if(response.productIds!==null && response.productIds!==undefined) {
							var productIds = $('#importProductId').val();
							if (productIds) {
								var productIdStr = productIds.split(",");
								productIdStr.push(response.productIds);
								$('#importProductId').val(productIdStr.join(','));
							} else {							
								$('#importProductId').val(response.productIds);
							}
						}
						if(response.updateRowCnt!==null && response.updateRowCnt!==undefined) {
							$('#updateRowCnt').val(response.updateRowCnt);
						}
						if(response.inserted_id!==null && response.inserted_id!==undefined) {
							var productId = $('#createdProductId').val();
							if (productId) {
								var productIdsStr = productId.split(",");
								productIdsStr.push(response.inserted_id);
								$('#createdProductId').val(productIdsStr.join(','));
							} else {							
								$('#createdProductId').val(response.inserted_id);
							}
						}						
						if(index_key == totalRows){
							$('#frmImportProduct')[0].reset();
							$('#btnRunImporter').attr('disabled',false);
							$('#btnRunImporter').val('Submit & Run Importer');
							$('.import-hide').val('');
							callToasterAlert('All product import successfully!','success');
							//console.log('success')
							$("#loading").hide();
							return false;
						}
					}
					
					if(response.is_exists == 0 && response.msg_type == "error"){
						index_key = parseInt(response.row_number);
						var totalRows = $("#total_rows").val();
						var width = Math.round((index_key * 100) / (totalRows));
						$('#process_data').text(index_key);
						$('.progress .progress-bar').css('width', width + '%');
						callToasterErrorAlert(response.msg, index_key, response.product_id, false);
						return false;
					}
					
					if(response.is_exists == 1 && response.msg_type == "error"){
						callToasterErrorAlert(response.msg, index_key, response.product_id);
						return false;
					}
					
				}
			})
			
		}
	
	}

	/* Import products code end here */
	
	function callToasterErrorAlert(message_text, index_key, product_id, hideOverWriteBtn = true){
		Swal.fire({
		  title: 'Warning',
		  icon: 'warning',
		  text: message_text,
		  //animation: true,
		  showDenyButton: hideOverWriteBtn,
		  showCancelButton: true,
		  allowOutsideClick: false,
		  confirmButtonText: 'Skip',
		  denyButtonText: 'Overwrite',
		  cancelButtonText: 'Abort',
		}).then((result) => {
		  
		  if (result.value) {
			var nextRow = parseInt(index_key);
			var totalRows = $("#total_rows").val();
			
			var width = Math.round((nextRow * 100) / (totalRows));
			$('#process_data').text(nextRow);
			$('.progress .progress-bar').css('width', width + '%');
				
			if(nextRow == totalRows){				
				$('#frmImportProduct')[0].reset();
				$('#btnRunImporter').attr('disabled',false);
				$('#btnRunImporter').val('Submit & Run Importer');
				callToasterAlert('All product import successfully!','success');
				$("#loading").hide();
				return false;
			}
			else
			{
				start_import(nextRow, 1, 0, product_id, 'skip');
			}
		  }
		  
		  if (result.isDenied) {
			// If click on overwrite button
			//console.log("Overwrite == "+result.isDenied);
			start_import(parseInt(index_key), 1, 0, product_id, 'overwrite');
			
		  }
			if(result.isDismissed){
				//console.log("Abort == "+result.isDismissed);
				// Abort 
				var nextRow = parseInt(index_key) + parseInt(1);
				start_import(nextRow, 0, 1, 0, 'abort');
				$("#loading").hide();
			}
		  
		})
	}
	
	
	function callToasterAlert(title,type){		
		Swal.fire({
			title: title,
			type: type,			
			icon: type,
			toast: true,
			//animation: true,
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
	
	var downloadSample = function () {
		$("#downloadSample").click(function(){
			$('#downloadSample').html('Loading...').addClass('disabled');
			$('#productCsvSample').click();
			$('#downloadSample').html('<i class="fa fa-download mr-2"></i> Download Sample CSV').removeClass('disabled');
		})
	};	
	

    return {
		importProducts: function () {
            importProducts();
            downloadSample();
        },
    };
})();
