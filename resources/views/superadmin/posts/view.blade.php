<section class="content">
  <div class="container-fluid">
	<div class="row">

	  <div class="col-md-12">
		<div class="card card-warning card-outline">
		 <div class="card-header p-2">
			<ul class="nav nav-pills">
			  <li class="nav-item"><a class="nav-link active" href="#tab1" data-toggle="tab">Post Details</a></li>
			  <li class="nav-item"><a class="nav-link" href="#tab2" data-toggle="tab">Post Media</a></li>
			</ul>
		  </div>
		  <div class="card-body">
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
				<table id="viewDetails" class="table table-striped table-bordered" style="width:100%">
                    <tr>
						<td width="200px">Post title</td>
						<td>
                            @if(empty($data['post_title']))
                            -
                            @else
                            {{ $data['post_title'] }}
                            @endif
                        </td>
					</tr>
					<tr>
						<td width="200px">Post content</td>
						<td>{{ $data['post_content'] }}</td>
					</tr>
					<tr>
						<td>Comments</td>
						<td>
						@if($data['comments_count'] == 0)
							<span class="badge counter-bg disabled">0</span>
						@else
							<a href="{{ url('superadmin/posts/comments/'.$data['id']) }}"><span class="badge counter-bg">{{ $data['comments_count'] }} </span></a>
						@endif
						</td>
					</tr>
					<tr>
						<td>Likes</td>
						<td>
                            @if($data['comments_count'] == 0)
							<span class="badge counter-bg disabled">0</span>
                            @else
                                <span class="badge counter-bg">{{ $data['likes_count'] }}</span>
                            @endif
						</td>
					</tr>
					<tr>
						<td>Flagged</td>
						<td>
                            @if($data['flagged_post_count'] == 0)
							<span class="badge flag-counter-bg disabled">0</span>
                            @else
                                <span class="badge flag-counter-bg">{{ $data['flagged_post_count'] }}</span>
                            @endif
						</td>
					</tr>
                    <tr>
						<td>Notes</td>
						<td>
                            @if(empty($data['notes']))
                            -
                            @else
                            {{ $data['notes'] }}
                            @endif
                        </td>
					</tr>
					<tr>
						<td>Created by</td>
						<td>
							@if(empty($data->user))
								-
							@else
							 	{{ (($data->user->role==3) ? $data->user->business->company_name : $data->user->user_name) ?? '-' }}
							 @endif
						</td>
					</tr>
					<tr>
						<td>Created at</td>
						<td>{{ $data->created_at }}</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>
						@if($data['status'] == 1)
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
						@if($data->post_media_rec_count > 0)
							@foreach($data->media as $media)
								<div class="col-md-4">
                                    <a data-fancybox="gallery" href="{{ $media['file_url'] }}"><img src="{{ $media['thumb_url'] }}" width="200px" height="200px"></a>
                                </div>
							@endforeach
						@else
							<div class="col-md-12 text-center">No any post media found.</div>
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
