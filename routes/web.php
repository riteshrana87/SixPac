<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','Auth\LoginController@showLoginForm')->name('login');
Route::get('/logout', '\Auth\LoginController@logout')->name('logout');
Route::get('/password-reset-done', 'PasswordResetSuccess@index');
Route::post('/getProfanityWords','ProfanityWordController@getProfanityWords')->name('superadmin.getProfanityWords');

Route::get('/business/test-form','TestForm@formExample')->name('business.formExample');

Auth::routes();

Route::group(['middleware' => ['auth', 'superadmin'], 'prefix' => 'superadmin'], function () {
	Route::get('/dashboard','Web\SuperAdmin\SuperAdminController@index')->name('superadmin.dashboard');
    Route::post('/dashboard/get-year-wise-user','Web\SuperAdmin\SuperAdminController@getYearWiseUser')->name('superadmin.user-graph');

    # Admin user route code start here
    Route::get('/users/admin-users','Web\SuperAdmin\AdminUserController@index')->name('admin-users');
    Route::get('/users/admin-users/add', 'Web\SuperAdmin\AdminUserController@add')->name('add-admin-user');
    Route::post('/users/admin-users/store', 'Web\SuperAdmin\AdminUserController@store')->name('store-admin-user');
    Route::get('/users/admin-users/edit/{id}','Web\SuperAdmin\AdminUserController@editAdminUser')->name('edit-admin-user');
    Route::put('/users/admin-users/save','Web\SuperAdmin\AdminUserController@updateAdminUser')->name('update-admin-user');
    Route::delete('/users/admin-users/destroy/{id}', 'Web\SuperAdmin\AdminUserController@destroy')->name('delete-admin-user');
    Route::put('/users/admin-users/changeStatus', 'Web\SuperAdmin\AdminUserController@changeStatus')->name('status-admin-user');
    Route::post('/users/admin-users/view', 'Web\SuperAdmin\AdminUserController@view');
    Route::post('/users/admin-users/importAdminUsers','Web\SuperAdmin\AdminUserController@importAdminUsers')->name('import-admin-user');
    Route::post('/users/admin-users/exportAdminUsers','Web\SuperAdmin\AdminUserController@exportAdminUsers')->name('export-admin-user');
    # Admin user route code end here

    # Business user route code start here
    Route::get('/users/business-users','Web\SuperAdmin\BusinessUserController@index')->name('business-users');
    Route::get('/users/business-users/add', 'Web\SuperAdmin\BusinessUserController@add')->name('add-business-user');
    Route::post('/users/business-users/store', 'Web\SuperAdmin\BusinessUserController@store')->name('store-business-user');
    Route::get('/users/business-users/edit/{id}','Web\SuperAdmin\BusinessUserController@editBusinessUser')->name('edit-business-user');
    Route::put('/users/business-users/save','Web\SuperAdmin\BusinessUserController@updateBusinessUser')->name('update-business-user');
    #Route::delete('/users/business-users/destroy/{id}', 'Web\SuperAdmin\BusinessUserController@destroy')->name('delete-business-user');
    Route::put('/users/business-users/changeStatus', 'Web\SuperAdmin\BusinessUserController@changeStatus')->name('status-business-user');
    Route::post('/users/business-users/view', 'Web\SuperAdmin\BusinessUserController@view');
    Route::post('/users/business-users/changeStatus', 'Web\SuperAdmin\BusinessUserController@changeStatus')->name('status-business-user');
    Route::post('/users/business-users/importBusinessUsers','Web\SuperAdmin\BusinessUserController@importBusinessUsers')->name('import-business-user');
    Route::post('/users/business-users/exportBusinessUsers','Web\SuperAdmin\BusinessUserController@exportBusinessUsers')->name('export-business-user');
    # Business user route code end here

    # Consumer user route code start here
    Route::get('/users/consumer-users','Web\SuperAdmin\ConsumerUserController@index')->name('consumer-users');
    Route::get('/users/consumer-users/add', 'Web\SuperAdmin\ConsumerUserController@add')->name('add-consumer-user');
    Route::post('/users/consumer-users/store', 'Web\SuperAdmin\ConsumerUserController@store')->name('store-consumer-user');
    Route::get('/users/consumer-users/edit/{id}','Web\SuperAdmin\ConsumerUserController@editConsumerUser')->name('edit-consumer-user');
    Route::put('/users/consumer-users/save','Web\SuperAdmin\ConsumerUserController@updateConsumerUser')->name('update-consumer-user');
    #Route::delete('/users/consumer-users/destroy/{id}', 'Web\SuperAdmin\ConsumerUserController@destroy')->name('delete-consumer-user');
    Route::post('/users/consumer-users/changeStatus', 'Web\SuperAdmin\ConsumerUserController@changeStatus')->name('status-consumer-user');
    Route::put('/users/consumer-users/changeStatus', 'Web\SuperAdmin\ConsumerUserController@changeStatus')->name('status-consumer-user');
    Route::post('/users/consumer-users/view', 'Web\SuperAdmin\ConsumerUserController@view');
    Route::post('/users/consumer-users/changeStatus', 'Web\SuperAdmin\ConsumerUserController@changeStatus')->name('status-consumer-user');
    Route::post('/users/consumer-users/importConsumerUsers','Web\SuperAdmin\ConsumerUserController@importConsumerUsers')->name('import-consumer-user');
    Route::post('/users/consumer-users/exportConsumerUsers','Web\SuperAdmin\ConsumerUserController@exportConsumerUsers')->name('export-consumer-user');
    # Consumer user route code end here

    # Interest route code start here
    Route::get('/interests','Web\SuperAdmin\InterestsController@index')->name('interests');
    Route::get('/interests/add', 'Web\SuperAdmin\InterestsController@add')->name('add-interests');
    Route::post('/interests/store', 'Web\SuperAdmin\InterestsController@store')->name('store-interests');
    Route::get('/interests/edit/{id}','Web\SuperAdmin\InterestsController@editInterest')->name('edit-interests');
    Route::put('/interests/save','Web\SuperAdmin\InterestsController@updateInterest')->name('update-interests');
    Route::delete('/interests/destroy/{id}', 'Web\SuperAdmin\InterestsController@destroy')->name('delete-interests');
    Route::post('/interests/view', 'Web\SuperAdmin\InterestsController@view');
    Route::get('/interests/checkInterstExists','Web\SuperAdmin\InterestsController@checkInterstExists')->name('superadmin.checkInterstExists');
    # Interest route code end here

    # Sub interest route code start here
    Route::get('/interests/sub-interests','Web\SuperAdmin\SubInterestsController@index')->name('sub-interests');
    //Route::get('/interests/sub-interests-list','Web\SuperAdmin\SubInterestsController@subInterestsList')->name('sub-interests-ajex');
    Route::get('/interests/sub-interests/add', 'Web\SuperAdmin\SubInterestsController@add')->name('add-sub-interests');
    Route::post('/interests/sub-interests/store', 'Web\SuperAdmin\SubInterestsController@store')->name('store-sub-interests');
    Route::get('/interests/sub-interests/edit/{id}','Web\SuperAdmin\SubInterestsController@editSubInterest')->name('edit-sub-interests');
    Route::put('/interests/sub-interests/save','Web\SuperAdmin\SubInterestsController@updateSubInterest')->name('update-sub-interests');
    Route::get('/interests/sub-interests/checkSubInterstExists','Web\SuperAdmin\SubInterestsController@checkSubInterstExists')->name('superadmin.checkSubInterstExists');
    Route::post('/interests/sub-interests/view', 'Web\SuperAdmin\SubInterestsController@view');
    Route::delete('/interests/sub-interests/destroy/{id}', 'Web\SuperAdmin\SubInterestsController@destroy')->name('delete-sub-interests');
    # Sub interest route code end here


    # Post route code start here
    Route::get('/my-posts','Web\SuperAdmin\PostsController@myPosts')->name('myposts');
    Route::get('/posts','Web\SuperAdmin\PostsController@usersPosts')->name('users-posts');
    Route::get('/my-posts/add', 'Web\SuperAdmin\PostsController@add')->name('add-post');
    Route::post('/my-posts/store', 'Web\SuperAdmin\PostsController@store')->name('superadmin.store-myposts');
    Route::get('/my-posts/edit/{id}','Web\SuperAdmin\PostsController@editPost')->name('edit-post');
    Route::put('/my-posts/save','Web\SuperAdmin\PostsController@updatePost')->name('update-myposts');
    Route::delete('/my-posts/destroy/{id}', 'Web\SuperAdmin\PostsController@destroy')->name('delete-myposts');
    Route::post('/my-posts/view', 'Web\SuperAdmin\PostsController@view');
    Route::post('/posts/view', 'Web\SuperAdmin\PostsController@view');
    Route::get('/posts/checkPostExists','Web\SuperAdmin\PostsController@checkPostExists')->name('superadmin.checkPostExists');
    Route::get('/posts/archive-posts','Web\SuperAdmin\PostsController@archivePosts')->name('superadmin.archive-posts');
    Route::delete('/posts/force-delete/{id}', 'Web\SuperAdmin\PostsController@forceDelete')->name('superadmin.force-delete-post');
    Route::post('/posts/archive-view', 'Web\SuperAdmin\PostsController@viewArchive');
    Route::put('/posts/restore/{id}', 'Web\SuperAdmin\PostsController@restorePost')->name('superadmin.restore-post');
    Route::post('/posts/view-post-likes', 'Web\SuperAdmin\PostsController@viewPostLikes');
    Route::post('/posts/view-post-flagged', 'Web\SuperAdmin\FlaggedPostController@viewPostFlagged');
    Route::put('/posts/flagged/changeStatus', 'Web\SuperAdmin\FlaggedPostController@changeStatus')->name('superadmin.changePostStatus');

    Route::get('/posts/flagged-posts','Web\SuperAdmin\PostsController@flaggedPosts')->name('superadmin.flagged-posts');
    # Post route code end here

    # Post comment route code start here
    Route::get('/posts/comments/{postId}','Web\SuperAdmin\PostCommentController@index')->name('posts.comments');
    Route::post('/posts/comments/reply','Web\SuperAdmin\PostCommentController@commentReplyForm')->name('posts.commentReply');
    Route::post('/posts/comments/add','Web\SuperAdmin\PostCommentController@addComment')->name('posts.addComment');
    Route::post('/posts/comment/saveComment','Web\SuperAdmin\PostCommentController@saveComment')->name('posts.saveComment');
    Route::post('/posts/comment/commentReply','Web\SuperAdmin\PostCommentController@saveCommentReply')->name('posts.commentReply');
    Route::post('/posts/comments/view', 'Web\SuperAdmin\PostCommentController@view');
    Route::post('/posts/comment/view-comment-upvotes', 'Web\SuperAdmin\PostCommentController@viewCommentUpVotes');
    Route::post('/posts/comment/view-comment-downvotes', 'Web\SuperAdmin\PostCommentController@viewCommentDownVotes');
    Route::get('/posts/comment/flagged','Web\SuperAdmin\FlaggedCommentController@index')->name('superadmin.flagged-comments');
    Route::post('/posts/comment/view-comment-flagged', 'Web\SuperAdmin\FlaggedCommentController@viewCommentFlagged');
    Route::put('/posts/comment/flagged/changeStatus', 'Web\SuperAdmin\FlaggedCommentController@changeStatus')->name('superadmin.changeCommentStatus');
    # Post comment route code end here

    Route::get('/advertisment','Web\SuperAdmin\AdvertismentController@index')->name('advertisment');

    #Route::get('/offer-codes','Web\SuperAdmin\OfferCodesController@index')->name('offer-codes');

    Route::get('/notifications','Web\SuperAdmin\NotificationsController@index')->name('notifications');

    # Offer code route code start here
    Route::get('/offer-codes','Web\SuperAdmin\OfferCodesController@index')->name('offer-codes');
    Route::get('/offer-codes/add', 'Web\SuperAdmin\OfferCodesController@add')->name('add-offer-code');
    Route::post('/offer-codes/store', 'Web\SuperAdmin\OfferCodesController@store')->name('store-offer-code');
    Route::get('/offer-codes/edit/{id}','Web\SuperAdmin\OfferCodesController@editOfferCode')->name('edit-offer-code');
    Route::put('/offer-codes/save','Web\SuperAdmin\OfferCodesController@updateOfferCode')->name('update-offer-code');
    Route::delete('/offer-codes/destroy/{id}', 'Web\SuperAdmin\OfferCodesController@destroy')->name('delete-offer-code');
    Route::post('/offer-codes/view', 'Web\SuperAdmin\OfferCodesController@view');
    Route::get('/offer-codes/checkOfferCodeExists','Web\SuperAdmin\OfferCodesController@checkOfferCodeExists')->name('superadmin.checkOfferCodeExists');
    # Offer code route code end here

    # Profanity words route code start here
    Route::get('/profanity-words','Web\SuperAdmin\ProfanityController@index')->name('superadmin.profanity-words');
    Route::get('/profanity-words/add', 'Web\SuperAdmin\ProfanityController@add')->name('superadmin.add-profanity-words');
    Route::post('/profanity-words/store', 'Web\SuperAdmin\ProfanityController@store')->name('superadmin.store-profanity-words');
    Route::get('/profanity-words/edit/{id}','Web\SuperAdmin\ProfanityController@editProfanityWord')->name('superadmin.edit-profanity-words');
    Route::put('/profanity-words/save','Web\SuperAdmin\ProfanityController@updateProfanityWord')->name('update-profanity-words');
    Route::delete('/profanity-words/destroy/{id}', 'Web\SuperAdmin\ProfanityController@destroy')->name('superadmin.delete-profanity-words');
    Route::post('/profanity-words/view', 'Web\SuperAdmin\ProfanityController@view');
    Route::get('/profanity-words/checkWordExists','Web\SuperAdmin\ProfanityController@checkWordExists')->name('superadmin.checkWordExists');
    # Profanity words route code end here

    # Fitness status route code start here
    Route::get('/fitness-status','Web\SuperAdmin\FitnessStatusController@index')->name('fitness-status');
    Route::get('/fitness-status/add', 'Web\SuperAdmin\FitnessStatusController@add')->name('add-fitness-status');
    Route::post('/fitness-status/store', 'Web\SuperAdmin\FitnessStatusController@store')->name('store-fitness-status');
    Route::get('/fitness-status/edit/{id}','Web\SuperAdmin\FitnessStatusController@editFitnessStatus')->name('edit-fitness-status');
    Route::put('/fitness-status/save','Web\SuperAdmin\FitnessStatusController@updateFitnessStatus')->name('update-fitness-status');
    Route::delete('/fitness-status/destroy/{id}', 'Web\SuperAdmin\FitnessStatusController@destroy')->name('delete-fitness-status');
    Route::post('/fitness-status/view', 'Web\SuperAdmin\FitnessStatusController@view');
    Route::get('/fitness-status/checkFitnessStatusExists','Web\SuperAdmin\FitnessStatusController@checkFitnessStatusExists')->name('superadmin.checkFitnessStatusExists');
    # Fitness status route code end here

    # Settings route code start here
    Route::get('/settings/edit-profile','Web\SuperAdmin\SettingsController@editProfile')->name('superadmin.edit-profile');
    Route::put('/settings/save-profile','Web\SuperAdmin\SettingsController@updateProfile')->name('superadmin.updateProfile');
    Route::get('/settings/change-password','Web\SuperAdmin\SettingsController@changePassword')->name('superadmin.changePassword');
    Route::put('/settings/update-password','Web\SuperAdmin\SettingsController@updatePassword')->name('superadmin.updatePassword');


    Route::get('/settings/validateUserEmail','Web\SuperAdmin\SettingsController@validateUserEmail')->name('superadmin.validateUserEmail');
    Route::get('/settings/validateUserPhone','Web\SuperAdmin\SettingsController@validateUserPhone')->name('superadmin.validateUserPhone');

    Route::post('/settings/getState', 'Web\SuperAdmin\SettingsController@getState');
    Route::post('/settings/getCity', 'Web\SuperAdmin\SettingsController@getCity');
    Route::post('/settings/getUsStateAndCity', 'Web\SuperAdmin\SettingsController@getUsStateAndCity');

    Route::get('/get-users','Web\Common\CommonController@getUsers')->name('business.getUsers');
    Route::get('/get-hash-tags','Web\Common\CommonController@getHashTags')->name('business.hashTags');
    # Settings route code end here

    Route::get('/settings/getCityList/{query}', 'Web\Business\SettingsController@getCityList');
    Route::post('/settings/getCityData', 'Web\Business\SettingsController@getCityData');

    # Get-Fit: Workout type route code start here
    Route::get('/get-fit/workout-type','Web\SuperAdmin\WorkoutTypeController@index')->name('workout-types');
    Route::get('/get-fit/workout-type/add','Web\SuperAdmin\WorkoutTypeController@create')->name('create-workout-type');
    Route::post('/get-fit/workout-type/add','Web\SuperAdmin\WorkoutTypeController@store')->name('add-workout-type');
    Route::get('/get-fit/workout-type/edit/{id}','Web\SuperAdmin\WorkoutTypeController@edit')->name('edit-workout-type');
    Route::post('/get-fit/workout-type/edit/{id}','Web\SuperAdmin\WorkoutTypeController@update')->name('edit-workout-type');
    Route::delete('/get-fit/workout-type/destroy/{id}', 'Web\SuperAdmin\WorkoutTypeController@destroy')->name('delete-workout-type');
    Route::post('/get-fit/workout-type/view', 'Web\SuperAdmin\WorkoutTypeController@show')->name('view-workout-type');


    # Get-Fit: Equipment route code start here
    Route::get('/get-fit/equipment','Web\SuperAdmin\EquipmentController@index')->name('equipment');
    Route::get('/get-fit/equipment/add','Web\SuperAdmin\EquipmentController@create')->name('create-equipment');
    Route::post('/get-fit/equipment/add','Web\SuperAdmin\EquipmentController@store')->name('add-equipment');
    Route::get('/get-fit/equipment/edit/{id}','Web\SuperAdmin\EquipmentController@edit')->name('edit-equipment');
    Route::post('/get-fit/equipment/edit/{id}','Web\SuperAdmin\EquipmentController@update')->name('edit-equipment');
    Route::delete('/get-fit/equipment/destroy/{id}', 'Web\SuperAdmin\EquipmentController@destroy')->name('delete-equipment');
    Route::post('/get-fit/equipment/view', 'Web\SuperAdmin\EquipmentController@show')->name('view-equipment');

    # Get-Fit: Plan goal route code start here
    Route::get('/get-fit/plan-goal','Web\SuperAdmin\PlanGoalsController@index')->name('plan-goals');
    Route::get('/get-fit/plan-goal/add','Web\SuperAdmin\PlanGoalsController@create')->name('create-plan-goal');
    Route::post('/get-fit/plan-goal/add','Web\SuperAdmin\PlanGoalsController@store')->name('add-plan-goal');
    Route::get('/get-fit/plan-goal/edit/{id}','Web\SuperAdmin\PlanGoalsController@edit')->name('edit-plan-goal');
    Route::post('/get-fit/plan-goal/edit/{id}','Web\SuperAdmin\PlanGoalsController@update')->name('edit-plan-goal');
    Route::delete('/get-fit/plan-goal/destroy/{id}', 'Web\SuperAdmin\PlanGoalsController@destroy')->name('delete-plan-goal');
    Route::post('/get-fit/plan-goal/view', 'Web\SuperAdmin\PlanGoalsController@show')->name('view-plan-goal');

    # Get-Fit: Plan sport route code start here
    Route::get('/get-fit/plan-sport','Web\SuperAdmin\PlanSportController@index')->name('plan-sports');
    Route::get('/get-fit/plan-sport/add','Web\SuperAdmin\PlanSportController@create')->name('create-plan-sport');
    Route::post('/get-fit/plan-sport/add','Web\SuperAdmin\PlanSportController@store')->name('add-plan-sport');
    Route::get('/get-fit/plan-sport/edit/{id}','Web\SuperAdmin\PlanSportController@edit')->name('edit-plan-sport');
    Route::post('/get-fit/plan-sport/edit/{id}','Web\SuperAdmin\PlanSportController@update')->name('edit-plan-sport');
    Route::delete('/get-fit/plan-sport/destroy/{id}', 'Web\SuperAdmin\PlanSportController@destroy')->name('delete-plan-sport');
    Route::post('/get-fit/plan-sport/view', 'Web\SuperAdmin\PlanSportController@show')->name('view-plan-sport');

    // Route::get('/get-fit/on-demand-services','Web\SuperAdmin\OnDemandServiceController@index')->name('on-demand-services');
    // Route::get('/get-fit/on-demand-services/add','Web\SuperAdmin\OnDemandServiceController@create')->name('create-on-demand-service');
    // Route::post('/get-fit/on-demand-services/add','Web\SuperAdmin\OnDemandServiceController@store')->name('add-on-demand-service');
    // Route::get('/get-fit/on-demand-services/edit/{id}','Web\SuperAdmin\OnDemandServiceController@edit')->name('edit-on-demand-service');
    // Route::post('/get-fit/on-demand-services/edit/{id}','Web\SuperAdmin\OnDemandServiceController@update')->name('edit-on-demand-service');
    // Route::delete('/get-fit/on-demand-services/destroy/{id}', 'Web\SuperAdmin\OnDemandServiceController@destroy')->name('delete-on-demand-service');
    // Route::post('/get-fit/on-demand-services/view', 'Web\SuperAdmin\OnDemandServiceController@show')->name('view-on-demand-service');
});

Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin'], function () {
	Route::get('/dashboard','Web\Admin\AdminController@index')->name('admin.dashboard');

    # Settings user route code end here
    Route::get('/settings/edit-profile','Web\Admin\SettingsController@editProfile')->name('admin.edit-profile');
    Route::put('/settings/save-profile','Web\Admin\SettingsController@updateProfile')->name('admin.updateProfile');
    Route::get('/settings/change-password','Web\Admin\SettingsController@changePassword')->name('admin.changePassword');
    Route::put('/settings/update-password','Web\Admin\SettingsController@updatePassword')->name('admin.updatePassword');
    Route::get('/settings/validateUserEmail','Web\Admin\SettingsController@validateUserEmail')->name('admin.validateUserEmail');
    Route::get('/settings/validateUserPhone','Web\Admin\SettingsController@validateUserPhone')->name('admin.validateUserPhone');
    # Settings user route code end here

});

