<section class="content">
  <div class="container-fluid">
	<div class="row">
	  <div class="col-md-3">
		<!-- Profile Image -->
		<div class="card card-success card-outline">
		  <div class="card-body box-profile">
			<div class="text-center">
				<img class="img-fluid" src="{{ $data->avtar_url }}" alt="{{ $data->name }}">
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
						<td>Name</td>
						<td>{{ ($data->name) ? $data->name : "No Name" }}</td>
					</tr>
					<tr>
						<td>Sex</td>
						<td>
							@if($data->gender == 1)
								Male
							@else
								Female
							@endif
						</td>
					</tr>
                    <tr>
						<td>Company name</td>
						<td>{{ $company_name }}</td>
					</tr>
                    <tr>
						<td>Company URL</td>
						<td>{{ $company_url }}</td>
					</tr>
					<tr>
						<td>Email address</td>
						<td>{{ ($data->email) ? $data->email : "-" }}</td>
					</tr>
					<tr>
						<td>Phone</td>
						<td>{{ $phone }}</td>
					</tr>
					<tr>
						<td>Address</td>
						<td>{{ $address }}</td>
					</tr>
					<tr>
						<td>Unit/Apt #</td>
						<td>{{ $unit_apt }}</td>
					</tr>
					<tr>
						<td>City</td>
						<td>{{ $city }}</td>
					</tr>
                    <tr>
						<td>State</td>
						<td>{{ $state }}</td>
					</tr>
					<tr>
						<td>ZIP or Postal code</td>
						<td>{{ $zipcode }}</td>
					</tr>
                    <tr>
						<td>Country</td>
						<td>{{ $country }}</td>
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
