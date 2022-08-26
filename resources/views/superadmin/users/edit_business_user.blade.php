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
        <h5 class="m-b-10">{{ __('Business User') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/users/business-users') }}">Business users</a></li>
            <li class="breadcrumb-item">Edit business user</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Edit Business User</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
					<form class="form-horizontal spform" name="frmEditBusinessUser" id="frmEditBusinessUser" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('update-business-user') }}">
					@csrf
					@method('PUT')
						<input type="hidden" name="user_id" id="user_id" value="{{ $data->id }}">
						<input type="hidden" name="old_avtar" id="old_avtar" value="{{ $old_avtar }}">
						<input type="hidden" name="existingMedia[]" id="existingMedia_<?=$data->id?>" class="existingMedia" value="<?=$original_image?>" data-order="1">
						<div class="col-md-12 text-center mb-4">
							<label>Avtar: <span class="req_star">*</span></label>
							<div class="cover-box text-center" id="uploadProfilePic">
								<?php if(!empty($data->avtar_url)){ ?>
									<div id="profile-photo-preview" class="sessionphoto">
										<img src="{{ $data->avtar_url }}" id="profile_photo_placeholder" width="185px" height="185px">
										<a href="javascript:void(0);" id="pf_edit" class="hovericon pf_edit"><i class="fa fa-pencil"></i></a>
										<a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a>
										<a href="javascript:void(0)" id="pf_crop" class="cropMedia hovericon" data-id="1"><i class='fa fa-scissors'></i></a>
									</div>

									<div id="profile-photo-img" style="display:none;">
										<img src="{{ $data->placeholder_url }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
										<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
										<a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a>
										
									</div>
								<?php
								}
								else
								{
								?>
								<div id="profile-photo-preview" class="sessionphoto" style="display:none;"></div>
								<div id="profile-photo-img">
									<img src="{{ $data->avtar_url }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
									<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
								</div>
								<?php
								}
								?>
							</div>
							<input type="file" accept="image/x-png,image/jpeg" name="avtar" id="avtar" class="hide">
							<input type="hidden" name="avtarImg" id="avtarImg" value="">
						</div>

						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									<div class="field">
										<label>Name: <span class="req_star">*</span></label>
										<input class="form-control req" type="text" name="name" id="name" placeholder="Enter admin name" maxlength="70" value="{{ $data->name }}">
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
										<label>Company name: <span class="req_star">*</span></label>
										<input class="form-control req" type="text" name="company_name" id="company_name" placeholder="Enter company name" maxlength="70" value="{{ $company_name }}">
									</div>
								</div>
								
                                <div class="col-md-6">
									<div class="field">
										<label>Company URL:</label>
										<input class="form-control req" type="url" name="company_url" id="company_url" placeholder="Enter company url" maxlength="255" value="{{ $company_url }}">
									</div>
								</div>

								<div class="col-md-6">
									<div class="field">
										<label>Email: <span class="req_star">*</span></label>
										<input class="form-control req" type="email" name="email" id="email" placeholder="Enter email address" maxlength="255" value="{{ $data->email }}">
									</div>
								</div>

								<div class="col-md-6">
									<div class="field">
										<label>Phone: <span class="req_star">*</span></label>
										<input class="form-control USphone onlynumber req" type="text" name="phone" id="phone" placeholder="Enter phone number" maxlength="14" value="{{ $phone }}">
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
												<label>Status: </label><br>
												@php
												if($data->status == 1){
													$statusFlag = 'checked';
												}
												else
												{
													$statusFlag = '';
												}
												@endphp
												<input type="hidden" name="status" id="status" value="{{ $data->status }}">
												<input type="checkbox" name="setStatus" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" {{ $statusFlag }}>
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
                                    <a href={{ url('superadmin/users/business-users') }}>
                                        <button type="button" class="btn-hover color-11 m-b-20 btn-cancel" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button>
                                    </a>
								</div>
								<div class="col-md-6 text-right">
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
