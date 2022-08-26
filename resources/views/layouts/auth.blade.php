<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<title>{{ config('app.name', 'SIXPAC') }}</title>
	<!-- HTML5 Shim and Respond.js IE9 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<!--- Theme related assets file added - start -->
	<!-- Favicon icon -->
    <link rel="icon" href="{{ asset('backend/assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/icon/themify-icons/themify-icons.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/icon/icofont/css/icofont.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/style.css') }}">

	<!--- Theme related assets file added - end -->
    <link rel="stylesheet" href="{{ asset('backend/assets/custom/css/custom.css') }}">
</head>
<body class="fix-menu">
	<section class="login p-fixed d-flex text-center common-img-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="login-card card-block auth-body mr-auto ml-auto">
						@yield('content')
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('sweetalert::alert')
	<!-- Footer assets file added - start -->
	<script type="text/javascript" src="{{ asset('backend/assets/js/jquery/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('backend/assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('backend/assets/js/popper.js/popper.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('backend/assets/js/bootstrap/js/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('backend/assets/js/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
	<script type="text/javascript" src="{{ asset('backend/assets/js/modernizr/modernizr.js') }}"></script>
	<script type="text/javascript" src="{{ asset('backend/assets/js/modernizr/css-scrollbars.js') }}"></script>
	<script type="text/javascript" src="{{ asset('backend/assets/js/common-pages.js') }}"></script>
	<script>
	$("#loginWith").change(function(){
		$("#email").val("");
		var loginWith = $(this).val();
		if(loginWith == 2){
			$("#email").attr("placeholder","Enter your phone number");
			$("#email").attr("maxlength","14");
			$("#email").attr("type","text");
			$("#email").addClass("USphone onlynumber");

			$(".USphone").on("keydown keyup", function () {
				if($("#email").attr("type") == "text"){
					var numbers = $(this).val().replace(/\D/g, ""),
					char = { 0: "(", 3: ") ", 6: "-" };
					var usNumber = "";
					for (var i = 0; i < numbers.length; i++) {
						usNumber += (char[i] || "") + numbers[i];
						$(this).val(usNumber);
					}
				}
			});
		}
		else
		{
			$("#email").attr("placeholder","Enter your email address");
			$("#email").attr("maxlength","200");
			$("#email").attr("type","email");
			$("#email").removeClass("USphone onlynumber");
		}
	})
	</script>

    <script src="{{ asset('backend/assets/custom/js/custom.js') }}"></script>

	<!-- Footer assets file added - end -->
</body>
</html>
