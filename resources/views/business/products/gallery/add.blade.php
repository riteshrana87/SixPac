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
        <h5 class="m-b-10">{{ __('Products') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('business/products') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ url('business/products/edit') }}/{{ $data->id }}">{{ $data->product_title }}</a></li>
            <li class="breadcrumb-item">Add product media</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Add Product Media</h5>
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
					<form class="form-horizontal spform" name="frmAddProductGallery" id="frmAddProductGallery" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('business.save-gallery') }}">
					@csrf

						<div class="row">
							<div class="col-md-4 text-left">
								<button type="button" name="btnUploadGallery" id="btnUploadGallery" class="btn btn-info mb-0"><i class="fa fa-upload mr-2"></i> Upload Images/Videos</button><span id="loading" class="ml-2 hide"><img src="{{ asset('backend/assets/images/loading_circle.gif') }}"></span>
							</div>
							<div class="col-md-4 text-center">
								<p><span class="text-danger"><strong>NOTE:</strong></span> Maximum 5 files allow for product gallery.</p>
							</div>
							<div class="col-md-4 text-right">
								<a href="{{ url('business/products/edit') }}/{{ $data->id }}"><button type="button" class="btnImport btn btn-success mr-4"><i class="fa fa-pencil mr-2"></i>Edit Product</button></a>
								<a href="{{ url('business/products') }}"><button type="button" class="btnImport btn btn-secondary mr-4"><i class="fa fa-angle-double-left mr-2"></i>Back to Products</button></a>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<input type="hidden" name="productId" id="productId" value="{{ $data->id }}">
								<input type="hidden" name="total_files" id="total_files" value="{{ $totalFiles }}">
								<input class="hide" type="file" id="files" name="files[]" placeholder="Choose images" multiple accept=".mp4, .jpeg, .jpg, .png">

								<div class="mt-1">
									<div class="preview-image">
										<?php
										if(count($prodGallery) > 0){
										$fileUrlArr		= $prodGallery['file_url'];
										$galleryIdArr	= $prodGallery['gallery_id'];
										$fileTypeArr	= $prodGallery['file_type'];
										$fileLengthArr	= $prodGallery['file_length'];
										$fileSizeArr	= $prodGallery['file_size'];
										?>
										<div id="galleryBox" class="row">
											<?php
											if(count($fileUrlArr) > 0){
												$x = 0;
												foreach($fileUrlArr as $files){
													if($fileTypeArr[$x] != 'video/mp4' && $fileTypeArr[$x] != 'mp4'){
														?>
														<div><img src="<?php echo $files; ?>" width="200px" height="200px"><i class="fa fa-trash gallery_trash" data-id="<?php echo $galleryIdArr[$x]; ?>"></i></div>
														<?php
													}
													if($fileTypeArr[$x] == 'video/mp4' || $fileTypeArr[$x] == 'mp4'){
														?>
														<div><video src="<?php echo $files; ?>" controls width="200px" height="200px"></video><i class="fa fa-trash gallery_trash" data-id="<?php echo $galleryIdArr[$x]; ?>"></i></div>
														<?php
													}
													$x++;
												}
											}
											?>
										</div>
										<?php
										}
                                        else {
                                            ?>
                                            <div id="galleryBox" class="row">
                                                <div class="col-md-12 text-center">No any product media found.</div>
                                            </div>
                                            <?php
                                        }
										?>

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
