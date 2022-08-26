@extends('layouts.backend')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<!-- Page-header start -->
<div class="page-header card">
    <div class="card-block">
        <h5 class="m-b-10">{{ __('Settings') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item">Change password</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->


<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

				<div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Change Password</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
                </div>

				<div class="card-block">

					@if ($errors->any())
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-danger mb-4 alertmsg" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
									<i data-feather="alert-circle"></i> <strong>Error!</strong>
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					@endif

					@if(session()->get('success'))
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-success mb-4 alertmsg" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
									<i data-feather="check"></i> <strong>Success!</strong> {{ session()->get('success') }}
								</div>
							</div>
						</div>
					@endif

					@if(session()->get('error'))
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-danger mb-4 alertmsg" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
									<i data-feather="alert-circle"></i> <strong>Error!</strong>{{ session()->get('error') }}
								</div>
							</div>
						</div>
					@endif

					<!-- Form code start here -->
					<form class="form-horizontal spform" name="frmPassword" id="frmPassword" method="POST" autocomplete="off" action="{{ route('business.updatePassword', $user_id) }}">
							@csrf
							@method('PUT')
						<div class="row">
							<input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
							<div class="col-md-6">
								<div class="field mb-3">
                                    <label>Password: <span class="req_star">*</span></label>
									<input type="password" class="form-control" name="current_password" id="current_password" placeholder="Current password" maxlength="15">
								</div>
								<div class="field mb-3">
                                    <label>New password: <span class="req_star">*</span></label>
									<input type="password" class="form-control" name="new_password" id="new_password" placeholder="New password" maxlength="15">
								</div>

								<div class="field mb-3">
                                    <label>Confirm password: <span class="req_star">*</span></label>
									<input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm password" maxlength="15">
								</div>
								<div class="row">
									<div class="col-md-12 text-right">
										<button type="submit" class="btn-hover color-9 m-b-20 btn-save" name="btnSaveProfile" id="btnSaveProfile">{{ __('Save') }}</button>
									</div>
								</div>
							</div>

						</div>
					</form>
					<!-- Form code end here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
