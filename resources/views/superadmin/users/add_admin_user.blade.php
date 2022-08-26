@extends('layouts.backend')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<link rel="stylesheet" href="{{ asset('backend/assets/css/cropper/cropper.css') }}"/>
<script src="{{ asset('backend/assets/js/cropper/cropper.js') }}"></script>
<!-- Page-header start -->
<div class="page-header card">
    <div class="card-block">
        <h5 class="m-b-10">{{ __('Admin User') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/users/admin-users') }}">Admin users</a></li>
            <li class="breadcrumb-item">Add admin user</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Add Admin User</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
                </div>


                <div class="card-block table-border-style">
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
					<form class="form-horizontal spform" name="frmAddAdminUser" id="frmAddAdminUser" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('store-admin-user') }}">
					@csrf
						<div class="col-md-12 text-center">
							<label>Avtar: <span class="req_star">*</span></label>
							<div class="text-center user-info">
								<div id="profile-photo-preview" class="sessionphoto" style="display:none;">
								</div>
								<div id="profile-photo-img" class="mb-4">
									<img src="{{ $placeholder_url }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
									<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
								</div>
							</div>
							<input type="file" accept="image/x-png,image/jpeg" name="avtar" id="avtar" class="hide">
							<input type="hidden" name="avtarImg" id="avtarImg" value="">
						</div>

						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									<div class="field">
										<label>Name: <span class="req_star">*</span></label>
										<input class="form-control req" type="text" name="name" id="name" placeholder="Enter admin name" maxlength="40" value="{{ old('name') }}">
									</div>
								</div>

								<div class="col-md-6">
									<div class="field">
										<label>Username: <span class="req_star">*</span></label>
										<input class="form-control req" type="text" name="user_name" id="user_name" placeholder="Enter admin user name" maxlength="50" value="{{ old('user_name') }}">
									</div>
								</div>

								<div class="col-md-6">
									<div class="field">
										<label>Email: <span class="req_star">*</span></label>
										<input class="form-control req" type="email" name="email" id="email" placeholder="Enter email address" maxlength="255" value="{{ old('email') }}">
									</div>
								</div>

								<div class="col-md-6">
									<div class="field">
										<label>Phone: <span class="req_star">*</span></label>
										<input class="form-control USphone onlynumber req" type="text" name="phone" id="phone" placeholder="Enter phone number" maxlength="14" value="{{ old('phone') }}">
									</div>
								</div>

								<div class="col-md-12">
									<div class="row">
										<div class="col-md-6">
											<div class="field">
												<label>Password: <span class="req_star">*</span></label>
												<input class="form-control req" type="password" name="password" id="password" placeholder="Enter password" maxlength="15" value="{{ old('password') }}">
											</div>
										</div>

										<div class="col-md-6">
											<div class="field">
												<label>Confirm password: <span class="req_star">*</span></label>
												<input class="form-control req" type="password" name="confirm_password" id="confirm_password" placeholder="Enter confirm password" maxlength="15" value="{{ old('confirm_password') }}">
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-12">
									<div class="field">
										<label>Address: </label>
										<input type="text" class="form-control req" name="address" id="address" placeholder="Enter address" maxlength="255" value="{{ old('address') }}">
									</div>
								</div>
								<div class="col-md-12">
									<div class="field">
										<label>Unit/Apt #: </label>
										<input class="form-control req" type="text" name="unit_apt" id="unit_apt" placeholder="Enter unit/apt #" maxlength="200" value="{{ old('unit_apt') }}">
									</div>
								</div>

								<div class="col-md-12">
									<div class="row">
										<div class="col-md-3">
											<div class="field">
												<label>City:</label>
												<input class="form-control req city-autocomplete" type="text" name="city_name"  id="city_name" placeholder="Enter city" autocomplete="off" data-url="{{ url ('superadmin/settings/getCityList/{query}') }}">
												<input class="form-control req" type="hidden" name="city"  id="city">
												<span id="loading" class="ml-2 hide"><img src="{{ asset('backend/assets/images/loading_circle.gif') }}" height="20" width="20"></span>
											</div>
										</div>

										<div class="col-md-3">
											<div class="field">
												<label>State:</label>
												<input class="form-control req" type="text" name="state_name"  id="state_name" readonly>
												<input class="form-control req" type="hidden" name="state"  id="state">
											</div>
										</div>

										<div class="col-md-3">
											<div class="field">
												<label>ZIP or Postal code:</label>
												<input class="form-control req" type="text" name="zipcode" id="zipcode" placeholder="Enter zipcode" maxlength="10" value="{{ old('zipcode') }}">
											</div>
										</div>
										<div class="col-md-3">
											<div class="field">
												<label>Country:</label>
												<input class="form-control req" type="text" name="country_name"  id="country_name" readonly>
												<input class="form-control req" type="hidden" name="country"  id="country">
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="row">
										<div class="col-md-6">
											<div class="choose">
												<label>Sex:</label><br>
												<label id="male">
													<input name="gender" id="gender_1" type="radio" value="1" checked > <span><i class="fa fa-male"></i></span>
												</label>
												<label id="female">
													<input name="gender" id="gender_0" type="radio" value="2"> <span><i class="fa fa-female"></i></span>
												</label>
											</div>
										</div>

										<div class="col-md-6">
											<div class="field mb-4">
												<label>Status: </label><br>
												<input type="hidden" name="status" id="status" value="1">
												<input type="checkbox" name="setStatus" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" checked>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>

						@include('common/cropImage')

						<div class="col-md-12 mt-4">
							<div class="row">
								<div class="col-md-6 text-left">

                                    <a href={{ url('superadmin/users/admin-users') }}><button class="btn-hover color-11 m-b-20 btn-save" type="button" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button></a>
								</div>
								<div class="col-md-6 text-right">
									{{-- <button type="submit" class="btn btn-shadow btn-success" name="btnSubmit" id="btnSubmit">Save</button> --}}
                                    <button type="submit" class="btn-hover color-9 m-b-20 btn-save" name="btnSubmit" id="btnSubmit">{{ __('Save') }}</button>
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
