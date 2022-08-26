<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type, mobile-app');

// User Login
//User register
Route::group([
    'prefix' => 'v1'
], function () {
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('login-with-mobile', [App\Http\Controllers\Api\AuthController::class, 'loginWithMobile']);

    Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('without-login-generate-otp', [App\Http\Controllers\Api\AuthController::class, 'WithOutLoginGenerateOTP']);

    Route::post('password/forgot-password', [App\Http\Controllers\Api\ForgotPasswordController::class, 'sendResetLinkResponse'])->name('passwords.sent');

    Route::post('auth/social-signup', [App\Http\Controllers\Api\SocialController::class, 'socialsignup']);
    Route::post('auth/social-login', [App\Http\Controllers\Api\SocialController::class, 'socialLogin']);
    // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //     return $request->user();
    // });
    Route::post('food-data', [App\Http\Controllers\Api\FoodController ::class, 'getFoodData']);

    Route::post('generate-otp', [App\Http\Controllers\Api\AuthController::class, 'generateOTP']);

    Route::post('verify-otp', [App\Http\Controllers\Api\AuthController::class, 'verifyOTP']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::post('check-username', [App\Http\Controllers\Api\AuthController::class, 'checkUsername']);
        Route::post('user-follower', [App\Http\Controllers\Api\Consumer\UserFollowerController ::class, 'store']);

        Route::delete('remove-followers/{id}', [App\Http\Controllers\Api\Consumer\UserFollowerController ::class, 'removeFollowers']);
        Route::delete('remove-followings/{id}', [App\Http\Controllers\Api\Consumer\UserFollowerController ::class, 'removefollowings']);

        

        Route::post('approved', [App\Http\Controllers\Api\Consumer\UserFollowerController ::class, 'update'])->name('approved');
        Route::post('followings', [App\Http\Controllers\Api\Consumer\UserFollowerController ::class, 'followings'])->name('followings');
        Route::post('followers', [App\Http\Controllers\Api\Consumer\UserFollowerController ::class, 'followers'])->name('followers');

        Route::post('following-and-followers-unique-user', [App\Http\Controllers\Api\Consumer\UserFollowerController ::class, 'getUserfollowingAndfollowers']);

        

        Route::post('userpost', [App\Http\Controllers\Api\UserPostController::class, 'store']);
        Route::get('like-post/{post_id}', [App\Http\Controllers\Api\UserPostController::class, 'liekPost']);
        Route::post('users-all-post', [App\Http\Controllers\Api\UserPostController::class, 'getUsersAllPost']);
        Route::post('add-post-comment', [App\Http\Controllers\Api\UserPostController::class, 'addPostComment']);
        Route::delete('delete-post/{id}', [App\Http\Controllers\Api\UserPostController::class, 'destroy']);

        Route::get('get-post-detail/{id}', [App\Http\Controllers\Api\UserPostController::class, 'show']);
        Route::post('edit-post', [App\Http\Controllers\Api\UserPostController::class, 'update']);
        Route::post('comments-upvote-downvote', [App\Http\Controllers\Api\UserPostController::class, 'commentsUpvoteDownvote']);
        Route::post('get-post-by-hash-tag', [App\Http\Controllers\Api\UserPostController::class, 'getPostByHashTag']);
        Route::post('report-problem', [App\Http\Controllers\Api\UserPostController::class, 'reportProblem']);
        Route::post('share-post-with-squad', [App\Http\Controllers\Api\UserPostController ::class, 'sharePostWithSquad']);
        Route::post('share-post-squad-to-feed', [App\Http\Controllers\Api\UserPostController ::class, 'sharePostSquadToFeed']);
        

        
        
        
        //Route::post('comments-downvote', [App\Http\Controllers\Api\UserPostController::class, 'commentsDownvote']);

        

        Route::post('get-all-hashtag', [App\Http\Controllers\Api\HashTagsController::class, 'getAllHashTag']);
        
        Route::get('interests', [App\Http\Controllers\Api\InterestsController::class, 'getInterestsData']);


        Route::post('refresh-token', [App\Http\Controllers\Api\AuthController::class, 'userRefreshToken']);
//Food
        Route::post('store-food-data', [App\Http\Controllers\Api\UserFoodController::class, 'store']);
        //Route::get('user-food-data', [App\Http\Controllers\Api\UserFoodController::class, 'index']);
        Route::post('user-food-data', [App\Http\Controllers\Api\UserFoodController::class, 'index']);
        Route::post('edit-food-data', [App\Http\Controllers\Api\UserFoodController::class, 'update']);
        Route::post('get-food-detail', [App\Http\Controllers\Api\UserFoodController::class, 'getFoodData']);
        Route::delete('delete-food-data/{id}', [App\Http\Controllers\Api\UserFoodController::class, 'destroy']);

        Route::post('feeds', [App\Http\Controllers\Api\UserPostController::class, 'seeAllLatestUpdatePost']);

        Route::post('personal-setting', [App\Http\Controllers\Api\UserSettingController::class, 'personalSetting']);
        Route::post('upload-profile', [App\Http\Controllers\Api\UserSettingController::class, 'uploadProfileAndBanner']);

        
        Route::post('getnotification', [App\Http\Controllers\Api\NotificationController::class, 'getNotificationUser']);

        Route::post('read-notification', [App\Http\Controllers\Api\NotificationController::class, 'read']);
        Route::get('read-all-notification', [App\Http\Controllers\Api\NotificationController::class, 'readAll']);

        

        
        
        
        
        #Route::post('food-data', [App\Http\Controllers\Api\FoodController ::class, 'getFoodData']);
    //Exercise
        Route::post('search-exercise-list', [App\Http\Controllers\Api\ExerciseDataController::class, 'searchExerciseList']);
        Route::post('store-exercise-data', [App\Http\Controllers\Api\UserExerciseDataController::class, 'store']);
        Route::get('user-exercise-data', [App\Http\Controllers\Api\UserExerciseDataController::class, 'index']);
        Route::post('edit-exercise-data', [App\Http\Controllers\Api\UserExerciseDataController::class, 'update']);
        Route::post('get-exercise-detail', [App\Http\Controllers\Api\UserExerciseDataController::class, 'getExerciseData']);
        Route::delete('delete-exercise-data/{id}', [App\Http\Controllers\Api\UserExerciseDataController::class, 'destroy']);

    //Squad
        Route::post('store-squad-data', [App\Http\Controllers\Api\SquadController::class, 'store']);

        Route::post('send-request-to-squad', [App\Http\Controllers\Api\SquadController::class, 'sendRequestToSquad']);

        Route::post('approved-squad-request', [App\Http\Controllers\Api\SquadController ::class, 'approvedSquadRequest']);
        Route::post('reject-squad-request', [App\Http\Controllers\Api\SquadController ::class, 'rejectSquadRequest']);
        


        
        

        
        //Route::get('squad-list', [App\Http\Controllers\Api\SquadController::class, 'index']);
        
        Route::post('all-squad-list', [App\Http\Controllers\Api\SquadController::class, 'getSquadList']);
        Route::post('squad-list', [App\Http\Controllers\Api\SquadController::class, 'getAllSquad']);
        Route::post('edit-squad-data', [App\Http\Controllers\Api\SquadController::class, 'update']);
        Route::post('get-squad-detail', [App\Http\Controllers\Api\SquadController::class, 'getSquadData']);
        Route::delete('delete-squad-data/{id}', [App\Http\Controllers\Api\SquadController::class, 'destroy']);

        Route::post('get-squad-post', [App\Http\Controllers\Api\SquadController::class, 'getSquadAllPost']);
        Route::post('recommended-squad', [App\Http\Controllers\Api\SquadController::class, 'seeAllRecommendedSquad']);
        Route::post('send-request-recommended-squad', [App\Http\Controllers\Api\SquadController::class, 'sendRecommendedSquadToUser']);

        


        //GetFit section
        Route::post('get-workout-type', [App\Http\Controllers\Api\GetFitController::class, 'getWorkoutType']);
        Route::post('get-on-demand-services', [App\Http\Controllers\Api\GetFitController::class, 'getOnDemandServices']);
        Route::get('get-body-parts', [App\Http\Controllers\Api\GetFitController::class, 'getBodyParts']);
        Route::get('get-for-you-data', [App\Http\Controllers\Api\GetFitController::class, 'getForYouData']);
        Route::get('get-exercise-tab-data', [App\Http\Controllers\Api\GetFitController::class, 'getExerciseTabData']);
        Route::get('get-workout-tab-data', [App\Http\Controllers\Api\GetFitController::class, 'getWorkoutTabData']);
        Route::get('get-plan-tab-data', [App\Http\Controllers\Api\GetFitController::class, 'getPlanTabData']);

        Route::post('get-exercise-by-body-part', [App\Http\Controllers\Api\GetFitExcerciseController::class, 'getExerciseListByBodyPart']);
        

        
        Route::post('get-getfit-search-type', [App\Http\Controllers\Api\GetFitController::class, 'getGetfitSearchType']);

        Route::post('add-excercise', [App\Http\Controllers\Api\GetFitExcerciseController::class, 'addExcercise']);
        Route::post('excercise-chunk-video', [App\Http\Controllers\Api\GetFitExcerciseController::class, 'uploadExcerciseChunkVideo']);

        


        
        
        
        
        

    // Product Category

    Route::post('get-all-categorys', [App\Http\Controllers\Api\ProductCategoryController::class, 'getAllCategory']);
        
    Route::post('get-all-products', [App\Http\Controllers\Api\ProductsController::class, 'getAllProducts']);
    Route::get('get-product-detail/{id}', [App\Http\Controllers\Api\ProductsController::class, 'productDetail']);
    
    
        
    });

    Route::group(['middleware' => 'auth:api','prefix' => 'business'], function () {
        Route::get('profile', [App\Http\Controllers\Api\Business\UserController::class, 'profileDetails']);
        Route::post('update', [App\Http\Controllers\Api\Business\UserController::class, 'update']);
    });

    Route::group(['middleware' => 'auth:api','prefix' => 'consumer'], function () {
        Route::get('profile', [App\Http\Controllers\Api\Consumer\UserController::class, 'profileDetails']);
        Route::post('user-profile-details', [App\Http\Controllers\Api\Consumer\UserController::class, 'getUserProfileDetails']);
        Route::post('update', [App\Http\Controllers\Api\Consumer\UserController::class, 'update']);
        Route::post('update-fitness-status', [App\Http\Controllers\Api\Consumer\UserController::class, 'updateFitnessStatus']);

        Route::post('get-all-users', [App\Http\Controllers\Api\Consumer\UserController::class, 'getAllUsers']);

        
        Route::get('user-profile', [App\Http\Controllers\Api\Consumer\UserController::class, 'userMyProfile']);


        Route::post('consumer-interests-update', [App\Http\Controllers\Api\Consumer\ConsumerInterestsController ::class, 'update']);
        Route::delete('consumer-interests-remove/{id}', [App\Http\Controllers\Api\Consumer\ConsumerInterestsController ::class, 'destroy']);


    });

});
