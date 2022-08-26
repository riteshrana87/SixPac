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
            <li class="breadcrumb-item">Archived product listing</li>
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

                <div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<h5 class="mb-0">Archived Product Listing</h5>
						</div>
						<!--<div class="col-md-8 text-right">
							<a href={{ url('business/products/add') }}><button type="button" class="btn btn-info"><i class="fa fa-plus mr-2"></i>Add New</button></a>
						</div>
						-->
					</div>
                </div>


                <div class="sp_table">
                    <div class="table-responsive">
                        <table id="tblProducts" class="table table-bordered">
                            <thead>
		                        <tr>
		                            <th>Id</th>
		                            <th>Product Title</th>
		                            <th>Category</th>
									<th>Created by</th>
									<th>Created date</th>
                                    <th>Status</th>
		                            <th class="no-sort" width="150px">Action</th>
		                        </tr>
		                    </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete product code start here -->
<div class="modal fade" id="DeleteModal">
    <div class="modal-dialog">
        <form action="" id="deleteForm" method="post">
            <div class="modal-content">
                <div class="modal-header no-bottom-border">
                <h4 class="modal-title">Delete confirmation</h4>
                </div>
                <div class="modal-body no-top-border">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <p class="modal-text">Are you sure you want to delete this product permanently?</p>
                </div>
                <div class="modal-footer">
                    <div class="col-6 text-left">
                        <button type="button" class="btn-hover color-11 btn-w100" name="closeBtn" id="closeBtn" data-dismiss="modal" data-dismiss="modal" title="Cancel">{{ __('Cancel') }}</button>
                    </div>

                    <div class="col-6 text-right">
                        <button type="submit" class="btn-hover color-9 btn-w100" name="yesDeleteBtn" id="yesDeleteBtn" data-dismiss="modal" data-dismiss="modal" title="Yes, Delete">{{ __('Yes, Delete') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Delete product code end here -->

<!-- Restore product code start here -->
<div class="modal fade" id="restoreModal">
    <div class="modal-dialog">
        <form action="" id="restoreForm" method="post">
            <div class="modal-content">
                <div class="modal-header no-bottom-border">
                <h4 class="modal-title">Restore confirmation</h4>
                </div>
                <div class="modal-body no-top-border">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <p class="modal-text">Are you sure you want to restore this product?</p>
                </div>
                <div class="modal-footer">
                    <div class="col-6 text-left">
                        <button type="button" class="btn-hover color-11 btn-w100" name="closeBtn" id="closeBtn" data-dismiss="modal" data-dismiss="modal" title="Cancel">{{ __('Cancel') }}</button>
                    </div>

                    <div class="col-6 text-right">
                        <button type="submit" class="btn-hover color-9 btn-w100" name="yesBtn" id="yesBtn" data-dismiss="modal" data-dismiss="modal" title="Yes, Restore">{{ __('Yes, Restore') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Restore product code end here -->

<!-- View product code start here -->
<div class="modal fade viewDetails" id="viewDetails">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Product details</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content full_details">
			</div>
		</div>
		{{-- <div class="modal-footer">
		  <button type="button" class="btn btn-danger" data-dismiss="modal" title="Close">Close</button>
		</div> --}}
	  </div>
	</div>
</div>
<!-- View product code end here -->

@endsection
