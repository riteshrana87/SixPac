<section class="content">
  <div class="container-fluid">
	<div class="row">

	  <div class="col-md-12">
		<div class="card card-warning card-outline">

			<div class="card-header p-2">
				<ul class="nav nav-pills">
					<li class="nav-item"><a class="nav-link active" href="#tab1" data-toggle="tab">Product Details</a></li>
					<li class="nav-item"><a class="nav-link" href="#tab2" data-toggle="tab">Product Media</a></li>
				</ul>
			</div>

		  <div class="card-body">
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
				<table id="viewDetails" class="table table-striped table-bordered" style="width:100%">
					<tr>
						<td width="200px">Product title</td>
						<td>{{ $data->product_title }}</td>
					</tr>
					<tr>
						<td>Category name</td>
						<td>{{ $data->productCategory->category_name }}</td>
					</tr>
					<tr>
						<td>Product description</td>
						<td>{{ $data->product_description }}</td>
					</tr>
					<tr>
						<td>SKU</td>
						<td>{{ $data->sku }}</td>
					</tr>
					<tr>
						<td>Quantity</td>
						<td>{{ $data->quantity }}</td>
					</tr>
					<tr>
						<td>Cost price($)</td>
						<td>{{ $data->cost_price }}</td>
					</tr>
					<tr>
						<td>Sell price($)</td>
						<td>{{ $data->sell_price }}</td>
					</tr>
					<tr>
						<td>Created at</td>
						<td>{{ $data->created_at }}</td>
					</tr>
					<tr>
						<td>Created by</td>
						<td>{{ (($data->usersData->role==3) ? $data->usersData->business->company_name : $data->usersData->user_name) ?? '-' }}</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>
						@if($data->status == 1)
							<span data-placement="bottom" data-toggle="tooltip" title="Active" class="badge bg-success">Active</span>
						@else
							<span data-placement="bottom" data-toggle="tooltip" title="Deactive" class="badge bg-danger">Deactive</span>
						@endif
						</td>
					</tr>
				</table>
			  </div>

			  <div class="tab-pane" id="tab2">
				<div class="row preview-image">
					@if(count($media) > 0)
						@foreach($media as $proMedia)
							<div class="col-md-4">
                                <a data-fancybox="gallery" href="{{ $proMedia->file_url }}"><img src="{{ $proMedia->thumb_url }}" width="200px" height="200px"></a>
                            </div>
						@endforeach
					@else
						<div class="col-md-12 text-center">No any product media found.</div>
					@endif
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</section>
