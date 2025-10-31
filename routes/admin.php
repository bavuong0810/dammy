<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ManagerController;
use Illuminate\Support\Facades\Artisan;
use App\Constants\BaseConstants;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\RequestChangeUserTypeController;
use App\Http\Controllers\Admin\WithdrawRequestController;
use App\Http\Controllers\Admin\ReviewController;
use Illuminate\Support\Facades\Redis;

// Route xử lý cho admin
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('admin.login');
    Route::post('/login', 'adminLogin');
    Route::get('/logout', 'logout')->name('admin.logout');
});

Route::group(['middleware' => ['admin']], function () {
    Route::get('/add-view', function() {
        $userViews = Redis::hgetall('UserViews');
        if (!empty($userViews)) {
            print_r($userViews);
        }
    })
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION)
        ->name('admin.add-view');

    Route::get('/clear-cache', function() {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return redirect()->route('admin.dashboard')->with(['success' => 'Đã xoá cache trên trang.']);
    })
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION)
        ->name('admin.clearCache');

    Route::get('/', [HomeController::class, 'index'])->name('admin.dashboard');
    Route::get('/change-user-types', [HomeController::class, 'changeUserType'])->name('admin.changeUserType');
    //update information
    Route::get('/account-information', [ManagerController::class, 'accountInformation'])
        ->name('admin.accountInformation');
    Route::post('/store-account-information', [ManagerController::class, 'storeAccountInformation'])
        ->name('admin.storeAccountInformation');

    //change password
    Route::get('/change-password', [AdminController::class, 'changePassword'])
        ->name('admin.changePassword');
    Route::post('/change-password', [AdminController::class, 'storePassword'])
        ->name('admin.storePassword');

    //ajax delete
    Route::post('/delete-id', [AjaxController::class, 'ajax_delete'])
        ->name('admin.ajax_delete');

    //Setting
    Route::get('/theme-option', [AdminController::class, 'getThemeOption'])
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION)
        ->name('admin.themeOption');
    Route::post('/theme-option', [AdminController::class, 'storeThemeOption'])
        ->middleware('role:super-admin,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.storeThemeOption');
    Route::get('/menu', [AdminController::class, 'getMenu'])
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION)
        ->name('admin.menu');

    //Page
    Route::get('/list-pages', [PageController::class, 'listPage'])
        ->middleware('role:page-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.pages');
    Route::get('/page/create', [PageController::class, 'createPage'])
        ->middleware('role:page-management,' . BaseConstants::CREATE_PERMISSION)
        ->name('admin.createPage');
    Route::get('/edit-page/{id}', [PageController::class, 'pageDetail'])
        ->middleware('role:page-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.pageDetail');
    Route::post('/page/store', [PageController::class, 'storePage'])
        ->middleware('role:page-management,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.storePage');

    //Story
    Route::get('/stories', [StoryController::class, 'index'])
        ->middleware('role:story-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.story.index');
    Route::get('/story/export', [StoryController::class, 'export'])
        ->name('admin.story.export')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    Route::get('/story/{id}', [StoryController::class, 'detail'])
        ->middleware('role:story-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.story.detail');
    Route::post('/story/store', [StoryController::class, 'store'])
        ->middleware('role:story-management,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.story.store');
    Route::post('/story/transfer', [StoryController::class, 'transfer'])
        ->middleware('role:story-management,' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.story.transfer');
    Route::post('/story/hide', [StoryController::class, 'hide'])
        ->middleware('role:story-management,' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.story.hide');



    //Chapter Comics
    Route::get('/chapter/{story_id}', [ChapterController::class, 'index'])
        ->middleware('role:story-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.chapter.index');
    Route::get('/chapter/{story_id}/{id}', [ChapterController::class, 'detail'])
        ->middleware('role:story-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.chapter.detail');
    Route::post('/chapter/store', [ChapterController::class, 'store'])
        ->middleware('role:story-management,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.chapter.store');

    //Comics Categories
    Route::get('/categories', [CategoryController::class, 'index'])
        ->middleware('role:category-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])
        ->middleware('role:category-management,' . BaseConstants::CREATE_PERMISSION)
        ->name('admin.category.create');
    Route::get('/category/{id}', [CategoryController::class, 'detail'])
        ->middleware('role:category-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.category.detail');
    Route::post('/category/store', [CategoryController::class, 'store'])
        ->middleware('role:category-management,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.category.store');

    //Payment
    Route::get('/payments', [PaymentController::class, 'index'])
        ->middleware('role:payment-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.payment.index');
    Route::get('/payment/{id}', [PaymentController::class, 'detail'])
        ->middleware('role:payment-management,' . BaseConstants::READ_PERMISSION)
        ->name('admin.payment.detail');
    Route::post('/payment/cancel', [PaymentController::class, 'cancel'])
        ->middleware('role:payment-management,' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.payment.cancel');
    Route::post('/payment/confirm', [PaymentController::class, 'confirm'])
        ->middleware('role:payment-management,' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.payment.confirm');

    //Withdraw Request
    Route::get('/withdraw-requests', [WithdrawRequestController::class, 'index'])
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION)
        ->name('admin.withdrawRequest.index');
    Route::get('/withdraw-request/{id}', [WithdrawRequestController::class, 'detail'])
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION)
        ->name('admin.withdrawRequest.detail');
    Route::post('/withdraw-request/cancel', [WithdrawRequestController::class, 'cancel'])
        ->middleware('role:super-admin,' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.withdrawRequest.cancel');
    Route::post('/withdraw-request/confirm', [WithdrawRequestController::class, 'confirm'])
        ->middleware('role:super-admin,' . BaseConstants::UPDATE_PERMISSION)
        ->name('admin.withdrawRequest.confirm');

    //Report
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('admin.report.index')
        ->middleware('role:report-management,' . BaseConstants::READ_PERMISSION);
    Route::get('/report/{id}', [ReportController::class, 'detail'])
        ->name('admin.report.detail')
        ->middleware('role:report-management,' . BaseConstants::READ_PERMISSION);
    Route::post('/report/confirm', [ReportController::class, 'confirm'])
        ->name('admin.report.confirm')
        ->middleware('role:report-management,' . BaseConstants::UPDATE_PERMISSION);
    Route::post('/report/cancel', [ReportController::class, 'cancel'])
        ->name('admin.report.cancel')
        ->middleware('role:report-management,' . BaseConstants::UPDATE_PERMISSION);

    //Review
    Route::get('/reviews', [ReviewController::class, 'index'])
        ->name('admin.review.index')
        ->middleware('role:review-management,' . BaseConstants::READ_PERMISSION);
    Route::get('/review/{id}', [ReviewController::class, 'detail'])
        ->name('admin.review.detail')
        ->middleware('role:review-management,' . BaseConstants::READ_PERMISSION);
    Route::post('/review/quick-update', [ReviewController::class, 'quickAction'])
        ->name('admin.review.quickAction')
        ->middleware('role:review-management,' . BaseConstants::UPDATE_PERMISSION);

    //Request Change User Type
    Route::get('request-change-user-types', [RequestChangeUserTypeController::class, 'index'])
        ->name('admin.requestChangeUserType.index')
        ->middleware('role:request-change-user-type,' . BaseConstants::READ_PERMISSION);
    Route::post('request-change-user-type/confirm', [RequestChangeUserTypeController::class, 'confirm'])
        ->name('admin.requestChangeUserType.confirm')
        ->middleware('role:request-change-user-type,' . BaseConstants::UPDATE_PERMISSION);
    Route::post('request-change-user-type/cancel', [RequestChangeUserTypeController::class, 'cancel'])
        ->name('admin.requestChangeUserType.cancel')
        ->middleware('role:request-change-user-type,' . BaseConstants::UPDATE_PERMISSION);

    //chat
    Route::get('/chats', [ChatController::class, 'index'])
        ->name('admin.chat.index')
        ->middleware('role:chat-management,' . BaseConstants::READ_PERMISSION);
    Route::delete('/chat/{id}', [ChatController::class, 'delete'])
        ->name('admin.chat.cancel')
        ->middleware('role:chat-management,' . BaseConstants::UPDATE_PERMISSION);

    //Users
    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.user.index')
        ->middleware('role:user-management,' . BaseConstants::READ_PERMISSION);
    Route::get('/user/{id}', [UserController::class, 'detail'])
        ->name('admin.user.detail')
        ->middleware('role:user-management,' . BaseConstants::READ_PERMISSION);
    Route::post('/user/store', [UserController::class, 'store'])
        ->name('admin.user.store')
        ->middleware('role:user-management,' . BaseConstants::UPDATE_PERMISSION);

    //Users
    Route::get('/translate-teams', [UserController::class, 'translateTeams'])
        ->name('admin.translateTeam.index')
        ->middleware('role:user-management,' . BaseConstants::READ_PERMISSION);
    Route::get('/translate-team/export', [UserController::class, 'exportTranslateTeams'])
        ->name('admin.translateTeam.export')
        ->middleware('role:user-management,' . BaseConstants::READ_PERMISSION);
    Route::post('/translate-team/reset-view', [UserController::class, 'resetView'])
        ->name('admin.translateTeam.resetView')
        ->middleware('role:user-management,' . BaseConstants::UPDATE_PERMISSION);
    Route::put('/translate-team/convert-view/{id}', [UserController::class, 'convertView'])
        ->name('admin.translateTeam.convertView')
        ->middleware('role:user-management,' . BaseConstants::UPDATE_PERMISSION);

    Route::get('/report-view-daily', [ReportController::class, 'viewDaily'])
        ->name('admin.report.viewDaily')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);

    //Manager
    Route::get('/list-manager', [ManagerController::class, 'listManagers'])
        ->name('admin.listManagers')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    Route::get('/manager/create', [ManagerController::class, 'createManager'])
        ->name('admin.createManager')
        ->middleware('role:super-admin,' . BaseConstants::CREATE_PERMISSION);
    Route::get('/manager/{id}', [ManagerController::class, 'managerDetail'])
        ->name('admin.managerDetail')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    Route::post('/manager/post', [ManagerController::class, 'postManagerDetail'])
        ->name('admin.postManagerDetail')
        ->middleware('role:super-admin,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION);

    // Module
    Route::get('/list-modules', [RoleController::class, 'listModules'])
        ->name('admin.listModules')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    Route::get('/module/create', [RoleController::class, 'createModule'])
        ->name('admin.createModule')
        ->middleware('role:super-admin,' . BaseConstants::CREATE_PERMISSION);
    Route::get('/module/{id}', [RoleController::class, 'moduleDetail'])
        ->name('admin.moduleDetail')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    Route::post('/module/store', [RoleController::class, 'storeModuleDetail'])
        ->name('admin.storeModuleDetail')
        ->middleware('role:super-admin,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION);

    // Role
    Route::get('/list-roles', [RoleController::class, 'listRoles'])
        ->name('admin.listRoles')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    Route::get('/role/create', [RoleController::class, 'createRole'])
        ->name('admin.createRole')
        ->middleware('role:super-admin,' . BaseConstants::CREATE_PERMISSION);
    Route::get('/role/{id}', [RoleController::class, 'roleDetail'])
        ->name('admin.roleDetail')
        ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    Route::post('/role/store', [RoleController::class, 'storeRoleDetail'])
        ->name('admin.storeRoleDetail')
        ->middleware('role:super-admin,' . BaseConstants::CREATE_PERMISSION . ', ' . BaseConstants::UPDATE_PERMISSION);

    //Business Settings
    Route::controller(BusinessSettingsController::class)->group(function () {
        Route::get('/general-setting', 'generalSetting')
            ->name('admin.generalSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::post('/general-setting', 'storeGeneralSetting')
            ->name('admin.storeGeneralSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::post('/store-setting-env', 'storeSettingEnv')
            ->name('admin.storeSettingEnv')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::get('/shipping-setting', 'shippingSetting')
            ->name('admin.shippingSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::post('/shipping-setting', 'storeShippingSetting')
            ->name('admin.storeShippingSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::get('/smtp-setting', 'smtpSetting')
            ->name('admin.smtpSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::post('/smtp-setting', 'storeSmtpSetting')
            ->name('admin.storeSmtpSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::get('/social-setting', 'socialSetting')
            ->name('admin.socialSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
        Route::post('/social-setting', 'storeSocialSetting')
            ->name('admin.storeSocialSetting')
            ->middleware('role:super-admin,' . BaseConstants::READ_PERMISSION);
    });
});
