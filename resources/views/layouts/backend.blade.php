<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<title>@if(!empty($page_title)) {{ config('app.name', 'SixPac') }} - {{ $page_title }} @else {{ config('app.name', 'SixPac') }} @endif</title>
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
	<link rel="icon" href="{{ asset('backend/assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/icon/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/icon/icofont/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/jquery.mCustomScrollbar.css') }}">

    @stack('css')
	<!--- Theme related assets file added - end -->

    <!--- Page wise cdn url add code start here -->
    @if(!empty($cdnurl_css))
    @foreach($cdnurl_css as $cdncss)
        <link rel="stylesheet" href="{{ asset($cdncss) }}" type="text/css" >
    @endforeach
    @endif
    <!--- Page wise cdn url add add code end here -->

    <!--- Page wise extra css add code start here -->
    @if(!empty($extra_css))
    @foreach($extra_css as $otherCss)
        <link rel="stylesheet" href="{{ asset($otherCss) }}" type="text/css" >
    @endforeach
    @endif
    <!--- Page wise extra css add code end here -->

    <script type="text/javascript">
    var baseurl = {!! json_encode(url('/')) !!}
    var superadmin_url = {!! json_encode(url('/superadmin')) !!}
    var admin_url = {!! json_encode(url('/admin')) !!}
    var business_url = {!! json_encode(url('/business')) !!}
    var consumer_url = {!! json_encode(url('/consumer')) !!}
    // var csrf_token = {!! csrf_token() !!}
    var csrf_token = <?php echo "'".csrf_token()."';"; ?>;
    </script>
    <link rel="stylesheet" href="{{ asset('backend/assets/custom/css/custom.css') }}">
</head>
<body>

    <!-- Get user role name from logged in users id code start -->
    @if (Auth::user()->role == 1)
        @php $roleName = 'superadmin'; @endphp
    @endif

    @if (Auth::user()->role == 2)
        @php $roleName = 'admin'; @endphp
    @endif

    @if (Auth::user()->role == 3)
        @php $roleName = 'business'; @endphp
    @endif

    @if (Auth::user()->role == 4)
        @php $roleName = 'employee'; @endphp
    @endif

    @if (Auth::user()->role == 5)
        @php $roleName = 'consumer'; @endphp
    @endif
    <!-- Get user role name from logged in users id code end -->

    <!-- Pre-loader start -->
    @include('backend.layouts.pre-load')
    <!-- Pre-loader end -->

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <!--- Top header code start --->
             @include('layouts.include.topbar.'.$roleName)
            <!--- Top header code end --->

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper1">

                    <!--- Left sidebar code start --->
                    @include('layouts.include.sidebar.'.$roleName)
                    <!--- Left sidebar code end --->

                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('sweetalert::alert')

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

	<!-- Footer assets file added - start -->
    <script type="text/javascript" src="{{ asset('backend/assets/js/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/assets/js/popper.js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/assets/js/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/assets/js/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/assets/js/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/assets/js/modernizr/css-scrollbars.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/assets/js/script.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/vartical-demo.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@v2.3.7/dist/latest/bootstrap-autocomplete.min.js"></script>
	<!-- Footer assets file added - end -->

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

<script src="{{ asset('backend/assets/custom/js/custom.js') }}"></script>
@stack('scripts')

<!--- Page wise cdn url js add code start here -->
@if(!empty($cdnurl_js))
  @foreach($cdnurl_js as $cdnjs)
    <script src="{{ asset($cdnjs) }}"></script>
  @endforeach
 @endif
<!--- Page wise cdn url js add code end here -->

<!--- Page wise js add code start here -->
@if(!empty($extra_js))
  @foreach($extra_js as $otherJs)
    <script src="{{ asset($otherJs) }}"></script>
  @endforeach
 @endif
<!--- Page wise js add code end here -->

<!--- Page wise js add code start here -->
@if(!empty($page_js))
  @foreach($page_js as $pageJs)
    <script src="{{ asset($pageJs) }}"></script>
  @endforeach
 @endif
<!--- Page wise js add code end here -->
<script>

  $(document).ready(function() {
    @if(!empty($init))
      @foreach($init as $values)
        {{ $values }}
      @endforeach
    @endif
});
</script>

<!-- Logout modal code start here -->
<!--<script src="//unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>-->
<script src="//unpkg.com/sweetalert2@11.4.8/dist/sweetalert2.all.js"></script>
<script>
$(".logoutLink").on("click", function() {
	Swal.fire({
		title: 'Do you really want to leave?',
		//type: 'warning',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes',
		cancelButtonText: "No",
		//closeOnConfirm: true,
		//closeOnCancel: true
	}).then((result) => {
		if (result.value===true) {
			$('#logout-form').submit() // this submits the form
		}
	})
})
</script>

<!--
<div class="modal fade" id="LogoutModal">
    <div class="modal-dialog">
		<form action="" id="LogoutForm" method="get">
			<div class="modal-content">
				<div class="modal-header no-bottom-border">
				<h4 class="modal-title">Logout of account</h4>
				</div>
				<div class="modal-body no-top-border">
					{{ csrf_field() }}
					<p class="modal-text">Do you really want to leave?</p>
				</div>
				<div class="modal-footer">
					<button type="button" id="closeBtn" class="btn btn-transparnt" data-dismiss="modal" data-placement="bottom" data-toggle="tooltip" title="Cancel">Cancel</button>
					<button type="submit" name="yesLogoutBtn" id="yesLogoutBtn" class="btn btn-primary" data-dismiss="modal" data-placement="bottom" data-toggle="tooltip" title="Yes, log out">Yes, log out</button>
				</div>
			</div>
		</form>
    </div>
</div>
-->
<!-- Logout modal code end here -->
</body>
</html>
