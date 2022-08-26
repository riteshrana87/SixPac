@php $page_title = !empty($data->id) ? 'Edit Plan goal' : 'Add Plan goal';
$type = !empty($data->id) ? 'Edit' : 'Add'; @endphp
@extends('layouts.backend')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap-switch/custom/css/bootstrap-switch.css') }}">
@endpush

<!-- Page-header start -->
<div class="page-header card">
    <div class="card-block">
        <h5 class="m-b-10">{{ __('Plan Goal') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/get-fit/workout-type') }}">Plan goal</a></li>
            <li class="breadcrumb-item">{{$type}} plan goal</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{$type}} Plan Goal</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
					<form class="form-horizontal spform" name="frmAddEditPlanGoal" id="frmAddEditPlanGoal" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ empty($data) ? route('add-plan-goal') : route('edit-plan-goal',$data->id) }}">
					@csrf
					<input type="hidden" name="created_by" id="created_by" value="{{Auth::user()->id}}">
					<input type="hidden" name="id" id="id" value="{{ $data->id ?? null }}">
						<div class="row">
							<div class="col-md-6">
								<div class="field">
                                    <label>Plan goal: <span class="req_star">*</span></label>
									<input class="form-control characterlimit req" max-character="70" type="text" name="name" id="name" placeholder="Enter plan goal title" maxlength="70" value="{{ $data->name ?? old('name') }}">
                                    <span class="pull-right label label-default count_message_field" id="cm_name"> {{!empty($data->name) ? strlen($data->name) : 0 }} / 70 </span>
								</div>
								<div class="field">
                                    <label>Status: </label><br>
									<input type="hidden" name="status" id="status" value="{{ $data->status ?? '1' }}">
								    <input type="checkbox" name="setStatus" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" @if(!empty($data) && $data->status==1) checked @elseif(empty($data)) checked @endif>
								</div>
							</div>

							<div class="col-md-6">
                                <label>Icon: <span class="req_star">*</span></label>
                                <input type="file" accept="image/x-png,image/jpeg" name="icon_file" id="goal_icon" class="hide">
                                    <input type="hidden" name="old_icon" id="old_icon" value="{{ $data->icon_file ?? '' }}">
                                    <div class="text-center user-info">
                                        <?php if(!empty($data->icon_file)) {  ?>
										<div id="interest-icon-preview" class="sessionphoto">
											<img src="{{ $data->icon_file }}" id="profile_photo_placeholder" width="185px" height="185px">
											<a href="javascript:void(0);" id="pf_edit" class="hovericon pf_edit"><i class="fa fa-pencil"></i></a>
											<a href="javascript:void(0);" id="pf_delete" class="hovericon pf_delete"><i class="fa fa-trash"></i></a>
										</div>

										<div id="interest-icon-img" style="display:none;">
											<img src="{{ asset('backend/assets/images/no-icon.png') }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
											<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
										</div>
									    <?php } else { ?>
	                                    <div id="interest-icon-preview" class="sessionphoto" style="display:none;">
										</div>
										<div id="interest-icon-img">
											<img src="{{ asset('backend/assets/images/no-icon.png') }}" id="pf_edit" width="185px" height="185px" style="cursor:pointer;">
											<a href="javascript:void(0);" id="pf_upload_icon" class="pf_hovericon lc_edit"><i class="fa fa-plus"></i></a>
										</div>
                                        <?php } ?>
                                    </div>
							</div>
							<div class="col-md-6">
							<div class="row">
	                            <div class="col-md-6">
	                                <a href={{ url('superadmin/get-fit/plan-goal') }}>
	                                    <button type="button" class="btn-hover color-11 m-b-20 btn-col-6-cancel" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button>
	                                <button type="submit" class="btn-hover color-9 m-b-20 btn-col-6-save ml-2" name="btnSubmit" id="btnSubmit">{{ __('Save') }}</button>
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

@push('scripts')
<script type="text/javascript" src="{{ asset('plugins/bootstrap-switch/custom/js/bootstrap-switch.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/validation/js/jquery.form.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/validation/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/assets/superadmin/js/manage_plan_goal.js') }}" onload="add();"></script>
@endpush
