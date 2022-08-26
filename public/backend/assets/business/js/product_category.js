var ProductCategory = (function () {
    var manage = function () {
        var table = $("#tblProductCategory").DataTable({
            processing: true,
            serverSide: true,
            ajax: business_url + "/products/product-category",
            columns: [
                { data: "category_name", name: "category_name" },
                { data: "created_at", name: "created_at" },
                { data: "created_by", name: "created_by" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                { targets: 0, searchable: true, className: "text-left" },
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: true, className: "text-center" },
                { targets: 4, searchable: false, className: "text-center" },
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
		$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
		});

		$('.active-status').on('switchChange.bootstrapSwitch', function(event, state) {
			if(state){ $("#status").val(1) } else { $("#status").val(0) }
		});
		
		var form = $("form[name='frmAddProductCategory']");
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					category_name: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: business_url+'/products/product-category/checkProductCategoryExists',
					}
				},
				messages: {
					category_name: {
						required:	'Please enter category name.',
						minlength:	'Category name minimum length should be 4 character.',
						maxlength:	'Category name maximum length should be 70 character.',
						remote: 'Category name already exist.'
					}
				}
			});
			if (form.valid()) {
				form.submit();
			}
		});
	};

	/** Edit record code start here **/
	var edit = function(){
		$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
		});

		$('.active-status').on('switchChange.bootstrapSwitch', function(event, state) {
			if(state){ $("#status").val(1) } else { $("#status").val(0) }
		});
		
		var form = $("form[name='frmEditProductCategory']");
		var category_id = $("#category_id").val();
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					category_name: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: business_url+'/products/product-category/checkProductCategoryExists?id='+category_id,
					}
				},
				messages: {
					category_name: {
						required:	'Please enter category name.',
						minlength:	'Category name minimum length should be 4 character.',
						maxlength:	'Category name maximum length should be 70 character.',
						remote: 'Category name already exist.'
					}
				}
			});
			if (form.valid()) {
				form.submit();
			}
		});

	}
	/** Edit record code end here **/

	/** View details code start here **/
	var view = function () {
		$('body').on('click', '.viewRecord', function () {
			$(".full_details").html("");
			$("#viewDetails").modal({backdrop: 'static', keyboard: false},'show');
			var id = $(this).attr("data-id");
			var url = $(this).attr("data-url");
			submitcall(url, {'object_id': id}, function (output) {
				$(".full_details").html(output);
			});
		});
	};
	/** View details code end here **/

    /** Delete record code start here **/
     var delete_record = function () {
		 $("body").on('click', '.delete', function () {
			var id = $(this).attr("data-id");
			var url = business_url+'/products/product-category/destroy/'+id;
			$("#deleteForm").attr('action', url);
			$("#DeleteModal").modal({backdrop: 'static', keyboard: false},'show');
	    });
		$("body").on('click', '#yesBtn', function () {
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