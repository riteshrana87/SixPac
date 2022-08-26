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
						<td width="200px">Post content</td>
						<td>
                            {{ $data->postWithTrashed->post_content }}
                        </td>
					</tr>
                    <tr>
						<td>Comment</td>
						<td>
                            {{ $data['comment'] }}
                        </td>
					</tr>
                    <tr>
						<td>Up vote</td>
						<td>
                            @if($data['comment_like_count'] == 0)
							<span class="badge counter-bg disabled">0</span>
                            @else
                                <span class="badge counter-bg">{{ $data['comment_like_count'] }}</span>
                            @endif
						</td>
					</tr>

					<tr>
						<td>Down vote</td>
						<td>
                            @if($data['comment_down_vote_count'] == 0)
							<span class="badge counter-bg disabled">0</span>
                            @else
                                <span class="badge counter-bg">{{ $data['comment_down_vote_count'] }}</span>
                            @endif
						</td>
					</tr>
                    <tr>
						<td>Flagged</td>
						<td>
                            @if($data['flagged_comment_count'] == 0)
							<span class="badge flag-counter-bg disabled">0</span>
                            @else
                                <span class="badge flag-counter-bg">{{ $data['flagged_comment_count'] }}</span>
                            @endif
						</td>
					</tr>
					<tr>
						<td>Comment by</td>
						<td>{{ $data->user->user_name }}</td>
					</tr>
					<tr>
						<td>Created at</td>
						<td>{{ $data->created_at }}</td>
					</tr>
					{{-- <tr>
						<td>Status</td>
						<td>
						@if($data['status'] == 1)
							<span data-placement="bottom" data-toggle="tooltip" title="Active" class="badge bg-success">Active</span>
						@else
							<span data-placement="bottom" data-toggle="tooltip" title="Deactive" class="badge bg-danger">Deactive</span>
						@endif
						</td>
					</tr> --}}
				</table>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</section>
