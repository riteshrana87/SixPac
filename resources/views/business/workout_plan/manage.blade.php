@extends('layouts.backend')

@section('content')

@if (session('status'))
<div class="alert alert-success" role="alert">
    {{ session('status') }}
</div>
@endif

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap-switch/custom/css/bootstrap-switch.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/exercise.css') }}">
<link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
@endpush

<!-- Page-header start -->
<div class="page-header card">
    <div class="card-block">
        <h5 class="m-b-10">{{ __('Add Workout Plan') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/get-fit/exercises') }}">Workout Plan</a></li>
            <li class="breadcrumb-item">Add workout plan</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card page-content">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Add Workout Plan</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
       <form class="form-horizontal spform" name="frmAddEditWorkoutPlan" id="frmAddEditWorkoutPlan" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ empty($data) ? route('business.create-workout-plan') : route('business.edit-workout',$data->id) }}">
           @csrf
           <input type="hidden" name="created_by" id="created_by" value="{{Auth::user()->id}}">
           <input type="hidden" name="id" id="id" value="{{ $data->id ?? null }}">
           <input type="hidden" name="media" id="exerciseVideo" value="">
           <input type="hidden" class="video" name="video_thumb" id="video_thumb" value="{{$data->video_thumb ?? ''}}">

           <div class="store"></div>

        <div class="row pb-4">
            <div class="col-md-12">
                <div class="field">
                    <label>Plan title: <span class="req_star">*</span></label>
                    <input class="form-control req" type="text" name="plan_title" id="plan_title" placeholder="Enter plan title" maxlength="150" value="{{ $data->name ?? old('plan_title') }}">
                </div>
            </div>
        </div>



        <div class="ExerciseCategory pb-4">
            <div class="row">
                <div class="col-md-12 section-title">
                    <label id="err_plan_day">Select duration: <span class="req_star">*</span></label>
                </div>
                @if (!empty($workoutPlanData['planDurations']))
                <ul>
                    @foreach($workoutPlanData['planDurations'] as $key => $duration)
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="radio" id="plan_day_<?=$key?>" name="plan_day" data-name="plan_day" value="{{$key}}" @if(!empty($data->plan_day) && $key==$data->plan_day) checked @endif />
                                <div>
                                    <p class="span_duration"><span>{{preg_replace('/[^0-9]/', '', $duration)}}</span>
                                        <br>
                                    {{preg_replace('/[^a-zA-Z]/', '', $duration)}}</p>
                                </div>
                            </article>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        <div class="ExerciseCategory pb-4">
            <div class="row">
                <div class="col-md-12 section-title">
                    <label id="err_goal_id">Select goal: <span class="req_star">*</span></label>
                </div>
                @if (!empty($workoutPlanData['goals']))

                <ul>
                    @foreach($workoutPlanData['goals'] as $key => $row)
                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.PLAN_GOAL_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.PLAN_GOAL_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                    ?>
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="radio" id="goal_<?=$row['id']?>" name="goal_id" data-name="goal_id" value="{{$row['id']}}" @if(!empty($data->goal_id) && $row['id']==$data->goal_id) checked @endif />
                                <div class="purple">
                                    <span class="icon-wrap"><img src="<?=$iconUrl?>" class="img-fluid"></span>
                                </div>
                            </article>
                            <span class="box_name">{{$row['name']}}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>


        <div class="ExerciseCategory pb-4">
            <div class="row">
                <div class="col-md-12 section-title">
                    <label id="err_goal_id">Select sport: <span class="req_star">*</span></label>
                </div>
                @if (!empty($workoutPlanData['planSports']))
                <ul>
                    @foreach($workoutPlanData['planSports'] as $key => $row)
                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.PLAN_SPORT_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.PLAN_SPORT_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                    ?>
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="radio" id="goal_<?=$row['id']?>" name="sport_id" data-name="sport_id" value="{{$row['id']}}" @if(!empty($data->sport_id) && $row['id']==$data->sport_id) checked @endif />
                                <div class="purple">
                                    <span class="icon-wrap"><img src="<?=$iconUrl?>" class="img-fluid"></span>
                                </div>
                            </article>
                            <span class="box_name">{{$row['name']}}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>





        <div class="row ExerciseCategory pb-4">
            <div class="col-md-9">
                <div class="field">
                    <label>Select poster image: <span class="req_star">*</span></label>
                    <div class="custom-file">
                        <input class="form-control req" type="file" name="poster_image" id="poster_image" placeholder="Select poster image" value="{{ old('poster_image') }}" accept=".jpeg, .jpg, .png">
                        <label class="custom-file-label" for="poster_image">Choose file</label>
                    </div>
                </div>
            </div>
             @php $fileUrl = (!empty($data->poster_image) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.EXERCISE_ORIGINAL_PHOTO_UPLOAD_PATH').$data->poster_image)) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.EXERCISE_ORIGINAL_PHOTO_UPLOAD_PATH').$data->poster_image) : ''; @endphp
            <div class="col-md-3">
                <div class="field post_image_preview" style="display:{{!empty($fileUrl) ? 'block' : 'none'}}">
                    <img src="{{$fileUrl}}" class="img-fluid">
                    <a href="javascript:void(0)" id="delete_image" class="deleteMedia"><i class="fa fa-trash"></i></a>
                     <input type="hidden" name="old_image" id="old_image" value="{{$fileUrl}}">
                </div>
            </div>
        </div>

        <div class="row ExerciseCategory pb-4">
            <div class="col-md-9">
                <div class="video_info">
                    <div class="field">
                        <label>Select exercise video: <span class="req_star">*</span></label>
                        <div class="custom-file">
                            <input class="form-control req" type="file" name="video_name" id="video_name" placeholder="Select exercise video" value="{{ old('video_name') }}" accept=".mp4, .mov, .mkv, .webm">
                            <label class="custom-file-label" for="video_name">Choose file</label>
                        </div>
                    </div>
                </div>
            </div>
            @php $thumbUrl = (!empty($data->video_thumb) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.EXERCISE_THUMB_PHOTO_UPLOAD_PATH').$data->video_thumb)) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.EXERCISE_THUMB_PHOTO_UPLOAD_PATH').$data->video_thumb) : ''; @endphp
            <div class="col-md-3">
                <div class="field video_preview" style="display:{{!empty($thumbUrl) ? 'block' : 'none'}}">
                    <img src="{{$thumbUrl}}" id="previewImg" style="width:100%;height:100%;">
                    <a href="javascript:void(0)" id="delete_video" class="deleteMedia"><i class="fa fa-trash"></i></a>
                    <input type="hidden" name="old_video" id="old_video" value="{{$thumbUrl}}">
                </div>
            </div>
        </div>


        <div class="row pb-4">
            <div class="col-md-9">
                <div class="field">
                    <label>Overview: <span class="req_star">*</span></label>
                    <textarea class="form-control req" name="overview" id="overview" placeholder="Enter exercise overview" maxlength="500" rows="8">{{ $data->overview ?? old('overview') }}</textarea>
                </div>
            </div>
        </div>

        <div class="row pb-4">
            <div class="col-md-9">
                <div class="field workout__listing">
                    <label>Select Workout: <span class="req_star">*</span></label>
                    <select class="selectpicker req select__dd" data-live-search="true" name="workout" id="workout_id">
                        <option value="">Select Workout</option>
                        <option value="1">Workout A</option>
                        <option value="2">Workout B</option>
                        <option value="3">Workout C</option>
                        <option value="4">Workout D</option>
                        <option value="5">Workout E</option>
                    </select>
                    <a href="javascript:void(0)" title="Add New Exercise"><img src="{{ asset('backend/assets/images/icons/plus_icon.png') }}" class="select_dd_plus"></a>
                </div>
            </div>
        </div>


        <div class="row pb-4">
            <div class="col-md-5">
                <div class="field mb-4">
                    <label>Status: </label><br>
                    <input type="hidden" name="status" id="status" value="{{ $data->status ?? '1' }}">
                    <input type="checkbox" name="setStatus" id="change_status" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" @if(!empty($data) && $data->status==1) checked @elseif(empty($data)) checked @endif>
                </div>
            </div>
            <!-- Suitable location box code end here -->
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ url('superadmin/get-fit/exercises') }}">
                        <button type="button" class="btn-hover color-11 m-b-20 btn-col-6-cancel" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button></a>
                        <button type="submit" class="btn-hover color-9 m-b-20 btn-col-6-save ml-2" name="btnSubmit" id="btnSubmit">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>


        </form>
    </div>
    <!-- Form code end here -->
</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="{{ asset('plugins/bootstrap-switch/custom/js/bootstrap-switch.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/validation/js/jquery.form.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/validation/js/jquery.validate.min.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="{{ asset('backend/assets/business/js/manage_exercise.js') }}" onload="add();"></script>
<script>$(function() {
  $('.selectpicker').selectpicker();
});
    </script>
@endpush
