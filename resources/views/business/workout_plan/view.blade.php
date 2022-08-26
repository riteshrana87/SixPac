<section class="content">
  <div class="container-fluid">
	<div class="row">
		<div class="col-md-3">
		<!-- Profile Image -->
		<div class="card card-success card-outline">
		  <div class="card-body box-profile">
			<div class="text-center">
				<img src="{{ $row->poster_image}}" alt="{{ $row->name }}" class="img-radius img-fluid wid-100" width="100px">
			</div>
		  </div>
		</div>
	  </div>
	  <div class="col-md-9">
		<div class="card card-warning card-outline">

		  <div class="card-body">
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
				<table id="viewDetails" class="table table-striped table-bordered" style="width:100%">
					<tr>
						<td width="200px">Plan title</td>
						<td>{{ $row->name ?? '-' }}</td>
					</tr>
					<tr>
						<td>Duration</td>
						<td>{{ $row->getPlanDay->name ?? '-' }}</td>
					</tr>
					<tr>
						<td>Created by</td>
						<td>
						@if($row->user) 
							{{ (($row->user->role==3) ? ($row->user->business->company_name ?? '-') : $row->user->user_name) ?? '-' }}
						@else
						 - 
						@endif
					</td>
					</tr>			
					<tr>
						<td>Created at</td>
						<td>{{ $row->created_at }}</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>
						@if($row->status == 1)
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
