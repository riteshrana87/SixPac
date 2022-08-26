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
            <li class="breadcrumb-item">Edit profile</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

@if ($errors->any())
    <div class="alert alert-icon-left alert-light-danger mb-4 alertmsg" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
        <i data-feather="alert-circle"></i> <strong>Error!</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session()->get('success'))
    <div class="alert alert-icon-left alert-light-success mb-4 alertmsg" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
        <i data-feather="check"></i> <strong>Success!</strong> {{ session()->get('success') }}
    </div>
@endif

@if(session()->get('error'))
    <div class="alert alert-icon-left alert-light-danger mb-4 alertmsg" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
        <i data-feather="alert-circle"></i> <strong>Error!</strong>{{ session()->get('error') }}
    </div>
@endif

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Edit Profile</h5>
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
					<form class="form-horizontal" name="frmEditProfile" id="frmEditProfile" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('admin.updateProfile', $profile->id) }}">
					@csrf
					@method('PUT')
						<div class="row">
							<input type="hidden" name="pf_img" id="pf_img" value="{{ $old_avtar }}">
							<input type="hidden" name="user_id" id="user_id" value="{{ $profile->id }}">
							<div class="col-md-6">
								<div class="field mb-3">
									<input class="form-control req" type="text" name="name" id="name" placeholder="Enter your name" value="{{ $profile->name }}" maxlength="40">
								</div>
								<div class="field mb-3">
									<input class="form-control req" type="email" name="email" id="email" placeholder="Enter your email address" value="{{ $profile->email }}" maxlength="255">
								</div>

								<div class="field mb-3">
									<input class="form-control onlynumber req" name="phone" id="phone" type="text" placeholder="Enter your phone number" value="{{ $profile->phone }}" maxlength="15">
								</div>


								<div class="choose mb-2">
									<label id="male">
										<input name="gender" id="gender_1" type="radio" value="1" {{ (isset($profile->gender) && $profile->gender == 1)  ? 'checked' : '' }} > <span><i class="fa fa-male"></i></span>
									</label>
									<label id="female">
										<input name="gender" id="gender_0" type="radio" value="0" {{ (isset($profile->gender) && $profile->gender == 0)  ? 'checked' : '' }} > <span><i class="fa fa-female"></i></span>
									</label>
								</div>

								<div class="field mb-3">
									<input type="text" class="form-control req" name="date_of_birth" id="date_of_birth" placeholder="Enter your birth date" value="{{ date('d/m/Y', strtotime($profile->date_of_birth)) }}" maxlength="10">
								</div>
							</div>

							<div class="col-md-6">
								<input type="file" accept="image/x-png,image/jpeg" name="profile_photo" id="profile_photo" class="hide">
								<div class="cover-box text-center" id="uploadProfilePic">
									<?php if(!empty($profile->avtar)){ ?>
										<div id="profile-photo-preview" class="sessionphoto">
											<img src="{{ $profile->avtar_url }}" id="profile_photo_placeholder" width="185px" height="185px">
											<a href="javascript:void(0);" id="pf_edit" class="hovericon pf_edit"><i class="fa fa-pencil"></i></a>
											<a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a>
										</div>

										<div id="profile-photo-img" style="display:none;">
											<img src="{{ $profile->placeholder_url }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
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
										<img src="{{ $profile->avtar_url }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
										<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
									</div>
									<?php
									}
									?>
								</div>
							</div>
							<div class="col-md-6 text-right">
								<button type="submit" class="btn btn-shadow btn-success" name="btnSaveProfile" id="btnSaveProfile">Save</button>
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
