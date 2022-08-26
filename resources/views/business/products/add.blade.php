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
        <h5 class="m-b-10">{{ __('Products') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('business/products') }}">Products</a></li>
            <li class="breadcrumb-item">Add product</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Add Product</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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

					<form class="form-horizontal spform" name="frmAddProduct" id="frmAddProduct" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('business.store_products') }}">
					@csrf

						<div class="row">
							<div class="col-md-6">
								<div class="field mb-4">
									<label>Product title: <span class="req_star">*</span></label>
									<input class="form-control characterlimit req" max-character="70" type="text" name="product_title" id="product_title" placeholder="Enter product title" maxlength="70" value="{{ old('product_title') }}">
									<span class="pull-right label label-default count_message_field" id="cm_product_title">0 / 70</span>
								</div>
								<div class="field mb-4">
									<label>Product category: <span class="req_star">*</span></label>
									<select class="form-control req" type="text" name="category_name" id="category_name" placeholder="Select product category">
										<option value="">Select category</option>
										@if(count($product_category) > 0)
											@foreach($product_category as $category)
											<option value="{{ $category->id }}">{{ $category->category_name }}</option>
											@endforeach
										@endif
									</select>
								</div>
								<div class="field mb-4">
									<label>Description: <span class="req_star">*</span></label>
									<textarea class="form-control characterlimit req" max-character="255" type="text" name="product_description" id="product_description" placeholder="Enter product description" maxlength="255">{{ old('product_description') }}</textarea>
									<span class="pull-right label label-default count_message_field" id="cm_product_description">0 / 255</span>
								</div>
								<div class="field mb-4">
									<label>SKU: <span class="req_star">*</span></label>
									<input class="form-control req" type="text" name="sku" id="sku" placeholder="Enter product SKU" maxlength="15" value="{{ old('sku') }}">
								</div>
								<div class="field mb-4">
									<label>Quantity: <span class="req_star">*</span></label>
									<input class="form-control onlynumber req" type="text" name="quantity" id="quantity" placeholder="Enter product quantity" maxlength="10" value="{{ old('quantity') }}">
								</div>
								<div class="field mb-4">
									<label>Cost Price($): <span class="req_star">*</span></label>
									<input class="form-control numerordecimal req" type="text" name="cost_price" id="cost_price" placeholder="Enter product cost price" maxlength="10" value="{{ old('cost_price') }}">
								</div>

								<div class="field mb-4">
									<label>Sell Price($): <span class="req_star">*</span></label>
									<input class="form-control numerordecimal req" type="text" name="sell_price" id="sell_price" placeholder="Enter product sell price" maxlength="10" value="{{ old('sell_price') }}">
								</div>
								<div class="field mb-4">
									<label>Status</label><br>
									<input type="hidden" name="status" id="status" value="1">
								    <input type="checkbox" name="setStatus" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" checked>
								</div>
							</div>

							<div class="col-md-6">
                                <label>Product media: </label>
								<div class="row pt-2">
									<div class="col-md-6 text-left">
										<span class="text-danger"><strong>Important Notes:</strong></span>
										<ul>
											<li>1. Maximum 5 files allow for product gallery.</li>
											<li>2. Image file should be .jpg, .jpeg or .png extenstion.</li>
											<li>3. Image file size should be less than 300KB.</li>
										</ul>
									</div>

									<div class="col-md-6 text-left">
										<span class="text-danger"><strong>&nbsp;</strong></span>
										<ul>
											<li>4. Only 1 video file allowed.</li>
											<li>5. Video file should be .mp4 extenstion.</li>
											<li>6. Video file size should be less than 1MB.</li>
											<li>7. Minimum dimension of cropped image should be (250px X 250px).</li>
										</ul>
									</div>
								</div>
								<!-- File upload code start here -->
								<input type="hidden" name="totalFiles" id="totalFiles" value="0">
								<div id="proGallery">
									<div class="store"></div>
									<ul class="preview sortable"></ul>
									<input id="gallery_files" type="file" name="gfiles[]" multiple class="hide" accept=".jpeg, .jpg, .png, .mp4, .mov, .mkv, .webm">
								</div>
								<div class="row">
									<div class="col-md-12 text-center mt-4">
										<button type="button" name="btnUploadGallery" id="btnUploadGallery" class="btn btn-info mb-0"><i class="fa fa-upload mr-2"></i> Select Media files</button><span id="loading" class="ml-2 hide"><img src="{{ asset('backend/assets/images/loading_circle.gif') }}"></span>
									</div>
								</div>
								<!-- File upload code end here -->
							</div>
						</div>

						@include('common/cropImage')

						<div class="row">
							<div class="col-md-6 text-left">
                                <a href={{ url('business/products') }}>
                                    <button type="button" class="btn-hover color-11 m-b-20 btn-cancel" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button>
                                </a>
							</div>
							<div class="col-md-6 text-right">
								<button type="submit" class="btn-hover color-9 m-b-20 btn-save" name="btnSubmit" id="btnSubmit">{{ __('Save') }}</button>
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
