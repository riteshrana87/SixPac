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
            <li class="breadcrumb-item">Import product</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">


				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<h5 class="mb-0">Import Products</h5>
						</div>
						<div class="col-md-8 text-right">
							<a href="{{ $sampleFilePath }}" id="productCsvSample" rel="nofollow"><button type="button" class="downloadSample btn btn-secondary mr-4" id="downloadSample"><i class="fa fa-download mr-2"></i>Download Sample CSV</button></a>
						</div>
					</div>
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

					<div class="form-group hide" id="process">
						<div class="progress product_progress">
							<div class="progress-bar progress-bar-striped active product_progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100">
								<span id="process_data">0</span> - <span id="total_data">0</span>
							</div>
						</div>
					</div>
					<span id="message"></span>

					<div class="row">
						<div class="col-md-6">
							<form class="form-horizontal spform" name="frmImportProduct" id="frmImportProduct" method="POST" autocomplete="off" enctype="multipart/form-data">
							@csrf
								<div class="row pb-4">
									<div class="col-md-12">
										<div class="field mb-4">
											<label>Select product CSV file: <span class="req_star">*</span></label>
											<input class="form-control mfile req" type="file" name="csv_file" id="csv_file" placeholder="Upload csv file" accept=".csv">
										</div>
									</div>
									<div class="col-md-12">
										<div class="field mb-4 mt-2">
											<label>Select product media ZIP file:</label>
											<input class="form-control mfile req" type="file" name="zip_file" id="zip_file" placeholder="Upload zip file" accept=".zip">
										</div>
									</div>
									<div class="col-md-12 pt-4">
										<div class="field mb-4 pt-2">
											<input type="hidden" name="hidden_field" value="1" />
											<button type="submit" class="btn btn-shadow btn-success" name="btnRunImporter" id="btnRunImporter">Submit & Run Importer</button><span id="loading" class="ml-2 hide"><img src="{{ asset('backend/assets/images/loading_circle.gif') }}"></span>
										</div>
									</div>
								</div>
								<br>
								<input type="hidden" name="csv_path" id="csv_path" value="" class="import-hide">
								<input type="hidden" name="gallery_folder" id="gallery_folder" value="" class="import-hide">
								<input type="hidden" name="total_rows" id="total_rows" value="0" class="import-hide">
								<input type="hidden" name="next_row" id="next_row" value="0" class="import-hide">
								<input type="hidden" name="product_ids" id="product_ids" value="" class="import-hide">
								<input type="hidden" name="importOldProductId" id="importOldProductId" value="" class="import-hide">
								<input type="hidden" name="importIndex" id="importIndex" value="" class="import-hide">
								<input type="hidden" name="importProductId" id="importProductId" value="" class="import-hide">
								<input type="hidden" name="updateRowCnt" id="updateRowCnt" value="" class="import-hide">
								<input type="hidden" name="createdProductId" id="createdProductId" value="" class="import-hide">
							</form>
						</div>
						<div class="col-md-6">
							<span class="text-danger"><strong>Important Notes:</strong></span>
							<ul class="productNotes">
								<li>1. Do not close browser or go other page while start import product process.</li>
								<li>2. Product CSV file size must be less than 2MB.</li>
								<li>3. Product media ZIP file size must be less than 125MB.</li>
								<li>4. Maximum 5 files allow for product gallery.</li>
								<li>5. Image file should be .jpg, .jpeg or .png extenstion.</li>
								<li>6. Image file size should be less than 300KB.</li>
								<li>7. Only 1 video file allowed.</li>
								<li>8. Video file should be .mp4 extenstion.</li>
								<li>9. Video file size should be less than 1MB.</li>
							</ul>
						</div>
					</div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
