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
        <h5 class="m-b-10">{{ __('Dashboard') }}</h5>
        <p class="text-muted m-b-10">Welcome to SIXPAC portal</p>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item">Dashboard</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">

        {{-- <div class="col-md-6 col-xl-3">
        <div class="card bg-c-blue order-card">
        <div class="card-body">
        <h6 class="text-white">Orders Received</h6>
        <h2 class="text-end text-white"><i class="ti-shopping-cart float-start"></i><span>486</span></h2>
        <p class="m-b-0">Completed Orders<span class="float-end">351</span></p>
        </div>
        </div>
        </div>

        <div class="col-md-6 col-xl-3">
        <div class="card bg-c-green order-card">
        <div class="card-body">
        <h6 class="text-white">Total Sales</h6>
        <h2 class="text-end text-white"><i class="ti-package float-start"></i><span>1641</span>
        </h2>
        <p class="m-b-0">This Month<span class="float-end">213</span></p>
        </div>
        </div>
        </div>

        <div class="col-md-6 col-xl-3">
        <div class="card bg-c-yellow order-card">
        <div class="card-body">
        <h6 class="text-white">Revenue</h6>
        <h2 class="text-end text-white"><i class="ti-reload float-start"></i><span>$42,562</span></h2>
        <p class="m-b-0">This Month<span class="float-end">$5,032</span></p>
        </div>
        </div>
        </div>

        <div class="col-md-6 col-xl-3">
        <div class="card bg-c-red order-card">
        <div class="card-body">
        <h6 class="text-white">Total Profit</h6>
        <h2 class="text-end text-white"><i class="ti-medall float-start"></i><span>$9,562</span></h2>
        <p class="m-b-0">This Month<span class="float-end">$542</span></p>
        </div>
        </div>
        </div> --}}


        <div class="col-sm-12">
            <div class="card page-content">
                <div class="card-block">
                    <div class="row">
                        <div class="col-md-4 text-left">
                            <h5 class="m-b-10">Year wise registred user</h5>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex flex-wrap justify-content-end">
                                <label class="pt-2 pr-2">Select Year:</label>
                                <select name="reg_year" id="reg_year" class="form-control" style="width:100px;">
                                    @for ($regYr=date('Y'); $regYr>=date('Y')-4; $regYr--)
                                        <option value={{ $regYr }}>{{ $regYr }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- <div class="card-body d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">Registred User</h5>
                            <select name="reg_year" id="reg_year" class="form-control" style="width:100px;">
                                <option value="2022">2022</option>
                                <option value="2021">2021</option>
                                <option value="2020">2020</option>
                            </select>
                        </div> --}}
                    </div>
                    <div class="dashboard mt-4" id="userRegisterGraph"></div>

                </div>
            </div>
        </div>


    </div>
</div>
@endsection
