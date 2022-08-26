<section class="content">
  <div class="container-fluid">
	<div class="row">

	  <div class="col-md-12">
		<div class="card card-warning card-outline">
		  <div class="card-body">
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
				<form class="spform" name="replyForm" id="replyForm" action="{{ url('business/posts/comment/commentReply') }}" method="post">
					{{ csrf_field() }}

					<input type="text" name="post_id" id="post_id" value="{{ $data->posts->id }}">
					<input type="text" name="comment_id" id="comment_id" value="{{ $data->id }}">
					<input type="text" name="comment_by" id="comment_by" value="{{ $data->usersInfo->id }}">

					<table id="viewDetails" class="table table-striped table-bordered" style="width:100%">
						<tr>
							<td width="200px">Post title</td>
							<td>{{ $data->posts->post_title }}</td>
						</tr>
						<tr>
							<td>Comment</td>
							<td>{{ $data->comment }}</td>
						</tr>
						<tr>
							<td>Comment by</td>
							<td><span data-placement="bottom" data-toggle="tooltip" title="Active" class="badge bg-info">{{ $data->usersInfo->name }}</span></td>
						</tr>
						<tr>
							<td>Comment date</td>
							<td>{{ $data->created_at }} </td>
						</tr>
						<tr>
							<td>Reply</td>
							<td><textarea class="form-control" name="reply_comment" id="reply_comment"></textarea></td>
						</tr>
						<tr>
							<td></td>
							<td>
                                <button type="button" class="btn-hover color-9 m-b-20 btn-save" name="submitReplyBtn" id="submitReplyBtn">{{ __('Save') }}</button>
                            </td>
						</tr>
					</table>
				</form>
			  </div>
			</div>
		  </div>
		</div>
	  </div>

	</div>
  </div>
</section>
