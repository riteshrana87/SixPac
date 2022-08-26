const mediaObj = import('../../js/common/uploadmedia.js');

var EmployeeUsers = (function () {
    var manage = function () {
        var table = $("#tblEmployeeUsers").DataTable({
            processing: true,
            serverSide: true,
            ajax: business_url + "/users/employee-users",
			"fnDrawCallback": function() {
			   $('.status_switch').bootstrapSwitch();
			},
			order: [[ 0, "desc" ]],
            columns: [
				{ data: "id", name: "id" },
                { data: "name", name: "name" },
                { data: "email", name: "email" },
                { data: "phone", name: "phone" },
                { data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
				{ targets: 0, searchable: false, className: "text-left", visible:false},
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-left" },
                { targets: 3, searchable: true, className: "text-left" },
                { targets: 4, searchable: true, className: "text-left" },
                { targets: 5, searchable: true, className: "text-center" },
                { targets: 6, searchable: false, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                   className: "text-left",
                },
            ],
        });
    };
	
	/*** Change status code start here ***/
	$('body').on('switchChange.bootstrapSwitch', 'input[class="status_switch"]', function(event, state) {
		var id	= $(this).attr('data-id');
		var url	= business_url+'/users/employee-users/changeStatus/';		
		if(state){ $("#status_"+id).val(1) } else { $("#status_"+id).val(0) }
		$("#statusForm").attr('action', url);
		$("#StatusModal").modal({
			backdrop: 'static',
			keyboard: false
		});
		$("#user_id").val(id);
		if(state){ $("#new_status").val(1) } else { $("#new_status").val(0) }		
	});
	
	$("body").on('click', '#user_yesBtn', function () {
		$("#statusForm").submit();
	});
	
	$("body").on('click', '#user_closeBtn, #StausModalClose', function () {
		var id	= $("#user_id").val();
		if($('#status_'+id).val() == 1){ $('#status_'+id).bootstrapSwitch('state',false) } else { $('#status_'+id).bootstrapSwitch('state',true) }
	});	
	/*** Change status code end here ***/

    var add = function () {

		$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
		});

		$('.active-status').on('switchChange.bootstrapSwitch', function(event, state) {
			if(state){ $("#status").val(1) } else { $("#status").val(0) }
		});
		
		/* jQuery.validator.addMethod("phoneUS", function (phone, element) {
			phone = phone.replace(/\s+/g, "");
			return this.optional(element) || phone.length > 9 && phone.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
		}, "Please specify a valid phone number."); */
		
		var form = $("form[name='frmAddEmployeeUser']");
		$('#btnSubmit').click(function (e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					name: {
						required: true,
						minlength : 4,
						maxlength: 40,
						//lettersonly: true
					},
					user_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 50,
                        noSpace: true
                    },
					phone: {
						required: true,
						minlength : 10,
						maxlength: 15,
						//phoneUS: true,
						remote: business_url+'/settings/validateUserPhone',
					},
					/* city: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					state: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					country: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					zipcode: {
						required: true,
						minlength : 4,
						maxlength: 10,
					}, */
					avtar: {
						required: true,
					},
					email: {
						required: true,
						email: true,
						remote:business_url+'/settings/validateUserEmail',
					},
					password : {
						required: true,
						minlength : 6
					},
					confirm_password : {
						required: true,
						equalTo : "#password"
					}
				},
				messages: {
					name: {
						required:	'Please enter employee name.',
						minlength:	'Employee name minimum length should be 4 character.',
						maxlength:	'Employee name maximum length should be 40 character.',
					},
					user_name: {
                        required: "Please enter username.",
                        minlength:
                            "User name minimum length should be 4 character.",
                        maxlength:
                            "User name maximum length should be 50 character.",
                    },
					phone: {
						required:	'Please enter phone number.',
						minlength:	'Phone number minimum length should be 10 digit.',
						maxlength:	'Phone number maximum length should be 15 digit.',
						remote: 'Phone number already exist.'
					},
					/* city: {
						required:	'Please enter city.',
						minlength:	'City minimum length should be 3 character.',
						maxlength:	'City maximum length should be 50 character.',
					},
					state: {
						required:	'Please enter state.',
						minlength:	'State minimum length should be 3 character.',
						maxlength:	'State maximum length should be 50 character.',
					},
					country: {
						required:	'Please enter country.',
						minlength:	'Country minimum length should be 3 character.',
						maxlength:	'Country maximum length should be 50 character.',
					},
					zipcode: {
						required:	'Please enter zipcode.',
						minlength:	'Zipcode minimum length should be 4 character.',
						maxlength:	'Zipcode maximum length should be 10 character.',
					}, */
					avtar: {
						required:	'Please select avtar.',
					},
					email: {
						required:	'Please enter email address.',
						email:	'Please enter correct email address.',
						remote: 'Email already exist.'
					},
					password : {
						required: 'Please enter password.',
						minlength : 'Password minimum length should be 6 character.'
					},
					confirm_password : {
						required : 'Please enter confirm password.',
						equalTo : "Password and confirm password does not match."
					}
				}
			});
			if (form.valid()) {
				form.submit();
			}
		});
		
		/** Change icon code start here **/
		$("#avtar").hide();
        $("body").on("click", "#pf_edit, #pf_upload_icon", function() {
            $("#avtar").trigger("click");
        });
		/** Change icon code end here **/
		
		/** AJAX: Get US country state and city code start here **/
		/* $( document ).ready(function() {
			var c_id = $("#country").val();
			var url = business_url + "/settings/getUsStateAndCity";
			
			submitcall(url, { country_id: c_id }, function (result) {
				var data = jQuery.parseJSON(result);
				$("#state").html("<option value=''>Select state</option>");
				$.each(data.state, function(index, stateVal) {
					$("#state").append("<option value='"+stateVal.id+"'>" + stateVal.name + "</option>");
				});
				
				$("#city").html("<option value=''>Select city</option>");
				$.each(data.city, function(index, cityVal) {
					$("#city").append("<option value='"+cityVal.id+"'>" + cityVal.name + "</option>");
				});
			});
		}); */
		/** AJAX: Get US country state and city code end here **/

	};
	
	/** Edit record code start here **/
	var edit = function(){
		$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
		});

		$('.active-status').on('switchChange.bootstrapSwitch', function(event, state) {
			if(state){ $("#status").val(1) } else { $("#status").val(0) }
		});
		
		/* jQuery.validator.addMethod("phoneUS", function (phone, element) {
			phone = phone.replace(/\s+/g, "");
			return this.optional(element) || phone.length > 9 && phone.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
		}, "Please specify a valid phone number."); */
		
		var form = $("form[name='frmEditEmployeeUser']");
		var userId = $("#user_id").val();
		$('#btnSubmit').click(function (e) {
			e.preventDefault();
			form.validate({
				ignore: [],
				rules: {
					name: {
						required: true,
						minlength : 4,
						maxlength: 40,
					},
					user_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 50,
                        noSpace: true
                    },
					phone: {
						required: true,
						minlength : 10,
						maxlength: 15,
						/* phoneUS: true, */
						remote: business_url+'/settings/validateUserPhone?id='+userId,
					},
					/* city: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					state: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					country: {
						required: true,
						minlength : 3,
						maxlength: 50,
					},
					zipcode: {
						required: true,
						minlength : 4,
						maxlength: 10,
					}, */
					avtar: {
						required: function (element) {
							if($("#avtar").val() == "" && $("#old_avtar").val() != ""){
								return false
							}
							else
							{
								return true
							}
						}
					},
					email: {
						required: true,
						email: true,
						remote:business_url+'/settings/validateUserEmail?id='+userId,
					},
					/* password : {
						required: true,
						minlength : 6
					},
					confirm_password : {
						required: true,
						equalTo : "#password"
					} */
				},
				messages: {
					name: {
						required:	'Please enter employee name.',
						minlength:	'Employee name minimum length should be 4 character.',
						maxlength:	'Employee name maximum length should be 40 character.',
					},
					user_name: {
                        required: "Please enter username.",
                        minlength:
                            "User name minimum length should be 4 character.",
                        maxlength:
                            "User name maximum length should be 50 character.",
                    },
					phone: {
						required:	'Please enter phone number.',
						minlength:	'Phone number minimum length should be 10 digit.',
						maxlength:	'Phone number maximum length should be 15 digit.',
						remote: 'Phone number already exist.'
					},
					city: {
						required:	'Please enter city.',
						minlength:	'City minimum length should be 3 character.',
						maxlength:	'City maximum length should be 50 character.',
					},
					/* state: {
						required:	'Please enter state.',
						minlength:	'State minimum length should be 3 character.',
						maxlength:	'State maximum length should be 50 character.',
					},
					country: {
						required:	'Please enter country.',
						minlength:	'Country minimum length should be 3 character.',
						maxlength:	'Country maximum length should be 50 character.',
					},
					zipcode: {
						required:	'Please enter zipcode.',
						minlength:	'Zipcode minimum length should be 4 character.',
						maxlength:	'Zipcode maximum length should be 10 character.',
					}, */
					avtar: {
						required:	'Please select avtar.',
					},
					email: {
						required:	'Please enter email address.',
						email:	'Please enter correct email address.',
						remote: 'Email already exist.'
					},
					/* password : {
						required: 'Please enter password.',
						minlength : 'Password minimum length should be 6 character.'
					},
					confirm_password : {
						required : 'Please enter confirm password.',
						equalTo : "Password and confirm password does not match."
					} */
				}
			});
			if (form.valid()) {
				form.submit();
			}
		});

		/** Save employee profile picture code start here **/
		$("#avtar").hide();
		$("body").on("click", "#pf_edit, #pf_upload_icon", function() {
			$("#avtar").trigger("click");
		});
		/** Save employee profile picture code end here **/
	}
	/** Edit record code end here **/
	
	/*** Common javascript code start here  **/
	$("#avtar").on("change", function() {
		$("#old_avtar").val("");
		$("#avtar-error").html("");
		$("#profile-photo-img").hide();
		$("#profile-photo-preview").show();
		$('#avtarImg').val("");
		var imgPath = $(this)[0].value;
		var extn = imgPath.substring(imgPath.lastIndexOf(".") + 1).toLowerCase();
		if (extn == "png" || extn == "jpg" || extn == "jpeg") {
			if (typeof FileReader != "undefined") {
				var uploadedFile = document.getElementById("avtar");
				var size = parseFloat(uploadedFile.files[0].size / 1024).toFixed(2);
				if(size < 1024){
					var reader = new FileReader();
					reader.readAsDataURL($(this)[0].files[0]);
					var file = $(this)[0].files[0] || null;
					reader.onload = function(e) {
						mediaArr[1] = {};
                        mediaArr[1] = URL.createObjectURL(file);
						var image = new Image();
						image.src = e.target.result;
						image.onload = function () {
							var height = this.height;
							var width = this.width;
							if (width < 100 || width > 800) {
								callToaster('error','Avtar width should be beetween 100px to 800px.');
								$("#profile-photo-img").show();
								$("#profile-photo-preview").html("");
								$('#avtar').val("");
								$("#old_avtar").val("");
								return false;
							}
							else
							{
								var editBtn = '<a href="javascript:void(0);" id="pf_edit" class="hovericon lc_edit"><i class="fa fa-pencil"></i></a><a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a><a href="javascript:void(0);" id="pf_crop" class="hovericon cropMedia" data-id="1"><i class="fa fa-scissors"></i></a>';
								$("#profile-photo-preview").html("<img src='" +e.target.result +"' id='avtar_placeholder' width='185px' height='185px'>"+editBtn);
							}
						}
					}
				}
				else
				{
					callToaster('error','Please select a file less than 1 MB.');
					$("#profile-photo-img").show();
					$("#profile-photo-preview").html("");
					$('#avtar').val("");
					$("#old_avtar").val("");
					return false;
				}

			} else {
				alert("This browser does not support FileReader.");
				return false;
			}
		} else {
			callToaster('error','Please upload a JPG or PNG image.');
			$("#profile-photo-img").show();
			$("#profile-photo-preview").html("");
			$('#avtar').val("");
			$("#old_avtar").val("");
			return false;
		}
	});

	$("body").on("click", "#pf_delete", function() {
		$("#profile-photo-img").show();
		$("#profile-photo-preview").hide();
		$("#avtar").val("");
		$("#old_avtar").val("");
		$('#avtarImg').val("");
	});
	/*** Common javascript code end here  **/
	
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
			var url = business_url+'/users/employee-users/destroy/'+id;
			$("#deleteForm").attr('action', url);
			$("#DeleteModal").modal({backdrop: 'static', keyboard: false},'show');
	    });
		$("body").on('click', '#yesBtn', function () {
			$("#deleteForm").submit();
	    });
    };
	/** Delete record code end here **/

	function loadAutocomplete(business_url) {
        mediaObj.then(media => {
            media.initAutocomplete(business_url);
        });
    }

    return {
        init: function () {
            manage();
			view();
            delete_record();
        },
		add: function () {
			window.mediaArr = {};
            add();
            loadAutocomplete(business_url);
        },
		edit: function () {
			window.mediaArr = {};
			edit();
			loadAutocomplete(business_url);
		},
    };
})();
