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
        <h5 class="m-b-10">{{ __('Orders') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item">Order listing</li>
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
                    <h5 class="mb-0">Order Listing</h5>
                    <!--<button type="button" class="btn btn-info"><i class="fa fa-plus mr-2"></i>Add New</button>-->
                </div>


                <div class="sp_table">
                    <div class="table-responsive">
                        <table id="tblOrders" class="table table-bordered">
                            <thead>
		                        <tr>
		                            <th>Order ID</th>
		                            <th>Customer name</th>
									<th>Order amount($)</th>
									<th>Created date</th>
                                    <th>Status</th>
		                            <th class="no-sort" width="150px">Action</th>
		                        </tr>
		                    </thead>
							<tr>
								<td>SP-1000001</td>
								<td>Rose Bush</td>
								<td>500</td>
								<td>11/02/2022 10:20:25</td>
								<td><label class="label label-success">Active</label></td>
								<td>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>SP-1000002</td>
								<td>Olive Yew</td>
								<td>150</td>
								<td>11/02/2022 10:20:25</td>
								<td><label class="label label-success">Active</label></td>
								<td>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>SP-1000003</td>
								<td>Fran G. Pani</td>
								<td>240</td>
								<td>11/02/2022 10:20:25</td>
								<td><label class="label label-success">Active</label></td>
								<td>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>SP-1000004</td>
								<td>Karen Onnabit</td>
								<td>500</td>
								<td>11/02/2022 10:20:25</td>
								<td><label class="label label-success">Active</label></td>
								<td>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>SP-1000005</td>
								<td>Peter Owt</td>
								<td>80</td>
								<td>11/02/2022 10:20:25</td>
								<td><label class="label label-success">Active</label></td>
								<td>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>SP-1000006</td>
								<td>Loco Lyzayta</td>
								<td>550</td>
								<td>11/02/2022 10:20:25</td>
								<td><label class="label label-success">Active</label></td>
								<td>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>
									<a class="viewRec ml-2 mr-2" href="" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>
								</td>
							</tr>
							<tr>
								<td>SP-1000007</td>
								<td>Anne T. Dote</td>
								<td>810</td>
								<td>11/02/2022 10:20:25</td>
								<td><label class="label label-success">Active</label></td>
								<td>
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
