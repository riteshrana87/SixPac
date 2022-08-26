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
        <h5 class="m-b-10">{{ __('Offer Code') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/offer-code') }}">Offer code</a></li>
            <li class="breadcrumb-item">Edit offer code</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Edit Offer Code</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
					<form class="form-horizontal spform" name="frmEditOfferCode" id="frmEditOfferCode" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('update-offer-code') }}">
					@csrf
					@method('PUT')
						<input type="hidden" name="offer_code_id" id="offer_code_id" value="{{ $data->id }}">
						<div class="row">
							<div class="col-md-6">
								<div class="field mb-4">
                                    <label>Offer code: <span class="req_star">*</span></label>
									<input class="form-control req" type="text" name="offer_code" id="offer_code" placeholder="Enter fiteness status" maxlength="70" value="{{ $data->offer_code }}">
								</div>
								<div class="field mb-4">
                                    <label>Discount: <span class="req_star">*</span></label>
									<input class="form-control numerordecimal req" type="text" name="discount" id="discount" placeholder="Enter discount(%)" maxlength="5" value="{{ $data->discount }}">
								</div>
								<div class="field mb-4">
                                    <label>Start date: <span class="req_star">*</span></label>
									<input class="form-control req" type="text" name="start_date" id="start_date" placeholder="Select start date" maxlength="10" value="{{ date('d/m/Y', strtotime($data->start_date)) }}">
								</div>
								<div class="field mb-4">
                                    <label>End date: <span class="req_star">*</span></label>
									<input class="form-control req" type="text" name="end_date" id="end_date" placeholder="Select end date" maxlength="10" value="{{ date('d/m/Y', strtotime($data->end_date)) }}">
								</div>
								<div class="field mb-4">
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
								<div class="row">
									<div class="col-md-6 text-left">
										<a href={{ url('superadmin/offer-codes') }}>
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
