<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card card-warning card-outline">
					<div class="card-header p-2">
						<ul class="nav nav-pills">
							<li class="nav-item"><a class="nav-link active" href="#tab1" data-toggle="tab">Workout Details</a></li>
							<li class="nav-item"><a class="nav-link" href="#tab2" data-toggle="tab">Poster Image</a></li>
							<li class="nav-item"><a class="nav-link" href="#tab3" data-toggle="tab">Workout Video</a></li>
						</ul>
					</div>			
					<div class="card-body">
						<div class="tab-content">
							<div class="tab-pane active" id="tab1">
								<table id="viewDetails" class="table table-striped table-bordered" style="width:100%">
									<tr>
										<td width="200px">Workout title</td>
										<td>{{ $row->name ?? '-' }}</td>
									</tr>
									<tr>
										<td width="200px">Overview</td>
										<td>{{ $row->overview ?? '-' }}</td>
									</tr>
									<tr>
										<td>Workout type</td>
										<td>{{ $row->workoutType->name ?? '-' }}</td>
									</tr>
									<tr>
										<td width="200px">Duration</td>
										<td>{{ $row->duration->duration ?? '-' }}</td>
									</tr>
									<tr>
										<td width="200px">Exercise</td>
										<td>{!! $row->exercises ?? '-' !!}</td>
									</tr>
									<tr>
										<td width="200px">Equipment</td>
										<td>{!! $row->equipments ?? '-' !!}</td>
									</tr>
									<tr>
										<td width="200px">Body Parts</td>
										<td>{!! $row->bodyParts ?? '-' !!}</td>
									</tr>
									<tr>
										<td width="200px">Fitness Level</td>
										<td>{!! $row->fitnessLevels ?? '-' !!}</td>
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
							<div class="tab-pane" id="tab2">
								<div class="row ex-preview-image">
									<div class="col-md-12">
										<img src="{{ $row->poster_image }}" alt="{{ $row->name }}" class="img-radius img-fluid wid-100" width="100%">
									</div>
								</div>
							</div>
							<div class="tab-pane" id="tab3">
								<div class="row ex-preview-image">
									<div class="col-md-12">
										<img src="{{ $row->video_thumb }}" alt="{{ $row->name }}" class="img-radius img-fluid wid-100" width="100%">
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
