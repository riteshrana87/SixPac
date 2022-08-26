var Products = (function () {
    var manage = function () {
        var table = $("#tblProducts").DataTable({
            processing: true,
            serverSide: true,
            ajax: business_url + "/products",
			order: [[ 0, "desc" ]],
            columns: [
                { data: "id", name: "id" },
                { data: "product_title", name: "product_title" },
                { data: "category_id", name: "category_id" },
                { data: "user_id", name: "user_id" },
				{ data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                { targets: 0, searchable: true, className: "text-center", visible:false },
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: true, className: "text-left" },
                { targets: 4, searchable: true, className: "text-left" },
                { targets: 5, searchable: false, className: "text-center" },
                { width: 200, targets: 6, searchable: false, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-left",
                },
            ],
        });
    };
	
	var archiveProducts = function () {
        var table = $("#tblProducts").DataTable({
            processing: true,
            serverSide: true,
            ajax: business_url + "/products/archive-products",
			order: [[ 0, "desc" ]],
            columns: [
                { data: "id", name: "id" },
                { data: "product_title", name: "product_title" },
                { data: "category_id", name: "category_id" },
                { data: "user_id", name: "user_id" },
				{ data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                { targets: 0, searchable: true, className: "text-center", visible:false },
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: true, className: "text-left" },
                { targets: 4, searchable: true, className: "text-left" },
                { targets: 5, searchable: false, className: "text-center" },
                { width: 200, targets: 6, searchable: false, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-left",
                },
            ],
        });
    };

	/** view record code start here **/
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
    /** view record code end here **/
	
	
	/** view archive record code start here **/
    var viewArchive = function () {
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
    /** view archive record code end here **/
	
    /** Delete record code start here **/
    var delete_record = function () {
		$("body").on('click', '.delete', function () {
			var id = $(this).attr("data-id");
			var url = business_url+'/products/destroy/'+id;
			$("#deleteForm").attr('action', url);
			$("#DeleteModal").modal({backdrop: 'static', keyboard: false},'show');
	    });
		$("body").on('click', '#yesBtn', function () {
			$("#deleteForm").submit();
	    });
	};
    /** Delete record code end here **/
	
	/** Permanently Delete record code start here **/
    var force_delete_record = function () {
		 $("body").on('click', '.delete', function () {
			var id = $(this).attr("data-id");
			var url = business_url+'/products/force-delete/'+id;
			$("#deleteForm").attr('action', url);
			$("#DeleteModal").modal({backdrop: 'static', keyboard: false},'show');
	    });
		$("body").on('click', '#yesDeleteBtn', function () {
			$("#deleteForm").submit();
	    });
    };
	/** Permanently record code end here **/
	
	/** Restore record code start here **/
     var restore_record = function () {
		 $("body").on('click', '.restore', function () {
			var id = $(this).attr("data-id");
			var url = business_url+'/products/restore/'+id;
			$("#restoreForm").attr('action', url);
			$("#restoreModal").modal({backdrop: 'static', keyboard: false},'show');
	    });
		$("body").on('click', '#yesBtn', function () {
			$("#restoreForm").submit();
	    });
    };
	/** Restore record code end here **/
	
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
        init: function () {
            manage();
			view();
            delete_record();
        },
		add: function () {
            /* add(); */
        },
		edit: function () {
			/* edit(); */
		},
		archiveProducts: function () {
			archiveProducts();
			viewArchive();
			force_delete_record();
            restore_record();
		},
    };
})();
