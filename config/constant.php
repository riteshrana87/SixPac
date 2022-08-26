<?php

return [

    'SUPER_ADMIN_SLUG' => 'superadmin',
    'NORMAL_USER_SLUG' => 'user',
    //'AWS_BUCKET_LINK' => env('AWS_BUCKET_LINK'),
    //'AWS_BUCKET' => env('AWS_BUCKET'),
    'REMOTELY' => env('REMOTELY'),

    // Status
    'ACTIVE_FLAG' => 'active',
    'INACTIVE_FLAG' => 'inactive',
    'ACTIVATION_TOKEN_LENGTH' => 36,

    // Gender
    'MALE' => 1,
    'FEMALE' => 0,

    'POST_STATUS'=>[
        '1'=>'Active',
        '0'=>'In Active',
    ],

    'PUBLIC_PUBLISH'=>[
        '1'=>'True',
        '0'=>'Fasle',
    ],

    // Import/Export users Module
    'IMPORT_ADMIN_USER_CSV_UPLOAD_PATH' => 'uploads/csv/import/admin/',
    'EXPORT_ADMIN_USER_CSV_UPLOAD_PATH' => 'uploads/csv/export/admin/',
    'IMPORT_BUSINESS_USER_CSV_UPLOAD_PATH' => 'uploads/csv/import/business/',
    'EXPORT_BUSINESS_USER_CSV_UPLOAD_PATH' => 'uploads/csv/export/business/',
    'IMPORT_CONSUMER_USER_CSV_UPLOAD_PATH' => 'uploads/csv/import/consumer/',
    'EXPORT_CONSUMER_USER_CSV_UPLOAD_PATH' => 'uploads/csv/export/consumer/',

    // User profile Module
    'USER_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/users/original/',
    'USER_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/users/thumb/',
    'USER_THUMB_PHOTO_HEIGHT' => 500,
    'USER_THUMB_PHOTO_WIDTH' => 500,


    // User banner image
    'BANNER_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/banner/original/',
    'BANNER_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/banner/thumb/',
    'BANNER_THUMB_PHOTO_HEIGHT' => 500,
    'BANNER_THUMB_PHOTO_WIDTH' => 500,

    // Squad image
    'SQUAD_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/squad/original/',
    'SQUAD_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/squad/thumb/',
    'SQUAD_THUMB_PHOTO_HEIGHT' => 500,
    'SQUAD_THUMB_PHOTO_WIDTH' => 500,

    // Squad banner image
    'SQUAD_BANNER_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/squad/banner/original/',
    'SQUAD_BANNER_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/squad/banner/thumb/',
    'SQUAD_BANNER_THUMB_PHOTO_HEIGHT' => 500,
    'SQUAD_BANNER_THUMB_PHOTO_WIDTH' => 500,



    // Super Admin User Module
    'SUPERADMIN_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/superadmin/original/',
    'SUPERADMIN_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/superadmin/thumb/',
    'SUPERADMIN_THUMB_PHOTO_HEIGHT' => 500,
    'SUPERADMIN_THUMB_PHOTO_WIDTH' => 500,

    //  User Module
    'BUSINESS_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/business/original/',
    'BUSINESS_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/business/thumb/',
    'BUSINESS_THUMB_PHOTO_HEIGHT' => 500,
    'BUSINESS_THUMB_PHOTO_WIDTH' => 500,


    //  User Module
    'CONSUMER_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/consumer/original/',
    'CONSUMER_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/consumer/thumb/',
    'CONSUMER_THUMB_PHOTO_HEIGHT' => 500,
    'CONSUMER_THUMB_PHOTO_WIDTH' => 500,

    // Interest Module
    'INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/interests/original/',
    'INTEREST_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/interests/thumb/',
    'INTEREST_THUMB_PHOTO_HEIGHT' => 500,
    'INTEREST_THUMB_PHOTO_WIDTH' => 500,

    // User Post Module
    'POST_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/post/original/',
    'POST_ORG_PHOTO_HEIGHT' => 500,
    'POST_ORG_PHOTO_WIDTH' => 500,

    'POST_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/post/thumb/',
    'POST_THUMB_PHOTO_HEIGHT' => 400,
    'POST_THUMB_PHOTO_WIDTH' => 400,

    // User Story Module
    'STORY_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/story/original/',
    'STORY_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/story/thumb/',
    'STORY_THUMB_PHOTO_HEIGHT' => 500,
    'STORY_THUMB_PHOTO_WIDTH' => 500,

    // User products Module
    'PRODUCTS_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/products/original/',
    'PRODUCTS_ORG_PHOTO_HEIGHT' => 500,
    'PRODUCTS_ORG_PHOTO_WIDTH' => 500,

    'PRODUCTS_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/products/thumb/',
    'PRODUCTS_THUMB_PHOTO_HEIGHT' => 400,
    'PRODUCTS_THUMB_PHOTO_WIDTH' => 400,

    // Import/Export products Module
    'IMPORT_PRODUCTS_CSV_UPLOAD_PATH' => 'uploads/csv/import/products/',
    'EXPORT_PRODUCTS_CSV_UPLOAD_PATH' => 'uploads/csv/export/products/',
    'IMPORT_PRODUCTS_ZIP_UPLOAD_PATH' => 'uploads/csv/import/products/zip/',
    'DOWNLOAD_PRODUCTS_CSV_SAMPLE_UPLOAD_PATH'=> 'uploads/csv/export/products/',

    // User Account verification Module
    'PRODUCTS_ACCOUNT_VERIFICATION_PHOTO_UPLOAD_PATH' => 'uploads/accountverification/original/',
    'PRODUCTS_ACCOUNT_VERIFICATION_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/accountverification/thumb/',
    'PRODUCTS_ACCOUNT_VERIFICATION_THUMB_PHOTO_HEIGHT' => 500,
    'PRODUCTS_ACCOUNT_VERIFICATION_THUMB_PHOTO_WIDTH' => 500,
    // Video Book Module
    'MAX_VIDEOS_PER_BOOK' => 7,

    // Video Extension
    'VIDEO_EXTENSION' => ['mp4', 'mpg', 'mpeg', 'mkv', 'webm', 'avi', 'wmv', 'mov'],
    'MEDIA_EXTENSION' => ['mp4' => 'mp4', 'quicktime' => 'mov', 'x-matroska' => 'mkv', 'webm' => 'webm', 'jpg' => 'jpg', 'jpeg' => 'jpeg', 'png' => 'png'],

    // Image Extension
    'IMAGE_EXTENSION' => ['png','jpeg','jpg'],

    // Event Video Module
    'EVENT_VIDEO_TEMP_UPLOAD_PATH' => 'uploads/event/temp/',
    'EVENT_VIDEO_UPLOAD_PATH' => 'uploads/event/videos/',
    'EVENT_VIDEO_THUMB_UPLOAD_PATH' => 'uploads/event/thumb/',

    'VIDEO_EVENT_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/videoseries/original/',
    'VIDEO_EVENT_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/event/video/thumb/',
    'VIDEO_EVENT_THUMB_PHOTO_HEIGHT' => 500,
    'VIDEO_EVENT_THUMB_PHOTO_WIDTH' => 500,

    //Story
    'STORY_VIDEO_TEMP_UPLOAD_PATH' => 'uploads/story/temp/',
    'STORY_VIDEO_UPLOAD_PATH' => 'uploads/story/videos/',
    'STORY_VIDEO_THUMB_UPLOAD_PATH' => 'uploads/story/thumb/',
    'VIDEO_STORY_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/story/video/thumb/',

    // Get Api side pagination Count
    'CATEGORY_LIST_PER_PAGE' => 10,
    'LIST_PER_PAGE' => 10,
    'SERIES_HISTORY_PER_PAGE' => 10,
    'VIDEO_HISTORY_PER_PAGE' =>10,
    'COMMENT_LIST_PER_PAGE' =>10,
    'AUTHOR_PER_PAGE'=>10,

    'VIDEO_ADVERTISEMENT_ORIGINAL_UPLOAD_PATH' => 'uploads/video/original/',
    'FILESYSTEM_DRIVER' => env('FILESYSTEM_DRIVER', 'public'),
    'FILESYSTEM_CLOUD' => env('FILESYSTEM_CLOUD', 'public'),
    //'AWS_URL' => 'https://s3.amazonaws.com/inexturemaincode/',

    'DEFAULT_PER_PAGE' => 10,

    //Video Category Module
    'VIDEO_CATEGORY_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/videocategory/original/',
    'VIDEO_CATEGORY_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/videocategory/thumb/',
    'VIDEO_CATEGORY_THUMB_PHOTO_HEIGHT' => 500,
    'VIDEO_CATEGORY_THUMB_PHOTO_WIDTH' => 500,

    'IMG_AD_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/imgAd/original/',
    'IMG_AD_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/imgAd/thumb/',
    'IMG_AD_THUMB_PHOTO_HEIGHT' => 500,
    'IMG_AD_THUMB_PHOTO_WIDTH' => 500,

    'TOKEN_EXPIRE_HOURS' => 24,
    'Apple_LOGIN' => 'apple',
    'FB_LOGIN' => 'facebook',
    'GOOGLE_LOGIN' => 'google',
    'DEFAULT_USER_IMAGE' => 'images/default_user_profile.png',

    'LIKE' => 'like',
    'UNLIKE' => 'unlike',
    'REVERT' => 'revert',

    // Is Completed
    'IS_COMPLETED_TRUE' => 1,
    'IS_COMPLETED_FALSE' => 0,

    'API_ACCESS_KEY' => env('API_ACCESS_KEY'),
    'NEWS_API_KEY' => env('NEWS_API_FEED_KEY'),

    'FCM_API_KEY_DATA' => env('FCM_API_KEY_NEW'),

    'RSA_PRIVATE_KEY' => env('RSA_PRIVATE_KEY'),
    'RSA_PUBLIC_KEY' => env('RSA_PUBLIC_KEY'),

    // WORKOUT Type Module
    'WORKOUT_TYPE_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/workout_category/original/',
    'WORKOUT_TYPE_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/workout_category/thumb/',
    'WORKOUT_TYPE_THUMB_PHOTO_HEIGHT' => 500,
    'WORKOUT_TYPE_THUMB_PHOTO_WIDTH' => 500,

    // EQUIPMENT Module
    'EQUIPMENT_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/equipment/original/',
    'EQUIPMENT_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/equipment/thumb/',
    'EQUIPMENT_THUMB_PHOTO_HEIGHT' => 500,
    'EQUIPMENT_THUMB_PHOTO_WIDTH' => 500,

    // PLAN GOAL Module
    'PLAN_GOAL_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/plan_goal/original/',
    'PLAN_GOAL_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/plan_goal/thumb/',
    'PLAN_GOAL_THUMB_PHOTO_HEIGHT' => 500,
    'PLAN_GOAL_THUMB_PHOTO_WIDTH' => 500,

    // PLAN SPORT Module
    'PLAN_SPORT_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/plan_sport/original/',
    'PLAN_SPORT_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/plan_sport/thumb/',
    'PLAN_SPORT_THUMB_PHOTO_HEIGHT' => 500,
    'PLAN_SPORT_THUMB_PHOTO_WIDTH' => 500,

    // WORKOUT Module
    'WORKOUT_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/workout/original/',
    'WORKOUT_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/workout/thumb/',
    'WORKOUT_THUMB_PHOTO_HEIGHT' => 500,
    'WORKOUT_THUMB_PHOTO_WIDTH' => 500,

    // WORKOUT Plan Module
    'WORKOUT_PLAN_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/workout_plan/original/',
    'WORKOUT_PLAN_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/workout_plan/thumb/',
    'WORKOUT_PLAN_THUMB_PHOTO_HEIGHT' => 500,
    'WORKOUT_PLAN_THUMB_PHOTO_WIDTH' => 500,

    // User Post Module
    'EXERCISE_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/exercise/original/',
    'EXERCISE_ORG_PHOTO_HEIGHT' => 500,
    'EXERCISE_ORG_PHOTO_WIDTH' => 500,
    'EXERCISE_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/exercise/thumb/',
    'EXERCISE_THUMB_PHOTO_HEIGHT' => 400,
    'EXERCISE_THUMB_PHOTO_WIDTH' => 400,

    // Body Part Module
    'BODY_PART_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/body_part/original/',
    'BODY_PART_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/body_part/thumb/',
    'BODY_PART_ORG_PHOTO_HEIGHT' => 500,
    'BODY_PART_ORG_PHOTO_WIDTH' => 500,

    // Fitness level Module
    'FITNESS_LEVEL_ORIGINAL_PHOTO_UPLOAD_PATH' => 'uploads/fitness_level/original/',
    'FITNESS_LEVEL_THUMB_PHOTO_UPLOAD_PATH' => 'uploads/fitness_level/thumb/',
    'FITNESS_LEVEL_ORG_PHOTO_HEIGHT' => 500,
    'FITNESS_LEVEL_ORG_PHOTO_WIDTH' => 500,

    'GETFIT_GENDER' => [
        1=> [
            'gender'=>'Male','icon'=>'backend/assets/images/icons/male.png'
        ],
        2 => [
            'gender'=>'Female','icon'=>'backend/assets/images/icons/female.png'
        ],
        3=>[
            'gender'=>'Unisex','icon'=>'backend/assets/images/icons/unisex.png'
        ]
    ],

    'GETFIT_LOCATION' => [
        1=> [
            'location'=>'Gym','icon'=>'backend/assets/images/icons/gym.png'
        ],
        2 => [
            'location'=>'Home','icon'=>'backend/assets/images/icons/home.png'
        ],
        3=>[
            'location'=>'Anywhere','icon'=>'backend/assets/images/icons/anywhere.png'
        ]
    ],
];