Route::group(['middleware' => ['auth', 'business'], 'prefix' => 'business'], function () {
	Route::get('/dashboard','Web\Business\BusinessController@index')->name('business.dashboard');

    # Employee user route code start here
    Route::get('/users/employee-users','Web\Business\EmployeeUserController@index')->name('employee-users');
    Route::get('/users/employee-users/add', 'Web\Business\EmployeeUserController@add')->name('add-employee-user');
    Route::post('/users/employee-users/store', 'Web\Business\EmployeeUserController@store')->name('store-employee-user');
    Route::get('/users/employee-users/edit/{id}','Web\Business\EmployeeUserController@editEmployeeUser')->name('edit-employee-user');
    Route::put('/users/employee-users/save','Web\Business\EmployeeUserController@updateEmployeeUser')->name('update-employee-user');
    Route::delete('/users/employee-users/destroy/{id}', 'Web\Business\EmployeeUserController@destroy')->name('delete-employee-user');
    Route::post('/users/employee-users/view', 'Web\Business\EmployeeUserController@view');
    Route::put('/users/employee-users/changeStatus', 'Web\Business\EmployeeUserController@changeStatus')->name('status-employee-user');
    # Employee user route code end here

    # Post route code start here
    Route::get('/posts','Web\Business\PostsController@index')->name('business.posts');
    Route::get('/posts/add', 'Web\Business\PostsController@add')->name('business.add-post');
    Route::post('/posts/store', 'Web\Business\PostsController@store')->name('business.store-post');
    Route::get('/posts/edit/{id}','Web\Business\PostsController@editPost')->name('business.edit-post');
    Route::put('/posts/save','Web\Business\PostsController@updatePost')->name('business.update-post');
    Route::delete('/posts/destroy/{id}', 'Web\Business\PostsController@destroy')->name('business.delete-post');
    Route::post('/posts/view', 'Web\Business\PostsController@view');
    Route::post('/posts/view-post-likes', 'Web\Business\PostsController@viewPostLikes');
    Route::get('/posts/checkPostExists','Web\Business\PostsController@checkPostExists')->name('business.checkPostExists');
    Route::get('/posts/archive-posts','Web\Business\PostsController@archivePosts')->name('business.archive-posts');
    Route::delete('/posts/force-delete/{id}', 'Web\Business\PostsController@forceDelete')->name('business.force-delete-post');
    Route::post('/posts/archive-view', 'Web\Business\PostsController@viewArchive');
    Route::put('/posts/restore/{id}', 'Web\Business\PostsController@restorePost')->name('business.restore-post');

    Route::post('/posts/view-post-flagged', 'Web\Business\FlaggedPostController@viewPostFlagged');
    Route::put('/posts/flagged/changeStatus', 'Web\Business\FlaggedPostController@changeStatus')->name('business.changePostStatus');
    # Post route code end here

    # Post comment route code start here
    Route::get('/posts/comments/{postId}','Web\Business\PostCommentController@index')->name('business.posts_comments');
    Route::post('/posts/comments/reply','Web\Business\PostCommentController@commentReplyForm')->name('business.posts_commentReply');
    Route::post('/posts/comments/add','Web\Business\PostCommentController@addComment')->name('business.posts_addComment');
    Route::post('/posts/comment/saveComment','Web\Business\PostCommentController@saveComment')->name('business.posts_saveComment');
    Route::post('/posts/comment/commentReply','Web\Business\PostCommentController@saveCommentReply')->name('business.posts_commentReply');
    Route::post('/posts/comments/view', 'Web\Business\PostCommentController@view');
    Route::post('/posts/comment/view-comment-upvotes', 'Web\Business\PostCommentController@viewCommentUpVotes');
    Route::post('/posts/comment/view-comment-downvotes', 'Web\Business\PostCommentController@viewCommentDownVotes');
    Route::get('/posts/comment/flagged','Web\Business\FlaggedCommentController@index')->name('business.flagged-comments');
    Route::post('/posts/comment/view-comment-flagged', 'Web\Business\FlaggedCommentController@viewCommentFlagged');
    Route::put('/posts/comment/flagged/changeStatus', 'Web\Business\FlaggedCommentController@changeStatus')->name('business.changeCommentStatus');
    # Post comment route code end here

    # Product route code end here
    Route::get('/products','Web\Business\ProductsController@index')->name('business.products');
    Route::get('/products/add','Web\Business\ProductsController@add')->name('business.add_products');
    Route::post('/products/store', 'Web\Business\ProductsController@store')->name('business.store_products');
    Route::get('/products/checkProductExists','Web\Business\ProductsController@checkProductExists')->name('business.checkProductExists');
    Route::get('/products/checkProductSkuExists','Web\Business\ProductsController@checkProductSkuExists')->name('business.checkProductSkuExists');
    Route::get('/products/edit/{id}','Web\Business\ProductsController@editProduct')->name('business.edit-product');
    Route::put('/products/save','Web\Business\ProductsController@updateProduct')->name('business.update-product');
    Route::delete('/products/destroy/{id}', 'Web\Business\ProductsController@destroy')->name('business.delete-product');
    Route::post('/products/view', 'Web\Business\ProductsController@view');
    #Route::get('/products/add-gallery/{productId}','Web\Business\ProductsController@addGallery')->name('business.add_gallery');
    #Route::post('/products/gallery/save-gallery','Web\Business\ProductsController@saveGallery')->name('business.save-gallery');
    #Route::post('/products/delete-product-gallery', 'Web\Business\ProductsController@deleteProductGallery')->name('business.delete-product-gallery');
    Route::get('/products/archive-products','Web\Business\ProductsController@archiveProducts')->name('business.archive-products');
    Route::post('/products/archive-view', 'Web\Business\ProductsController@viewArchive');
    Route::put('/products/restore/{id}', 'Web\Business\ProductsController@restoreProduct')->name('business.restore-products');
    Route::delete('/products/force-delete/{id}', 'Web\Business\ProductsController@forceDelete')->name('business.force-delete-product');
    # Product route code end here

    # Import product route code start here
    Route::get('/products/import-products','Web\Business\ImportProductsController@importProducts')->name('business.product-import');
    Route::post('/products/upload-csv','Web\Business\ImportProductsController@uploadCSV')->name('business.upload-csv');
    Route::post('/products/import-csv-data','Web\Business\ImportProductsController@importCsvData')->name('business.import-csv-data');
    Route::get('/products/total-products','Web\Business\ImportProductsController@totalProducts')->name('business.total-products');
    # Import product route code end here

    # Product category route code start here
    Route::get('/products/product-category','Web\Business\ProductCategoryController@index')->name('business.product-category');
    Route::get('/products/product-category/add', 'Web\Business\ProductCategoryController@add')->name('business.add-product-category');
    Route::post('/products/product-category/store', 'Web\Business\ProductCategoryController@store')->name('business.store-product-category');
    Route::get('/products/product-category/edit/{id}','Web\Business\ProductCategoryController@editProductCategory')->name('business.edit-product-category');
    Route::put('/products/product-category/save','Web\Business\ProductCategoryController@updateProductCategory')->name('business.update-product-category');
    Route::delete('/products/product-category/destroy/{id}', 'Web\Business\ProductCategoryController@destroy')->name('business.delete-product-category');
    Route::post('/products/product-category/view', 'Web\Business\ProductCategoryController@view');
    Route::get('/products/product-category/checkProductCategoryExists','Web\Business\ProductCategoryController@checkProductCategoryExists')->name('business.checkProductCategoryExists');
    # Product category route code end here

    Route::get('/orders','Web\Business\OrdersController@index')->name('orders');

    Route::get('/settings/edit-profile','Web\Business\SettingsController@editProfile')->name('business.edit-profile');
    Route::put('/settings/save-profile','Web\Business\SettingsController@updateProfile')->name('business.updateProfile');

    Route::get('/settings/change-password','Web\Business\SettingsController@changePassword')->name('business.changePassword');
    Route::put('/settings/update-password','Web\Business\SettingsController@updatePassword')->name('business.updatePassword');

    Route::get('/settings/validateUserEmail','Web\Business\SettingsController@validateUserEmail')->name('business.validateUserEmail');
    Route::get('/settings/validateUserPhone','Web\Business\SettingsController@validateUserPhone')->name('business.validateUserPhone');

    Route::post('/settings/getState', 'Web\Business\SettingsController@getState');
    Route::post('/settings/getCity', 'Web\Business\SettingsController@getCity');
    Route::post('/settings/getUsStateAndCity', 'Web\Business\SettingsController@getUsStateAndCity');

    Route::get('/settings/getCityList/{query}', 'Web\Business\SettingsController@getCityList');
    Route::post('/settings/getCityData', 'Web\Business\SettingsController@getCityData');

    Route::get('/get-users','Web\Common\CommonController@getUsers')->name('business.getUsers');
    Route::get('/get-hash-tags','Web\Common\CommonController@getHashTags')->name('business.hashTags');

    // Getfit: Exercise routes code start
    Route::get('/get-fit/exercises','Web\Business\ExerciseController@index')->name('business.exercises');
    Route::get('/get-fit/exercises/add','Web\Business\ExerciseController@create')->name('business.create-exercise');
    Route::post('/get-fit/exercises/add','Web\Business\ExerciseController@saveExercise')->name('business.add-exercise');
    Route::get('/get-fit/exercises/edit/{id}','Web\Business\ExerciseController@edit')->name('business.edit-exercise');
    Route::post('/get-fit/exercises/edit/{id}','Web\Business\ExerciseController@update')->name('business.edit-exercise');
    Route::delete('/get-fit/exercises/destroy/{id}', 'Web\Business\ExerciseController@removeExercise')->name('business.delete-exercise');
    Route::post('/get-fit/exercises/view', 'Web\Business\ExerciseController@viewExercise')->name('business.view-exercise');

    // Getfit: Workout routes code start
    Route::get('/get-fit/workout','Web\Business\WorkoutController@index')->name('business.workout');
    Route::get('/get-fit/workout/add','Web\Business\WorkoutController@create')->name('business.create-workout');
    Route::post('/get-fit/workout/add','Web\Business\WorkoutController@store')->name('business.add-workout');
    Route::get('/get-fit/workout/edit/{id}','Web\Business\WorkoutController@edit')->name('business.edit-workout');
    Route::post('/get-fit/workout/edit/{id}','Web\Business\WorkoutController@update')->name('business.update-workout');
    Route::post('/get-fit/workout/view', 'Web\Business\WorkoutController@show')->name('business.view-workout');
    Route::delete('/get-fit/workout/destroy/{id}', 'Web\Business\WorkoutController@destroy')->name('business.delete-workout');
    Route::post('/get-fit/workout/create-exercise', 'Web\Business\WorkoutController@createExcercise')->name('business.workout.create-exercise');
    Route::post('/get-fit/workout/add-exercise', 'Web\Business\WorkoutController@saveExcercise')->name('business.workout.add-exercise');

    // Getfit: Workout Plan routes code start
    Route::get('/get-fit/workout-plan','Web\Business\WorkoutPlanController@index')->name('business.workout-plan');
    Route::get('/get-fit/workout-plan/add','Web\Business\WorkoutPlanController@create')->name('business.create-workout-plan');
    Route::get('/get-fit/workout-plan/edit/{id}','Web\Business\WorkoutPlanController@edit')->name('business.edit-workout-plan');
    Route::delete('/get-fit/workout-plan/destroy/{id}', 'Web\Business\WorkoutPlanController@removeWorkout')->name('business.delete-workout-plan');
    Route::post('/get-fit/workout-plan/view', 'Web\Business\WorkoutPlanController@show')->name('business.view-workout-plan');
});


// Route::group(['middleware' => ['auth', 'employee'], 'prefix' => 'employee'], function () {
// 	Route::get('/dashboard','Web\Employee\EmployeeController@index')->name('employee.dashboard');
// });

// Route::group(['middleware' => ['auth', 'consumer'], 'prefix' => 'consumer'], function () {
// 	Route::get('/dashboard','Web\Consumer\ConsumerController@index')->name('consumer.dashboard');
// });