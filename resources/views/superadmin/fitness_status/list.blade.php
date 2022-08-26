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
        <h5 class="m-b-10">{{ __('Fitness Status') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item">Fitness status listing</li>
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
                    <h5 class="mb-0">Fitness Status Listing</h5>
                    <a href={{ url('superadmin/fitness-status/add') }}><button type="button" class="btn btn-info"><i class="fa fa-plus mr-2"></i>Add New</button></a>
                </div>


                <div class="sp_table">
                    <div class="table-responsive">
                        <table id="tblFitnessStatus" class="table table-bordered">
                            <thead>
		                        <tr>
		                            <th>Fitness status</th>
		                            <th>Created date</th>
                                    <th class="text-center">Status</th>
		                            <th class="no-sort text-center" width="150px">Action</th>
		                        </tr>
		                    </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete interest code start here -->
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
                    <p class="modal-text">Are you sure you want to delete this fitness status?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="closeBtn" class="btn btn-success" data-dismiss="modal" title="Cancel">Cancel</button>
                    <button type="submit" name="yesBtn" id="yesBtn" class="block-page btn btn-danger" data-dismiss="modal" title="Yes, Delete">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Delete interest code end here -->

<!-- View interest code start here -->
<div class="modal fade viewDetails" id="viewDetails">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Fitness status details</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content full_details">
			</div>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-danger" data-dismiss="modal" title="Close">Close</button>
		</div>
	  </div>
	</div>
</div>
<!-- View interest code end here -->
@endsection
