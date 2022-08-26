var ProfanityWord = (function () {
    var manage = function () {
        var table = $("#tblProfanityWord").DataTable({
            processing: true,
            serverSide: true,
            ajax: superadmin_url + "/profanity-words",
            columns: [
				{ data: 'id', name: 'id' },
                { data: "word", name: "word" },
				{ data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            aaSorting: [[2, 'desc']],
            columnDefs: [
                { targets: 0, searchable: false, className: "text-center", width: "10%" },
                { targets: 1, searchable: true, className: "text-left", width: "40%" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: false, className: "text-center" },
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
		
		var form = $("form[name='frmAddProfanityWord']");
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					profanity_word: {
						required: true,
						minlength : 2,
						maxlength: 20,
						remote: superadmin_url+'/profanity-words/checkWordExists',
					}
				},
				messages: {
					profanity_word: {
						required:	'Please enter profanity word.',
						minlength:	'Profanity word minimum length should be 2 character.',
						maxlength:	'Profanity word maximum length should be 20 character.',
						remote: 'Offer code already exist.'
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
		
		var form = $("form[name='frmEditProfanityWord']");
		var word_id = $("#word_id").val();
		
		$("body").on("click", "#btnSubmit", function(e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					profanity_word: {
						required: true,
						minlength : 2,
						maxlength: 20,
						remote: superadmin_url+'/profanity-words/checkWordExists?id='+word_id,
					}
				},
				messages: {
					profanity_word: {
						required:	'Please enter profanity word.',
						minlength:	'Profanity word minimum length should be 2 character.',
						maxlength:	'Profanity word maximum length should be 20 character.',
						remote: 'Profanity word already exist.'
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
			var url = superadmin_url+'/profanity-words/destroy/'+id;
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
