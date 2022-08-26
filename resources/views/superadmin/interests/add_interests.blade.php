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
        <h5 class="m-b-10">{{ __('Interests') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/interests') }}">Interests</a></li>
            <li class="breadcrumb-item">Add interest</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Add Interest</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
					<form class="form-horizontal spform" name="frmAddInterest" id="frmAddInterest" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('store-interests') }}">
					@csrf
						<div class="row">
							<div class="col-md-6">
								<div class="field mb-4">
                                    <label>Interest name: <span class="req_star">*</span></label>
									<input class="form-control req" type="text" name="interest_name" id="interest_name" placeholder="Enter interest name" maxlength="70" value="{{ old('interest_name') }}">
								</div>
								<div class="field mb-4">
                                    <label>Status: </label><br>
									<input type="hidden" name="status" id="status" value="1">
								    <input type="checkbox" name="setStatus" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" checked>
								</div>
							</div>
							<div class="col-md-6">
                                <label>Interest icon: <span class="req_star">*</span></label>
                                <input type="file" accept="image/x-png,image/jpeg" name="interest_icon" id="interest_icon" class="hide">
								<div class="text-center user-info">
                                    <div id="interest-icon-preview" class="sessionphoto" style="display:none;">
									</div>
									<div id="interest-icon-img">
										<img src="{{ $noIcon }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
										<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
									</div>
                                </div>
							</div>

							<div class="col-md-6">
								<div class="row">
									<div class="col-md-6 text-left">
                                        <a href={{ url('superadmin/interests') }}>
                                            <button type="button" class="btn-hover color-11 m-b-20 btn-col-6-cancel" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button>
                                        </a>
									</div>
									<div class="col-md-6 text-right">
                                        <button type="submit" class="btn-hover color-9 m-b-20 btn-col-6-save" name="btnSubmit" id="btnSubmit">{{ __('Save') }}</button>
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
