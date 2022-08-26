var FitnessStatus = (function () {
    var manage = function () {
        var table = $("#tblFitnessStatus").DataTable({
            processing: true,
            serverSide: true,
            ajax: superadmin_url + "/fitness-status",
            columns: [
                { data: "fitness_status", name: "fitness_status" },
                { data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                { targets: 0, searchable: true, className: "text-left" },
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-center" },
                { targets: 3, searchable: false, className: "text-center" },
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
		
		var form = $("form[name='frmAddFitnessStatus']");
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					fitness_status: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: superadmin_url+'/fitness-status/checkFitnessStatusExists',
					}
				},
				messages: {
					fitness_status: {
						required:	'Please enter fitness status.',
						minlength:	'Fitness status minimum length should be 4 character.',
						maxlength:	'Fitness status maximum length should be 70 character.',
						remote: 'Fitness status already exist.'
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
		
		var form = $("form[name='frmEditFitnessStatus']");
		var fitness_status_id = $("#fitness_status_id").val();
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					fitness_status: {
						required: true,
						minlength : 4,
						maxlength: 70,
						remote: superadmin_url+'/fitness-status/checkFitnessStatusExists?id='+fitness_status_id,
					}
				},
				messages: {
					fitness_status: {
						required:	'Please enter fitness status.',
						minlength:	'Fitness status minimum length should be 4 character.',
						maxlength:	'Fitness status maximum length should be 70 character.',
						remote: 'Fitness status already exist.'
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
			var url = superadmin_url+'/fitness-status/destroy/'+id;
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
