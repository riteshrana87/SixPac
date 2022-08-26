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
        <h5 class="m-b-10">{{ __('Settings') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item">Edit profile</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->


<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Edit Profile</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
					<form class="form-horizontal spform" name="frmEditProfile" id="frmEditProfile" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('superadmin.updateProfile', $data->id) }}">
					@csrf
					@method('PUT')

						<input type="hidden" name="pf_img" id="pf_img" value="{{ $old_avtar }}">
						<input type="hidden" name="user_id" id="user_id" value="{{ $data->id }}">
						<input type="hidden" name="existingMedia[]" id="existingMedia_<?=$data->id?>" class="existingMedia" value="<?=$original_image?>" data-order="1">
						<div class="col-md-12 text-center mb-4">
						<label>Avtar: <span class="req_star">*</span></label>
							<div class="cover-box text-center" id="uploadProfilePic">
								<?php if(!empty($data->avtar)){ ?>
									<div id="profile-photo-preview" class="sessionphoto">
										<img src="{{ $data->avtar_url }}" id="profile_photo_placeholder" width="185px" height="185px">
										<a href="javascript:void(0);" id="pf_edit" class="hovericon pf_edit"><i class="fa fa-pencil"></i></a>
										<a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a>
										<a href="javascript:void(0)" id="pf_crop" class="cropMedia hovericon" data-id="1"><i class='fa fa-scissors'></i></a>
									</div>

									<div id="profile-photo-img" style="display:none;">
										<img src="{{ $data->placeholder_url }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
										<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
									</div>
								<?php
								}
								else
								{
								?>
								<div id="profile-photo-preview" class="sessionphoto" style="display:none;">
								</div>
								<div id="profile-photo-img">
									<img src="{{ $data->avtar_url }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
									<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
								</div>
								<?php
								}
								?>
							</div>
							<input type="file" accept="image/x-png,image/jpeg" name="profile_photo" id="profile_photo" class="hide">
							<input type="hidden" name="avtarImg" id="avtarImg" value="">
						</div>

						<div class="col-md-12">
							<div class="row">
                            
								<div class="col-md-6">
									<div class="field">
										<label>Name: <span class="req_star">*</span></label>
										<input class="form-control req" type="text" name="name" id="name" placeholder="Enter your name" value="{{ $data->name }}" maxlength="40">
									</div>
								</div>

								<div class="col-md-6">
									<div class="field">
										<label>Username: <span class="req_star">*</span></label>
										<input class="form-control req" type="text" name="user_name" id="user_name" placeholder="Enter admin user name" maxlength="50" value="{{ $data->user_name }}">
									</div>
								</div>
                            
								<div class="col-md-6">
									<div class="field">
										<label>Email: <span class="req_star">*</span></label>
										<input class="form-control req" type="email" name="email" id="email" placeholder="Enter your email address" value="{{ $data->email }}" maxlength="255">
									</div>
								</div>

								<div class="col-md-6">
									<div class="field">
										<label>Phone: <span class="req_star">*</span></label>
										<input class="form-control USphone onlynumber req" name="phone" id="phone" type="text" placeholder="Enter your phone number" value="{{ $phone }}" maxlength="14">
									</div>
								</div>

								<div class="col-md-12">
									<div class="field">
										<label>Address: </label>
										<input type="text" class="form-control req" name="address" id="address" placeholder="Enter address" maxlength="255" value="{{ $address }}">
									</div>
								</div>

								<div class="col-md-12">
									<div class="field">
										<label>Unit/Apt #: </label>
										<input class="form-control req" type="text" name="unit_apt" id="unit_apt" placeholder="Enter unit/apt #" maxlength="200" value="{{ $unit_apt }}">
									</div>
								</div>
							<div class="col-md-12">
								<div class="row">
										<div class="col-md-3">
											<div class="field">
												<label>City:</label>
												<input class="form-control req city-autocomplete" type="text" placeholder="Enter city" name="city_name"  id="city_name" value="{{ $city_name }}" autocomplete="off" data-url="{{ url ('superadmin/settings/getCityList/{query}') }}">
												<input class="form-control req" type="hidden" name="city"  id="city" value="{{ $city }}">
											</div>
										</div>
										<div class="col-md-3">
											<div class="field">
												<label>State:</label>
												<input class="form-control req" type="text" name="state_name"  id="state_name" value="{{$state_name}}" readonly>
												<input class="form-control req" type="hidden" name="state" id="state" value="{{$state}}">
											</div>
										</div>
										<div class="col-md-3">
											<div class="field">
												<label>ZIP or Postal code:</label>
												<input class="form-control req" type="text" name="zipcode" id="zipcode" placeholder="Enter zipcode" maxlength="10" value="{{ $zipcode }}">
											</div>
										</div>
										<div class="col-md-3">
											<div class="field">
												<label>Country:</label>
												<input class="form-control req" type="text" name="country_name"  id="country_name" value="{{ $country_name }}" readonly>
												<input class="form-control req" type="hidden" name="country"  id="country" value="{{ $country }}" readonly>
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
													<input name="gender" id="gender_1" type="radio" value="1" {{ (isset($data->gender) && $data->gender == 1)  ? 'checked' : '' }} > <span><i class="fa fa-male"></i></span>
												</label>
												<label id="female">
													<input name="gender" id="gender_0" type="radio" value="2" {{ (isset($data->gender) && $data->gender == 2)  ? 'checked' : '' }} > <span><i class="fa fa-female"></i></span>
												</label>
											</div>
										</div>

										<div class="col-md-6">
											<div class="field mb-4">
												<label>Date of birth: <span class="req_star">*</span></label>
												<input type="text" class="form-control req" name="date_of_birth" id="date_of_birth" placeholder="Enter your birth date" value="{{ date('d/m/Y', strtotime($data->date_of_birth)) }}" maxlength="10">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						@include('common/cropImage')

						<div class="col-md-12 mt-4">
							<div class="row">
								<div class="col-md-12 text-right">
                                    <button style="width:10%;" type="submit" class="btn-hover color-9 m-b-20 btn-save" name="btnSaveProfile" id="btnSaveProfile">{{ __('Save') }}</button>
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
