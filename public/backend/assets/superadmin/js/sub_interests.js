var SubInterests = (function () {
    var manage = function () {
        var table = $("#tblSubInterests").DataTable({
            processing: true,
            serverSide: true,
            ajax: superadmin_url + "/interests/sub-interests",
			columns: [
                { data: "sub_interest_name", name: "sub_interest_name" },
                { data: "interest_id", name: "interest_id" },
                { data: "created_at", name: "created_at" },
                { data: "created_by", name: "created_by" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
			aaSorting: [[2, 'desc']],
            columnDefs: [
                { targets: 0, searchable: true, className: "text-left"},
                { targets: 1, searchable: true, className: "text-left"},
                { targets: 2, searchable: true, className: "text-left"},
                { targets: 3, searchable: true, className: "text-left"},
                { targets: 4, searchable: false, className: "text-center"},
                { targets: 5, searchable: false, className: "text-center"},
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

		var form = $("form[name='frmAddSubInterest']");
		$('#btnSubmit').click(function (e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					interest_name: {
						required: true,
					},
					sub_interest_name: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: superadmin_url+'/interests/sub-interests/checkSubInterstExists',						
					}
				},
				messages: {
					interest_name: {
						required:	'Please select interest.',
					},
					sub_interest_name: {
						required:	'Please enter sub interest.',
						minlength:	'Sub interest name minimum length should be 4 character.',
						maxlength:	'Sub interest name maximum length should be 70 character.',
						remote: 'Sub interest name already exist.'
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

		var form = $("form[name='frmEditSubInterest']");
		var subInterestId = $("#sub_interest_id").val();
		
		$('#btnSubmit').click(function (e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					interest_name: {
						required: true,
					},
					sub_interest_name: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: superadmin_url+'/interests/sub-interests/checkSubInterstExists?id='+subInterestId,						
					}
				},
				messages: {
					interest_name: {
						required:	'Please select interest.',
					},
					sub_interest_name: {
						required:	'Please enter sub interest.',
						minlength:	'Sub interest name minimum length should be 4 character.',
						maxlength:	'Sub interest name maximum length should be 70 character.',
						remote: 'Sub interest name already exist.'
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
			var url = superadmin_url+'/interests/sub-interests/destroy/'+id;
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
