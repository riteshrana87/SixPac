@if (session('status'))
<div class="alert alert-success" role="alert">
    {{ session('status') }}
</div>
@endif

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/exercise.css') }}">
@endpush

<!-- Page-header end -->
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card page-content">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Add Exercise</h5><span>All fields (<span class="req_star">*</span>) are required.</span>
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
                    <form class="form-horizontal spform" name="frmAddEditExercise" id="frmAddEditExercise" method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('business.workout.add-exercise') }}">
                        @csrf
                        <input type="hidden" name="created_by" id="created_by" value="{{Auth::user()->id}}">
                        <input type="hidden" name="id" id="id" value="{{ $data->id ?? null }}">
                        <input type="hidden" name="media" id="exerciseVideo" value="">
                        <input type="hidden" class="video" name="video_thumb" id="video_thumb">

                        <div class="store"></div>

                            <div class="row pb-4">
                                <div class="col-md-12">
                                    <div class="field">
                                        <label>Exercise title: <span class="req_star">*</span></label>
                                        <input class="form-control req" type="text" name="name" id="exercise_title" placeholder="Enter exercise title" maxlength="150" value="{{ old('exercise_title') }}">
                                    </div>
                                </div>
                            </div>


                        <div class="ExerciseCategory pb-4">
                            <div class="row">
                                <div class="col-md-12 section-title">
                                    <label id="err_interests">Select interests: <span class="req_star">*</span></label>
                                </div>
                                @if (!empty($exerciseData['interests']))
                                <ul>
                                    @foreach($exerciseData['interests'] as $key => $interest)
                                    <?php $iconUrl = (!empty($interest['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$interest['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$interest['icon_file']) : asset('backend/assets/images/no-icon.png');
                                    ?>
                                    <li class="text-center">
                                        <div class="box-middle">
                                            <article>
                                                <input type="checkbox" id="interests_<?=$interest['id']?>" name="interests[]" data-name="interests" value="{{$interest['id']}}" />
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
                                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_HEIGHT').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_HEIGHT').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                                    ?>
                                    <li class="text-center">
                                        <div class="box-middle">
                                            <article>
                                                <input type="radio" id="wo_type_<?=$row['id']?>" name="workout_type_id" data-name="workout_type_id" value="{{$row['id']}}" />
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
                                                <input type="radio" id="duration_id_<?=$key?>" name="duration_id" data-name="duration_id" value="{{$key}}" />
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
                                    <label id="err_equipment_id">Select equipment: <span class="req_star">*</span></label>
                                </div>
                                @if (!empty($exerciseData['equipments']))
                                <ul>
                                    @foreach($exerciseData['equipments'] as $key => $row)
                                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.EQUIPMENT_THUMB_PHOTO_HEIGHT').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.EQUIPMENT_THUMB_PHOTO_HEIGHT').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                                    ?>
                                    <li class="text-center">
                                        <div class="box-middle">
                                            <article>
                                                <input type="checkbox" id="equipment_id_<?=$row['id']?>" name="equipment[]" data-name="equipment_id" value="{{$row['id']}}" />
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
                                    <label id="err_body_part_id">Select body part: <span class="req_star">*</span></label>
                                </div>
                                @if (!empty($exerciseData['bodyParts']))
                                <ul>
                                    @foreach($exerciseData['bodyParts'] as $key => $row)
                                    <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.BODY_PART_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.BODY_PART_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                                    ?>
                                    <li class="text-center">
                                        <div class="box-middle">
                                            <article>
                                                <input type="checkbox" id="body_part_id_{{$row['id']}}" name="body_part[]" data-name="body_part_id" value="{{$row['id']}}" />
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
                                        <label id="err_fitness_level_id">Select fitness level: <span class="req_star">*</span></label>
                                    </div>
                                    @if (!empty($exerciseData['fitnessLevels']))
                                    <ul>
                                        @foreach($exerciseData['fitnessLevels'] as $key => $row)
                                        <?php $iconUrl = (!empty($row['icon_file']) && Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.FITNESS_LEVEL_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file'])) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.FITNESS_LEVEL_THUMB_PHOTO_UPLOAD_PATH').$row['icon_file']) : asset('backend/assets/images/no-icon.png');
                                        ?>
                                        <li class="text-center">
                                            <div class="box-middle">
                                                <article>
                                                    <input type="checkbox" id="fitness_level_id_{{$row['id']}}" name="fitness_level[]" data-name="fitness_level_id" value="{{$row['id']}}" />
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
                                        <input class="form-control req" type="file" name="poster_image" id="poster_img" placeholder="Select poster image" value="{{ old('poster_image') }}" accept=".jpeg, .jpg, .png">
                                        <label class="custom-file-label" for="poster_img">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field post_image_preview" id="exerciseImgPreview" style="display:none">
                                    <img src="http://placehold.jp/500x300.png" class="img-fluid">
                                </div>
                            </div>
                        </div>

                        <div class="row ExerciseCategory pb-4">
                            <div class="col-md-9">
                                <div class="video_info">
                                    <div class="field">
                                        <label>Select exercise video: <span class="req_star">*</span></label>
                                        <div class="custom-file">
                                            <input class="form-control req" type="file" name="video_name" id="videoName" placeholder="Select exercise video" value="{{ old('video_name') }}" accept=".mp4, .mov, .mkv, .webm">
                                            <label class="custom-file-label" for="videoName">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field video_preview" id="ExevideoPreview" style="display:none">
                                    <img src="" id="previewImg" style="width:100%;height:100%;">
                                    <a href="javascript:void(0)" id="deleteExeVideo" class="deleteMedia"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>


                        <div class="row pb-4">
                            <div class="col-md-9">
                                <div class="field">
                                    <label>Overview: <span class="req_star">*</span></label>
                                    <textarea class="form-control req" name="overview" id="exerOverview" placeholder="Enter exercise overview" maxlength="500" rows="8">{{ old('overview') }}</textarea>
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
                                                    <input type="radio" id="gender_id_{{$key}}" name="gender" data-name="gender" value="{{$key}}" />

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
                            <div class="col-md-4 p-0">
                                <div class="ExerciseLocation">
                                    <div class="col-md-12 section-title">
                                        <label id="err_location">Exercise location: <span class="req_star">*</span></label>
                                    </div>
                                    @if (!empty($exerciseData['location']))
                                    <ul>
                                        @foreach($exerciseData['location'] as $key => $location)
                                        <li class="text-center">
                                            <div class="box-middle">
                                                <article class="location">
                                                    <input type="radio" id="location_id_{{$key}}" name="location" data-name="location" value="{{$key}}" />
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
                                @if (!empty($exerciseData['ageGroups']))
                                <ul>
                                    @foreach($exerciseData['ageGroups'] as $key => $ageGroup)
                                    <li class="text-center">
                                        <div class="box-middle">
                                            <article>
                                                <input type="checkbox" id="age_group_id_{{$key}}" name="age_group[]" data-name="age_group" value="{{$key}}" />
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

                        <input type="hidden" name="status" id="status" value="1">
                       <!--  <div class="row pb-4">
                            <div class="col-md-6">
                                <div class="field mb-4">
                                    <label>Status: </label><br>
                                    <input type="hidden" name="status" id="status" value="1">
                                    <input type="checkbox" name="setStatus" data-size="small" data-on-text="Active" data-off-text="Deactive" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="active-status" checked>
                                </div>
                            </div>
                        </div> -->
                        <div class="footer_btn_small d-flex">
                            <button type="button" class="btn-hover color-11" name="user_closeBtn" id="user_closeBtn" data-dismiss="modal" data-dismiss="modal" title="Cancel">{{ __('Cancel') }}</button>
                            <button type="button" class="btn-hover color-9 ml-3" name="yesBtn" id="saveExerciseBtn" title="Save">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            <!-- Form code end here -->
            </div>
        </div>
    </div>
</div>
