@extends('layouts.backend')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
   <link rel="stylesheet" href="{{ asset('backend/assets/css/cropper/cropper.css') }}"/>
   <script src="{{ asset('backend/assets/js/cropper/cropper.js') }}"></script>
<!-- Page-header start -->
<div class="page-header card">
    <div class="card-block">
        <h5 class="m-b-10">{{ __('Posts') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('business/posts') }}">Posts</a></li>
            <li class="breadcrumb-item">Edit post</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card page-content">

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Edit Post</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
                </div>


                <div class="card-block table-border-style">
                    @if ($errors->any())
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-danger mb-4 alertmsg" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
									<i data-feather="alert-circle"></i> <strong>Error!</strong>
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					@endif

					@if(session()->get('success'))
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-success mb-4 alertmsg" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
									<i data-feather="check"></i> <strong>Success!</strong> {{ session()->get('success') }}
								</div>
							</div>
						</div>
					@endif

					@if(session()->get('error'))
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-danger mb-4 alertmsg" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="close"></i></button>
									<i data-feather="alert-circle"></i> <strong>Error!</strong>{{ session()->get('error') }}
								</div>
							</div>
						</div>
					@endif

					<!-- Form code start here -->
					<form class="form-horizontal spform" name="frmEditPosts" id="frmEditPosts" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('business.update-post') }}">
					@csrf
					@method('PUT')
						<input type="hidden" name="post_id" id="post_id" value="{{ $data->id }}">
						<input type="hidden" name="old_tag_users" id="old_tag_users" value="{{ $old_tag_users}}">
						<!--
						<input type="hidden" name="old_file" id="old_file" value="{{ $data->old_file }}">
						<input type="hidden" name="temp_file" id="temp_file" value="{{ $data->old_file }}">
						-->
						<div class="row">

							<div class="col-md-6">

                                <div class="field mb-4">
                                    <label>Post title:</label>
                                    <input class="form-control characterlimit req" max-character="200" name="post_title" id="post_title" placeholder="Enter post title" maxlength="200" autofocus value="{{ $data->post_title }}">
                                    <span class="pull-right label label-default count_message_field" id="cm_post_title">{{ strlen($data->post_title) }} / 200</span>
								</div>

								<div class="field mb-4">
                                    <label>Post content: <span class="req_star">*</span></label>
                                    <textarea class="form-control characterlimit req" max-character="2200" name="post_content" id="post_content" placeholder="Enter post content" maxlength="2200" rows="12">{{ $data->post_content }}</textarea>
                                    <span class="pull-right label label-default count_message_field" id="cm_post_content">{{ strlen($data->post_content) }} / 2200</span>
								</div>

                                <div class="field mb-4">
                                    <label>Notes:</label>
                                    <textarea class="form-control characterlimit req" max-character="300" name="notes" id="notes" placeholder="Enter post notes" maxlength="300" rows="8">{{ $data->notes }}</textarea>
                                    <span class="pull-right label label-default count_message_field" id="cm_notes">{{ strlen($data->notes) }} / 300</span>
								</div>

								{{-- <div class="field mb-4">
                                    <label>Post type: </label><br>
									@php
									if($data->is_public == 1){
										$publicFlag = 'checked';
									}
									else
									{
										$publicFlag = '';
									}
									@endphp
									<input type="hidden" name="is_public" id="is_public" value="{{ $data->is_public }}">
								    <input type="checkbox" name="setPublic" data-size="small" data-on-text="Public" data-off-text="Private" data-on-color="blue-1" data-off-color="orangeYellow" data-bootstrap-switch class="active-status" id="change_is_public" {{ $publicFlag }}>
								</div> --}}
								<div class="field mb-4">
                                    <label>Status: </label><br>
									@php
									if($data->status == 1){
										$statusFlag = 'checked';
									}
									else
									{
										$statusFlag = '';
									}
									@endphp
									<input type="hidden" name="status" id="status" value="{{ $data->status }}">
								    <input type="checkbox" name="setStatus" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" id="change_status" {{ $statusFlag }}>
								</div>
							</div>
							<div class="col-md-6">
                                <label>Post media: <span class="req_star">*</span></label>
								<div class="row pt-2">
									<div class="col-md-6 text-left">
										<span class="text-danger"><strong>Important Notes:</strong></span>
										<ul>
											<li>1. Maximum 5 files allow for post gallery.</li>
											<li>2. Image file should be .jpg, .jpeg or .png extenstion.</li>
											<li>3. Image file size should be less than 300KB.</li>
										</ul>
									</div>

									<div class="col-md-6 text-left">
										<span class="text-danger"><strong>&nbsp;</strong></span>
										<ul>
											<li>4. Only 1 video file allowed.</li>
											<li>5. Video file should be .mp4 extenstion.</li>
											<li>6. Video file size should be less than 1MB.</li>
											<li>7. Minimum dimension of cropped image should be (250px X 250px).</li>
										</ul>
									</div>
								</div>
								<!-- File upload code start here -->								

								<div id="proGallery">
									<div class="store">
									<?php
									if($totalMedia > 0){
										for($m = 0; $m < $totalMedia; $m++){
											$galleryId	= $postGallery['gallery_id'][$m];
											$mediaOrder = $postGallery['media_order'][$m];
											$imgUrl	= $postGallery['original_image'][$m] ?? null;
									?>
										<input type="hidden" name="old_media[]" id="old_inputMedia_<?php echo $galleryId; ?>" value="<?php echo $galleryId; ?>">
										@if(!empty($imgUrl))
										<input type="hidden" name="existingMedia[]" id="existingMedia_<?php echo $galleryId; ?>" class="existingMedia" value="<?=$imgUrl?>" data-order="<?=$mediaOrder?>">
										@endif
									<?php
										}
									}
									?>
									</div>
									<ul class="preview sortable">
									<?php
									$i=1;
									if($totalMedia > 0){
										for($g = 0; $g < $totalMedia; $g++){
											$galleryId	= $postGallery['gallery_id'][$g];
											$fileType	= strtolower($postGallery['file_type'][$g]);
											$fileUrl	= $postGallery['file_url'][$g];
                                            $thumbUrl	= $postGallery['thumb_url'][$g];
                                            $mediaOrder = $postGallery['media_order'][$g];
                                            $imgUrl	= $postGallery['original_image'][$g] ?? null;
									?>
									<li class="filebox old-gallery" id="old_gallery_id_<?php echo $galleryId; ?>" data-mid="<?=$mediaOrder;?>" data-id="<?=$galleryId;?>" data-index="<?=$i?>" style="cursor: move;">
										<?php
                                        $videoClassName = '';
                                        if(in_array($fileType, Config::get('constant.VIDEO_EXTENSION'))){
                                            $videoClassName = 'video';
                                        }
                                        ?>
                                        <img class="old_gallery_thumb {{ $videoClassName }}" src="<?php echo $thumbUrl; ?>" id="old_gallery_thumb_<?=$mediaOrder?>" data-id="<?=$galleryId?>">
										<a href="javascript:void(0)" class="old_deleteMedia" data-id="<?php echo $galleryId; ?>"><i class="fa fa-trash"></i></a>
										@if(!empty($imgUrl))
										<a href="javascript:void(0)" id="crop_media_<?=$mediaOrder?>" class='cropMedia' data-id="<?=$mediaOrder?>"><i class='fa fa-scissors'></i></a>
										@endif
									</li>
									<?php
										$i++;
										}
									}
									?>
									</ul>
									<!-- <input id="gallery_files" type="file" name="gfiles[]" multiple class="hide" accept=".jpeg, .jpg, .png, .mp4, .mpg, .mpeg, .mkv, .webm, .avi, .wmv, .mov"> -->
									<input id="gallery_files" type="file" name="gfiles[]" multiple class="hide" accept=".jpeg, .jpg, .png, .mp4, .mov, .mkv, .webm">
								</div>
								<div class="row">
									<?php
									$hideButtonClass = '';
									if($totalMedia >= 5){ $hideButtonClass = 'hide'; }
									?>
									<div class="col-md-12 text-center mt-4">
										<button type="button" name="btnUploadGallery" id="btnUploadGallery" class="btn btn-info mb-0 {{ $hideButtonClass }}"><i class="fa fa-upload mr-2"></i> Select Media files</button><span id="loading" class="ml-2 hide"><img src="{{ asset('backend/assets/images/loading_circle.gif') }}"></span>
										<br><input type="hidden" name="totalFiles" id="totalFiles" value="{{ $totalMedia }}">
									</div>
								</div>

								@include('common/cropImage')

								<!-- File upload code end here -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 text-left">
								<a href={{ url('business/posts') }}>
                                    <button type="button" class="btn-hover color-11 m-b-20 btn-cancel" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button>
                                </a>
							</div>
							<div class="col-md-6 text-right">
								<button type="submit" class="btn-hover color-9 m-b-20 btn-save" name="btnSubmit" id="btnSubmit">{{ __('Save') }}</button>
							</div>
						</div>
					</form>
					<!-- Form code end here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
