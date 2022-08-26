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
        <h5 class="m-b-10">{{ __('Advertisment') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item">Advertisment listing</li>
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
                    <h5 class="mb-0">Advertisment Listing</h5>
                    <button type="button" class="btn btn-info"><i class="fa fa-plus mr-2"></i>Add New</button>
                </div>


                <div class="sp_table">
                    <div class="table-responsive">
                        <table id="tblAdvertisment" class="table table-bordered">
                            <thead>
		                        <tr>
		                            <th>Title</th>
		                            <th>Category</th>
		                            <th>Created by</th>
		                            <th>Created date</th>
                                    <th class="text-center">Status</th>
		                            <th class="no-sort text-center" width="150px">Action</th>
		                        </tr>
		                    </thead>
							<tr>
								<td>Elementum sem Curabitur odio urna.</td>
								<td>Suscipit dolor</td>
								<td>Greg Arias</td>
								<td>11/02/2022 10:20:25</td>
								<td class="text-center"><label class="label label-success">Active</label></td>
								<td class="no-sort text-center">
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>Suscipit dolor eget, pharetra augue.</td>
								<td>Suscipit dolor</td>
								<td>Lynn O’Leeum</td>
								<td>11/02/2022 10:20:25</td>
								<td class="text-center"><label class="label label-success">Active</label></td>
								<td class="no-sort text-center">
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>Commodo eget tristique a, efficitur a neque.</td>
								<td>Curabitur</td>
								<td>Don Messwidme</td>
								<td>11/02/2022 10:20:25</td>
								<td class="text-center"><label class="label label-success">Active</label></td>
								<td class="no-sort text-center">
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>Eros vel ultrices lacinia, sapien felis.</td>
								<td>Pulvinar elementum</td>
								<td>Teri Dactyl</td>
								<td>11/02/2022 10:20:25</td>
								<td class="text-center"><label class="label label-success">Active</label></td>
								<td class="no-sort text-center">
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>Efficitur mauris imperdiet ut.</td>
								<td>Molestie</td>
								<td>Jack Aranda</td>
								<td>11/02/2022 10:20:25</td>
								<td class="text-center"><label class="label label-success">Active</label></td>
								<td class="no-sort text-center">
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>Delis molestie imperdiet.</td>
								<td>Suspendisse</td>
								<td>Bridget Theriveaquai</td>
								<td>11/02/2022 10:20:25</td>
								<td class="text-center"><label class="label label-success">Active</label></td>
								<td class="no-sort text-center">
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>Enim quis ante pulvinar elementum sit amet vel velit.</td>
								<td>Phasellus quis</td>
								<td>Peter Owt</td>
								<td>11/02/2022 10:20:25</td>
								<td class="text-center"><label class="label label-success">Active</label></td>
								<td class="no-sort text-center">
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
