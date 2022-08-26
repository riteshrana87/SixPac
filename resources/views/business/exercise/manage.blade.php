@php $page_title = !empty($data->id) ? 'Edit Exercise' : 'Add Exercise';
$type = !empty($data->id) ? 'Edit' : 'Add'; @endphp

@extends('layouts.backend')

@section('content')

@if (session('status'))
<div class="alert alert-success" role="alert">
    {{ session('status') }}
</div>
@endif

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap-switch/custom/css/bootstrap-switch.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/getfit.css') }}">
<style>
</style>
@endpush

<!-- Page-header start -->
<div class="page-header card">
    <div class="card-block">
        <h5 class="m-b-10">{{ __('Exercise') }}</h5>
        <ul class="breadcrumb-title b-t-default p-t-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"> <i class="fa fa-home"></i> </a></li>
            <li class="breadcrumb-item"><a href="{{ url('superadmin/get-fit/exercises') }}">Exercises</a></li>
            <li class="breadcrumb-item">{{ $type }} exercise</li>
        </ul>
    </div>
</div>
<!-- Page-header end -->
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card page-content">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ $type }} Exercise</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
<form class="form-horizontal spform" name="frmAddEditExercise" id="frmAddEditExercise" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ empty($data) ? route('business.add-exercise') : route('business.edit-exercise',$data->id) }}">
 @csrf
 <input type="hidden" name="created_by" id="created_by" value="{{Auth::user()->id}}">
 <input type="hidden" name="id" id="id" value="{{ $data->id ?? null }}">
 <input type="hidden" name="media" id="exerciseVideo" value="">
 <input type="hidden" class="video" name="video_thumb" id="video_thumb" value="{{$data->video_thumb ?? ''}}">

 <div class="store"></div>

        <div class="row pb-4">
            <div class="col-md-12">
                <div class="field">
                    <label>Exercise title: <span class="req_star">*</span></label>
                    <input class="form-control characterlimit req" type="text" name="name" id="exercise_title" placeholder="Enter exercise title" max-character="150" maxlength="150" value="{{ $data->name ?? old('exercise_title') }}">
					<span class="pull-right label label-default count_message_field" id="cm_exercise_title"> {{!empty($data->name) ? strlen($data->name) : 0 }} / 150 </span>
                </div>
            </div>
        </div>


        <div class="ExerciseCategory pb-4 interest-sec">
            <div class="row">
                <div class="col-md-12 section-title">
                    <label id="err_interests">Select interests: <span class="req_star">*</span></label>
                </div>
                @php $interestArr = []; @endphp
                @if(!empty($data->interests))
                @foreach($data->interests as $key => $interest)
                @php array_push($interestArr, $interest->id); @endphp
                @endforeach
                @endif
                @if (!empty($exerciseData['interests']))
                <ul>
                    @foreach($exerciseData['interests'] as $key => $interest)
                    <?php $iconUrl = (!empty($interest['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$interest['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$interest['icon_file']) : asset('backend/assets/images/no-icon.png');
                    ?>
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="checkbox" id="interest_<?=$interest['id']?>" name="interests[]" data-name="interests" value="{{$interest['id']}}" @if(!empty($interestArr) && in_array($interest['id'],$interestArr)) checked @endif />
                                <div class="blue">
                                    <span class="icon-wrap"><img src="<?=$iconUrl?>" class="img-fluid"></span>
                                </div>
                            </article>
                            <span class="box_name">{{$interest['interest_name']}}</span>
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
                    <label id="err_workout_type_id">Select workout type: <span class="req_star">*</span></label>
                </div>
                @if (!empty($exerciseData['workoutTypes']))
                <ul>
                    @foreach($exerciseData['workoutTypes'] as $key => $row)
                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                    ?>
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="radio" id="wo_<?=$row['id']?>" name="workout_type_id" data-name="workout_type_id" value="{{$row['id']}}" @if(!empty($data->workout_type_id) && $row['id']==$data->workout_type_id) checked @endif />
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
                    <label id="err_duration_id">Select duration: <span class="req_star">*</span></label>
                </div>
                @if (!empty($exerciseData['durations']))
                <ul>
                    @foreach($exerciseData['durations'] as $key => $duration)
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="radio" id="duration_<?=$key?>" name="duration_id" data-name="duration_id" value="{{$key}}" @if(!empty($data->duration_id) && $key==$data->duration_id) checked @endif />
                                <div>
                                    <p class="span_duration">
                                        <span>{{preg_replace('/[^0-9]/', '', $duration)}}</span><br>
                                        <span class="duration__time">{{preg_replace('/[^a-zA-Z]/', '', $duration)}}</span>
                                    </p>
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
                    <label id="err_equipment">Select equipment: <span class="req_star">*</span></label>
                </div>
                @php $equipmentArr = []; @endphp
                @if(!empty($data->equipments))
                @foreach($data->equipments as $key => $equipment)
                @php array_push($equipmentArr, $equipment->id); @endphp
                @endforeach
                @endif
                @if (!empty($exerciseData['equipments']))
                <ul>
                    @foreach($exerciseData['equipments'] as $key => $row)
                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.EQUIPMENT_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.EQUIPMENT_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                    ?>
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="checkbox" id="equipment_<?=$row['id']?>" name="equipment[]" data-name="equipment" value="{{$row['id']}}" @if(!empty($equipmentArr) && in_array($row['id'],$equipmentArr)) checked @endif />
                                <div>
                                    <span class="icon-wrap"><img src="{{$iconUrl}}" class="img-fluid"></span>
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
                    <label id="err_body_part">Select body part: <span class="req_star">*</span></label>
                </div>
                @php $bodyPartArr = []; @endphp
                @if(!empty($data->bodyParts))
                @foreach($data->bodyParts as $key => $bodyPart)
                @php array_push($bodyPartArr, $bodyPart->id); @endphp
                @endforeach
                @endif
                @if (!empty($exerciseData['bodyParts']))
                <ul>
                    @foreach($exerciseData['bodyParts'] as $key => $row)
                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.BODY_PART_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.BODY_PART_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                    ?>
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="checkbox" id="body_part_{{$row['id']}}" name="body_part[]" data-name="body_part" value="{{$row['id']}}" @if(!empty($bodyPartArr) && in_array($row['id'],$bodyPartArr)) checked @endif />
                                <div>
                                    <span class="icon-wrap"><img src="{{$iconUrl}}" class="img-fluid"></span>
                                </div>
                            </article>
                        </div>
                        <span class="box_name">{{$row['name']}}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        <div class="row ExerciseCategory_bottom pb-4">
            <!-- Fitness Level Box code start here -->
            <div class="col-md-12 p-0">
                <div class="ExerciseCategory">
                    <div class="col-md-12 section-title">
                        <label id="err_fitness_level">Select fitness level: <span class="req_star">*</span></label>
                    </div>
                    @php $fitnessLevelsArr = []; @endphp
                    @if(!empty($data->fitnessLevels))
                    @foreach($data->fitnessLevels as $key => $fitnessLevel)
                    @php array_push($fitnessLevelsArr, $fitnessLevel->id); @endphp
                    @endforeach
                    @endif
                    @if (!empty($exerciseData['fitnessLevels']))
                    <ul>
                        @foreach($exerciseData['fitnessLevels'] as $key => $row)
                        <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.FITNESS_LEVEL_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.FITNESS_LEVEL_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                        ?>
                        <li class="text-center">
                            <div class="box-middle">
                                <article>
                                    <input type="checkbox" id="fitness_level_{{$row['id']}}" name="fitness_level[]" data-name="fitness_level" value="{{$row['id']}}" @if(!empty($fitnessLevelsArr) && in_array($row['id'],$fitnessLevelsArr)) checked @endif />
                                    <div>
                                        <span class="icon-wrap"><img src="{{$iconUrl}}" class="img-fluid"></span>
                                    </div>
                                </article>
                            </div>
                            <span class="box_name">{{$row['name']}}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            <!-- Fitness Level Box code end here -->

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
                    <img src="{{$fileUrl}}" class="img-fluid" width="100%">
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
            <!-- Exercise gender box code start here -->
            <div class="col-md-4 p-0">
                <div class="ExerciseGender">
                    <div class="col-md-12 section-title">
                        <label id="err_gender">Exercise for: <span class="req_star">*</span></label>
                    </div>
                    @if (!empty($exerciseData['gender']))
                    <ul>
                        @foreach($exerciseData['gender'] as $key => $gender)
                        <li class="text-center">
                            <div class="box-middle">
                                <article class="gender">
                                    <input type="radio" id="gender_{{$key}}" name="gender" data-name="gender" value="{{$key}}" @if(!empty($data->gender) && $key==$data->gender) checked @endif />
                                    <div>
                                        <span class="icon-wrap"><img src="{{ asset($gender['icon']) }}" class="img-fluid"></span>
                                    </div>
                                </article>
                            </div>
                            <span class="box_name">{{$gender['gender']}}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            <!-- Exercise gender box code end here -->

            <!-- Suitable location box code start here -->
            <div class="col-md-5 p-0">
                <div class="ExerciseLocation">
                    <div class="col-md-12 section-title">
                        <label id="err_location">Exercise suitable location: <span class="req_star">*</span></label>
                    </div>
                    @if (!empty($exerciseData['location']))
                    <ul>
                        @foreach($exerciseData['location'] as $key => $location)
                        <li class="text-center">
                            <div class="box-middle">
                                <article class="location">
                                    <input type="radio" id="location_{{$key}}" name="location" data-name="location" value="{{$key}}" @if(!empty($data->location) && $key==$data->location) checked @endif />
                                    <div>
                                        <span class="icon-wrap"><img src="{{ asset($location['icon']) }}" class="img-fluid"></span>
                                    </div>
                                </article>
                            </div>
                            <span class="box_name">{{$location['location']}}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            <!-- Suitable location box code end here -->

        </div>

        <div class="ExerciseCategory pb-4">
            <div class="row">
                <div class="col-md-12 section-title">
                    <label id="err_age_group">Select age group: <span class="req_star">*</span></label>
                </div>
                @php $ageGroupArr = []; @endphp
                @if(!empty($data->ageGroups))
                @foreach($data->ageGroups as $key => $ageGroup)
                @php array_push($ageGroupArr, $ageGroup->id); @endphp
                @endforeach
                @endif
                @if (!empty($exerciseData['ageGroups']))
                <ul>
                    @foreach($exerciseData['ageGroups'] as $key => $ageGroup)
                    <li class="text-center">
                        <div class="box-middle">
                            <article>
                                <input type="checkbox" id="age_group_{{$key}}" name="age_group[]" data-name="age_group" value="{{$key}}" @if(!empty($ageGroupArr) && in_array($key,$ageGroupArr)) checked @endif />
                                <div>
                                    <span class="span_duration">{{$ageGroup}}</span>
                                </div>
                            </article>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        <div class="row pb-4">
            <div class="col-md-9">
                <div class="field mb-4">
                    <label>Status: </label><br>
                    <input type="hidden" name="status" id="status" value="{{ $data->status ?? '1' }}">
                    <input type="checkbox" name="setStatus" id="change_status" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" @if(!empty($data) && $data->status==1) checked @elseif(empty($data)) checked @endif>
                </div>
            </div>
            </div>
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ url('business/get-fit/exercises') }}">
                        <button type="button" class="btn-hover color-11 m-b-20 btn-col-6-cancel" name="btnCancel" id="btnCancel">{{ __('Cancel') }}</button></a>
                        <button type="submit" class="btn-hover color-9 m-b-20 btn-col-6-save ml-3" name="btnSubmit" id="btnSubmit">{{ __('Save') }}</button>
                    </div>
                    <div class="col-md-5">
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
<script type="text/javascript" src="{{ asset('backend/assets/business/js/manage_exercise.js') }}" onload="add();"></script>

@endpush
