<?php

use Illuminate\Support\Facades\Session;

/**
 * getCountryName: Get country name from country id
 *
 * @param  mixed $countryId: Country id
 * @return void
 */
function getCountryName($countryId){
	$results =  \App\Models\Country::select('name')->where('id',$countryId)->first();
	$name = '';
	if(!empty($results)){
		$name = $results->name;
	}
	return $name;
}

/**
 * getStateName: Get state name from state id
 *
 * @param  mixed $stateId: State id
 * @return void
 */
function getStateName($stateId){
	$results =  \App\Models\State::select('name')->where('id',$stateId)->first();
	$name = '';
	if(!empty($results)){
		$name = $results->name;
	}
	return $name;
}

/**
 * getCityName: Get city name from city id
 *
 * @param  mixed $cityId: City id
 * @return void
 */
function getCityName($cityId){
	$results =  \App\Models\City::select('name')->where('id',$cityId)->first();
	$name = '';
	if(!empty($results)){
		$name = $results->name;
	}
	return $name;
}

/**
 * getCountryId: Get country id from country name
 *
 * @param  mixed $country: Country name
 * @return void
 */
function getCountryId($country){
	$results =  \App\Models\Country::select('id')->whereRaw('LOWER(name) = ?', strtolower($country))->first();
    $id = '';
	if(!empty($results)){
		$id = $results->id;
	}
	return $id;
}

/**
 * getStateId: Get state id from state name
 *
 * @param  mixed $state: State name
 * @return void
 */
function getStateId($state){
	$results =  \App\Models\State::select('id')->whereRaw('LOWER(name) = ?', strtolower($state))->first();
	$id = '';
	if(!empty($results)){
		$id = $results->id;
	}
	return $id;
}

/**
 * getCityId: Get city id from city name
 *
 * @param  mixed $city: city name
 * @return void
 */
function getCityId($city){
	$results =  \App\Models\City::select('id')->whereRaw('LOWER(name) = ?', strtolower($city))->first();
	$id = '';
	if(!empty($results)){
		$id = $results->id;
	}
	return $id;
}


/**
 * convertPhoneToInt
 *
 * @param  mixed $phone
 * @return void
 */
function convertPhoneToInt($phone){
	if(!empty($phone)){
		$phoneNumber = str_replace('-', '', $phone); // Replaces all spaces with hyphens.
		$phone = preg_replace('/[^A-Za-z0-9\-]/', '', $phoneNumber); // Removes special chars.
		return (int)$phone;
	}
}


/**
 * convertPhoneToUsFormat
 *
 * @param  mixed $phone
 * @return void
 */
function convertPhoneToUsFormat($phone){
	if(!empty($phone)){
		if(  preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $phone,  $matches ) ){
			$phone = '('.$matches[1]. ') ' .$matches[2] . '-' . $matches[3];
		}
		return $phone;
	}
}

/**
 * deleteProductAndMedia
 *
 * @param  mixed $productId
 * @param  mixed $originalPath
 * @param  mixed $thumbPath
 * @param  mixed $isDeleteProduct: if value is 0 than delete only product media else delete product with product media
 * @return void
 */
function deleteProductAndMedia($productId, $originalPath, $thumbPath, $isDeleteProduct){
    $galleryRsArr = \App\Models\ProductGallery::where('product_id',$productId)->get();

    if(count($galleryRsArr) > 0){

        foreach($galleryRsArr as $galleryFiles){

            $originalStoragePath = $originalPath.$galleryFiles->file_name;
            $thumbStoragePath = $thumbPath.$galleryFiles->thumb_name;

            if (file_exists($originalStoragePath)) {
                @unlink($originalStoragePath);
            }
            if (file_exists($thumbStoragePath)) {
                @unlink($thumbStoragePath);
            }
        }
    }
    // Remove exiting gallery from gallery table
    // \App\Models\ProductGallery::where('product_id',$productId)->delete();
    \App\Models\ProductGallery::where('product_id',$productId)->forceDelete();

    if($isDeleteProduct == 1){
        \App\Models\Products::where('id',$productId)->forceDelete();
    }

    return true;
}


/**
 * deleteProductMediaFromMediaId
 *
 * @param  mixed $mediaId
 * @param  mixed $originalPath
 * @param  mixed $thumbPath
 * @param  mixed $isDeleteProduct: if value is 0 than delete only product media else delete product with product media.
 * @return void
 */
