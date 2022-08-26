var OfferCode = (function () {
    var manage = function () {
        var table = $("#tblOfferCode").DataTable({
            processing: true,
            serverSide: true,
            ajax: superadmin_url + "/offer-codes",
            columns: [
                { data: "offer_code", name: "offer_code" },
                { data: "discount", name: "discount" },
                { data: "start_date", name: "start_date" },
                { data: "end_date", name: "end_date" },               
                { data: "created_by", name: "created_by" },
				{ data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                { targets: 0, searchable: true, className: "text-left" },
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: true, className: "text-left" },
                { targets: 4, searchable: true, className: "text-left" },
                { targets: 5, searchable: true, className: "text-left" },
                { targets: 6, searchable: false, className: "text-center" },
                { targets: 7, searchable: false, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-left",
                },
            ],
        });
    };

	/* $('#start_date').datepicker({
		format: 'dd/mm/yyyy',
	});
	$('#end_date').datepicker({
		format: 'dd/mm/yyyy',
	}); */
	
	$("#start_date").datepicker({
		startDate: new Date(),
		format: 'dd/mm/yyyy',
		autoclose: true,
	}).on('changeDate', function (selected) {
		var startDate = new Date(selected.date.valueOf());
		$('#end_date').datepicker('setStartDate', startDate);
	}).on('clearDate', function (selected) {
		$('#end_date').datepicker('setStartDate', null);
	});

	$("#end_date").datepicker({
		startDate: new Date(),
		format: 'dd/mm/yyyy',
		autoclose: true,
	}).on('changeDate', function (selected) {
	   var endDate = new Date(selected.date.valueOf());
	   $('#start_date').datepicker('setEndDate', endDate);
	}).on('clearDate', function (selected) {
	   $('#start_date').datepicker('setEndDate', null);
	});
	
    var add = function () {
		$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
		});

		$('.active-status').on('switchChange.bootstrapSwitch', function(event, state) {
			if(state){ $("#status").val(1) } else { $("#status").val(0) }
		});
		
		var form = $("form[name='frmAddOfferCode']");
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					offer_code: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: superadmin_url+'/offer-codes/checkOfferCodeExists',
					},
					discount: {
						required: true,
						range: [1, 100],
					},
					start_date: {
						required: true,
					},
					end_date: {
						required: true,
					}
				},
				messages: {
					offer_code: {
						required:	'Please enter offer code.',
						minlength:	'Offer code minimum length should be 4 character.',
						maxlength:	'Offer code maximum length should be 70 character.',
						remote: 'Offer code already exist.'
					},
					discount: {
						required:	'Please enter discount(%).',
						range: 'Discount should be between 1 to 100%',
					},
					start_date: {
						required:	'Please select offer start date.',
					},
					end_date: {
						required:	'Please select offer end date.',
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
		
		var form = $("form[name='frmEditOfferCode']");
		var offer_code_id = $("#offer_code_id").val();
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					offer_code: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: superadmin_url+'/offer-codes/checkOfferCodeExists?id='+offer_code_id,
					},
					discount: {
						required: true,
						range: [1, 100],
					},
					start_date: {
						required: true,
					},
					end_date: {
						required: true,
					}
				},
				messages: {
					offer_code: {
						required:	'Please enter offer code.',
						minlength:	'Offer code minimum length should be 4 character.',
						maxlength:	'Offer code maximum length should be 70 character.',
						remote: 'Offer code already exist.'
					},
					discount: {
						required:	'Please enter discount(%).',
						range: 'Discount should be between 1 to 100%',
					},
					start_date: {
						required:	'Please select offer start date.',
					},
					end_date: {
						required:	'Please select offer end date.',
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
			var url = superadmin_url+'/offer-codes/destroy/'+id;
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
