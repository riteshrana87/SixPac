<section class="content">
  <div class="container-fluid">
	<div class="row">
	  <div class="col-md-12">
		<div class="card card-warning card-outline">
		  <div class="card-body">
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
				<table id="viewDetails" class="table table-striped table-bordered" style="width:100%">
					<tr>
						<td width="200px">Service</td>
						<td>{{ $data->service }}</td>
					</tr>
					<tr>
						<td width="200px">Getfit category</td>
						<td>{{ $data->getFitData->name ?? 'null' }}</td>
					</tr>
					<tr>
						<td>Created by</td>
						<td>{{ (($data->usersData->role==3) ? $data->usersData->business->company_name : $data->usersData->user_name) ?? '-' }}</td>
					</tr>
					<tr>
						<td>Created at</td>
						<td>{{ $data->created_at }}</td>
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
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</section>
