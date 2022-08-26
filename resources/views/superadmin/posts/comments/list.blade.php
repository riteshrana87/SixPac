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
        <h5 class="m-b-10">Comments</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/posts') }}"> Posts  </a></li>
            <li class="breadcrumb-item">Comment listing</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

@if ($errors->any())
    <div class="alert alert-icon-left alert-light-danger mb-4 alertmsg" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
        <i data-feather="alert-circle"></i> <strong>Error!</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session()->get('success'))
    <div class="alert alert-icon-left alert-light-success mb-4 alertmsg" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
        <i data-feather="check"></i> <strong>Success!</strong> {{ session()->get('success') }}
    </div>
@endif

@if(session()->get('error'))
    <div class="alert alert-icon-left alert-light-danger mb-4 alertmsg" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
        <i data-feather="alert-circle"></i> <strong>Error!</strong>{{ session()->get('error') }}
    </div>
@endif

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<h5 class="mb-0">Comment Listing</h5>
						</div>
						<div class="col-md-8 text-right">
							<a href="{{ url('superadmin/posts') }}"><button type="button" class="btnImport btn btn-secondary mr-4"><i class="fa fa-angle-double-left mr-2"></i>Back to Posts</button></a>
							<button type="button" class="btn btn-info" id="btnAddComment" data-post_id="{{ $postId }}" data-url="{{ url('superadmin/posts/comments/add') }}"><i class="fa fa-plus mr-2"></i>Add Comment</button>
						</div>
					</div>
                </div>

				<input type="hidden" name="postId" id="postId" value="{{ $postId }}">

                <div class="sp_table">
                    <div class="table-responsive">
                        <table id="tblPostComment" class="table table-bordered">
                            <thead>
		                        <tr>
		                            <th>Id</th>
		                            <th class="no-sort">Comment</th>
                                    <th class="text-center">Up vote</th>
                                    <th class="text-center">Down vote</th>
                                    <th class="text-center">Flagged</th>
		                            <th>Comment by</th>
		                            <th>Created date</th>
                                    <th class="text-center">Status</th>
		                            <th class="no-sort text-center" width="150px">Action</th>
		                        </tr>
		                    </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- View post comment code start here -->
<div class="modal fade viewCommentDetails" id="viewCommentDetails">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Comment details</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content full_details">
			</div>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-danger" data-dismiss="modal" title="Close">Close</button>
		</div>
	  </div>
	</div>
</div>
<!-- View post comment code end here -->

<!-- View comment falgged code start here -->
<div class="modal fade viewFlaggedByUsers" id="viewFlaggedByUsers">
	<div class="modal-dialog modal-md">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Users</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content full_details">
			</div>
		</div>
	  </div>
	</div>
</div>
<!-- View comment falgged code end here -->

<!-- Add Comment form code start here -->
<div class="modal fade viewDetails" id="addCommentModal">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Add Comment</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content addComment">
			</div>
		</div>
	  </div>
	</div>
</div>
<!-- Add Comment form code end here -->

<!-- Comment reply form code start here -->
<div class="modal fade viewDetails" id="commentDetails">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Comment reply</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content commentInfo">
			</div>
		</div>
	  </div>
	</div>
</div>
<!-- Comment reply form code end here -->

<!-- View comment up votes code start here -->
<div class="modal fade viewUpVoteUsers" id="viewUpVoteUsers">
	<div class="modal-dialog modal-md">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Users</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content full_details">
			</div>
		</div>
	  </div>
	</div>
</div>
<!-- View comment up votes code end here -->

<!-- View comment down votes code start here -->
<div class="modal fade viewDownVoteUsers" id="viewDownVoteUsers">
	<div class="modal-dialog modal-md">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Users</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<div class="modal_content full_details">
			</div>
		</div>
	  </div>
	</div>
</div>
<!-- View comment down votes code end here -->

@endsection