function deleteProductMediaFromMediaId($mediaId, $originalPath, $thumbPath, $isDeleteProduct){

	if(!empty($mediaId)){
		$obj = \App\Models\ProductGallery::find($mediaId);
	}

	if($isDeleteProduct == 1 && !empty($isDeleteProduct) && !empty($obj)){
		// $galleryObj = \App\Models\Products::delete($obj->product_id);
		$galleryObj = \App\Models\Products::where('id',$obj->product_id)->forceDelete();
	}

	if(!empty($mediaId)){
		$originalStoragePath	= $originalPath.$obj->file_name;
		$thumbStoragePath		= $thumbPath.$obj->thumb_name;

		if (file_exists($originalStoragePath)) {
			@unlink($originalStoragePath);
		}
        if (file_exists($thumbStoragePath)) {
			@unlink($thumbStoragePath);
		}
		$obj->forceDelete();
	}
}

/**
 * deletePostMediaFromPostId
 *
 * @param  mixed $postId
 * @param  mixed $originalPath
 * @param  mixed $thumbPath
 * @param  mixed $isDeletePost: if value is 0 than delete only post media else delete post with post media.
 * @return void
 */
function deletePostMediaFromPostId($postId, $originalPath, $thumbPath, $isDeletePost){
    if(!empty($postId)){

        $galleryRsArr = \App\Models\PostGallery::where('post_id',$postId)->get();

        if(count($galleryRsArr) > 0){

            foreach($galleryRsArr as $galleryFiles){

                $originalStoragePath = $originalPath.$galleryFiles->file_name;
                $thumbStoragePath = $thumbPath.$galleryFiles->thumb_name;

                if (file_exists($originalStoragePath)) {
                    @unlink($originalStoragePath);
                }
                if (file_exists($thumbStoragePath)) {
                    @unlink($thumbStoragePath);
                }
            }
        }

        $obj = \App\Models\PostGallery::where('post_id',$postId)->delete();
    }

    if($isDeletePost == 1){
        \App\Models\UserPost::where('id',$postId)->forceDelete();
    }
}

/**
 * deletePostMediaFromMediaId
 *
 * @param  mixed $mediaId
 * @param  mixed $originalPath
 * @param  mixed $thumbPath
 * @param  mixed $isDeletePost: if value is 0 than delete only post media else delete post with post media.
 * @return void
 */
function deletePostMediaFromMediaId($mediaId, $originalPath, $thumbPath, $isDeletePost){

	if(!empty($mediaId)){
		$obj = \App\Models\PostGallery::find($mediaId);
	}

	if($isDeletePost == 1 && !empty($isDeletePost) && !empty($obj)){
		$galleryObj = \App\Models\UserPost::where('id',$obj->post_id)->delete();
		// $galleryObj = \App\Models\UserPost::where('id',$obj->post_id)->forceDelete();
	}

	if(!empty($mediaId)){
		$originalStoragePath	= $originalPath.$obj->file_name;
		$thumbStoragePath		= $thumbPath.$obj->thumb_name;

		if (file_exists($originalStoragePath)) {
			@unlink($originalStoragePath);
		}
        if (file_exists($thumbStoragePath)) {
			@unlink($thumbStoragePath);
		}
		// $obj->forceDelete();
        $obj->delete();
	}
}


/**
 * getUserTimeZone: Get current users timezone from IP address.
 *
 * @return void
 */
function getUserTimeZone(){
    $ip = request()->ip();
    $ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
    $ipInfo = json_decode($ipInfo);
    if($ipInfo->status == 'fail'){
        $localTimezone = "UTC";
        }else{
        $localTimezone = $ipInfo->timezone;
    }
    //$localTimezone = "Asia/Kolkata";
    //return $localTimezone;
    $timeZone = Session::put('user_timezone', $localTimezone);
    return $timeZone;
}

/**
 * getVideoThumb is used to get thumbnail from video
 *
 * @param  string  $videoPath   video path from which you want thumbnail
 * @param  string  $thumbPath   thumb path that you want to store
 * @param  integer $videoLength video length from were thumb can be captured
 * @return mixed   thumb path or false
 * @author Spec Developer
 */
function getVideoThumb($videoPath, $thumbPath, $videoLength = 0)
{
    try {
        if (!is_file($videoPath) || !file_exists($videoPath) || empty($thumbPath)) {
            return false;
        }
        $ffmpeg = \FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => env('FFMPEG_BINARY'),
            'ffprobe.binaries' => env('FFPROBE_BINARY')
        ]);
        $video = $ffmpeg->open($videoPath);
        if (!$video) {
            return false;
        }
        $time_to_image = !empty($videoLength) ? floor(($videoLength)/2) : $videoLength;
        $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($time_to_image));
        $frame->save($thumbPath);
        return basename($thumbPath) ?? null;
    } catch (\Exception $e) {
        return false;
    }
}

?>