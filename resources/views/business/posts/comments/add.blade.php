<section class="content">
  <div class="container-fluid">
	<div class="row">

	  <div class="col-md-12">
		<div class="card card-warning card-outline">
		  <div class="card-body">
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
                <span>All fields (<span class="req_star">*</span>) are required.</span>
				<form name="addCommentForm" class="spform" id="addCommentForm" action="{{ url('business/posts/comment/saveComment') }}" method="post">
					{{ csrf_field() }}

					<input type="hidden" name="post_id" id="post_id" value="{{ $postId }}">

					<table id="viewDetails" class="table table-striped table-bordered" style="width:100%">
						<tr>
							<td width="200px"><label>Comment: <span class="req_star">*</span></label></td>
							<td>
                                <textarea class="form-control inputor characterlimit" max-character="500" name="comment" id="comment" maxlength="500" rows="6" style="z-index: 999999;"></textarea>
                                <span class="pull-right label label-default count_message_field" id="cm_comment">0 / 500</span>
                            </td>
						</tr>
						<tr>
							<td></td>
							<td>
                                <button type="button" class="btn-hover color-9 m-b-20 btn-save" name="submitCommentBtn" id="submitCommentBtn">{{ __('Save') }}</button>
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
