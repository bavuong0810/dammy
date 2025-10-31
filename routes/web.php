<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\User\StoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\User\ChapterController;
use App\Http\Controllers\User\CoinController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\ReviewController;


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

Auth::routes(['verify' => true]);

//get channel info
//Route::get('telegram/updated-activity', [TelegramBotController::class, 'updatedActivity'])->name('telegram.updatedActivity');

//site map
Route::controller(SitemapController::class)->group(function () {
    Route::group(['prefix' => 'sitemap-index', 'as' => 'sitemap.'], function () {
        Route::get('/', 'home')->name('index');
        Route::get('/static.xml', 'static')->name('static');
        Route::get('/pages.xml', 'pages')->name('pages');
        Route::get('/stories.xml', 'stories')->name('stories');
        Route::get('/chapters.xml', 'chapters')->name('chapters');
        Route::get('/categories.xml', 'categories')->name('categories');
        Route::get('/authors.xml', 'authors')->name('authors');
    });
});

//social login
//Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('social.login');
//Route::get('/auth/{provide}/callback', [SocialAuthController::class, 'handleProviderCallback']);

Route::get('/quen-mat-khau', [HomeController::class, 'forgotPassword'])->name('forgotPassword');
Route::post('/quen-mat-khau', [HomeController::class, 'forgotPasswordSend'])->name('forgotPassword.sendOTP');
Route::get('dat-lai-mat-khau/{token}', [HomeController::class, 'resetPasswordView'])->name('resetPasswordView');
Route::post('dat-lai-mat-khau/{token}', [HomeController::class, 'resetPassword'])->name('resetPassword');

Route::get('/tao-tai-khoan', [UserController::class, 'register'])->name('registerUser');
Route::post('/tai-khoan/tao', [UserController::class, 'storeUser'])->name('postRegisterUser');
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'processLogin'])->name('loginUserAction');
Route::get('/active-account/{id}/{key}', [UserController::class, 'getActiveEmailCode'])
    ->name('user.getActiveEmailCode');
Route::get('/success-active', [UserController::class, 'successActiveEmail'])
    ->name('successActiveEmail');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/active-email', [UserController::class, 'activeEmail'])
        ->name('user.activeEmail');
    Route::get('/resend-active-email', [UserController::class, 'resendActiveEmail'])
        ->name('user.resendActiveEmail');
    Route::group(['middleware' => 'checkUserActiveEmail'], function () {
        Route::group(['prefix' => 'tai-khoan'], function () {
            Route::get('/', [UserController::class, 'index'])
                ->name('user.dashboard');

            Route::get('truyen-dang-duoc-quan-tam', [UserController::class, 'areBeingInterested'])
                ->middleware('needTranslateTeamFacebook')
                ->name('user.areBeingInterested');

            Route::get('thong-tin-ca-nhan', [UserController::class, 'profile'])
                ->name('user.profile');
            Route::post('thong-tin-ca-nhan', [UserController::class, 'updateProfile'])
                ->name('user.updateProfile');

            Route::get('tro-thanh-tac-gia', [UserController::class, 'requestChangeUserType'])
                ->name('user.requestChangeUserType');
            Route::post('tro-thanh-tac-gia', [UserController::class, 'requestChangeUserTypeProcess'])
                ->name('user.requestChangeUserType.process');

            Route::get('doi-mat-khau', [UserController::class, 'changePassword'])
                ->name('user.changePassword');
            Route::post('doi-mat-khau', [UserController::class, 'storeChangePassword'])
                ->name('user.storeChangePassword');

            Route::get('dang-xuat', [UserController::class, 'logoutUser'])
                ->name('user.logout');

            Route::get('dich-gia-dang-theo-doi', [UserController::class, 'following'])
                ->name('user.following');
            Route::get('truyen-da-luu', [UserController::class, 'bookmark'])
                ->name('user.bookmark');
            Route::get('lich-su-doc-truyen', [UserController::class, 'readHistories'])
                ->name('user.readHistories');

//            Route::get('nap-tien', [CoinController::class, 'recharge'])->name('user.recharge');
//            Route::post('nap-tien', [CoinController::class, 'processRecharge'])->name('user.processRecharge');
//            Route::get('nap-tien/{code}', [CoinController::class, 'waitTransfer'])->name('user.coin.waitTransfer');
//            Route::post('nap-tien/{code}/xac-nhan-chuyen-khoan', [CoinController::class, 'confirmTransfer'])->name('user.coin.confirmTransfer');

//            Route::get('lich-su-nap-tien', [CoinController::class, 'paymentHistory'])->name('user.paymentHistory');
//            Route::post('donate', [CoinController::class, 'donate'])->name('user.donate');
//            Route::post('donate-to-author', [CoinController::class, 'donateToAuthor'])->name('user.donateToAuthor');
//            Route::get('lich-su-donate', [CoinController::class, 'donateHistory'])->name('user.donateHistory');
//            Route::get('lich-su-nhan-donate', [CoinController::class, 'donatedHistory'])->name('user.donatedHistory');
            Route::get('lich-su-giao-dich', [CoinController::class, 'coinHistories'])->name('user.coinHistories');

//            Route::get('mua-vip', [UserController::class, 'buyVip'])->name('user.buyVip');
//            Route::post('mua-vip', [UserController::class, 'processBuyVip'])->name('user.processBuyVip');

            Route::group(['middleware' => 'checkUserIsTranslateTeam'], function () {
                Route::get('rut-xu', [CoinController::class, 'withdraw'])->name('user.withdraw');
                Route::post('rut-xu', [CoinController::class, 'withdrawProcess'])->name('user.withdraw.process');

                Route::get('dang-ky-de-cu', [StoryController::class, 'registerRecommendedStory'])->name('user.registerRecommendedStory');
                Route::post('dang-ky-de-cu', [StoryController::class, 'registerRecommendedStoryProcess'])->name('user.registerRecommendedStoryProcess');

                Route::get('de-cu-truyen-cua-toi', [StoryController::class, 'recommendedMyStories'])->name('user.recommendedMyStories');
                Route::post('de-cu-truyen-cua-toi', [StoryController::class, 'processRecommendedMyStories'])->name('user.processRecommendedMyStories');

                Route::get('truyen-cua-ban', [StoryController::class, 'index'])->name('user.story.index');
                Route::get('truyen/create', [StoryController::class, 'create'])
                    ->name('user.story.create')
                    ->middleware('needTranslateTeamFacebook');
                Route::get('truyen/{id}', [StoryController::class, 'detail'])->name('user.story.detail');
                Route::post('truyen/store', [StoryController::class, 'store'])
                    ->middleware('needTranslateTeamFacebook')
                    ->name('user.story.store');
                Route::post('truyen/quick-update', [StoryController::class, 'quickUpdate'])->name('user.story.quickUpdate');
                Route::delete('truyen/delete', [StoryController::class, 'delete'])->name('user.story.delete');

                Route::get('truyen/{story_id}/comments', [StoryController::class, 'comments'])
                    ->name('user.story.comments');
                Route::delete('truyen/{story_id}/comments/{cmt_id}', [StoryController::class, 'deleteComment'])
                    ->name('user.story.deleteComment');

                //Chapter
                Route::post('chapter/quick-update', [ChapterController::class, 'quickUpdate'])->name('user.chapter.quickUpdate');
                Route::get('chapter/{story_id}', [ChapterController::class, 'index'])->name('user.chapter.index');
                Route::get('chapter/{story_id}/create', [ChapterController::class, 'create'])
                    ->name('user.chapter.create')
                    ->middleware('needTranslateTeamFacebook');
                Route::get('chapter/{story_id}/bulk-posting', [ChapterController::class, 'bulkPosting'])
                    ->name('user.chapter.bulkPosting')
                    ->middleware('needTranslateTeamFacebook');
                Route::post('chapter/{story_id}/bulk-posting', [ChapterController::class, 'processBulkPosting'])
                    ->name('user.chapter.processBulkPosting')
                    ->middleware('needTranslateTeamFacebook');
                Route::get('chapter/{story_id}/{id}', [ChapterController::class, 'detail'])->name('user.chapter.detail');
                Route::post('chapter/{story_id}/store', [ChapterController::class, 'store'])
                    ->name('user.chapter.store')
                    ->middleware('needTranslateTeamFacebook');
                Route::post('ajax/request-delete-chapter', [AjaxController::class, 'deleteChapter'])->name('ajax.deleteChapter');

            });
        });

        Route::post('/ajax/bookmark', [AjaxController::class, 'bookmark'])->name('ajax.bookmark');
        Route::post('/ajax/remove-bookmark', [AjaxController::class, 'removeBookmark'])->name('ajax.removeBookmark');
        Route::post('/ajax/follow-author', [AjaxController::class, 'followAuthor'])->name('ajax.followAuthor');
        Route::post('/ajax/unfollow-author', [AjaxController::class, 'unfollowAuthor'])->name('ajax.unfollowAuthor');
        Route::post('/ajax/chat', [AjaxController::class, 'chat'])->name('ajax.chat');
        Route::post('/ajax/read-notification', [AjaxController::class, 'readNotification'])->name('ajax.readNotification');
        Route::post('/ajax/make-all-read-notification', [AjaxController::class, 'makeAllReadNotification'])->name('ajax.makeAllReadNotification');
        Route::post('/ajax/buy-chapter', [CoinController::class, 'buyChapter'])->name('ajax.buyChapter');
        Route::post('ajax/post-comment', [AjaxController::class, 'postComment'])->name('ajax.postComment');
        Route::post('ajax/error', [AjaxController::class, 'processError'])->name('ajax.processError');
        Route::post('/ajax/favourite', [AjaxController::class, 'favourite'])->name('ajax.favourite');
    });
});

Route::controller(MainController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('404', 'NotFound')->name('404');

//    Route::post('/report-license', 'reportLicense')->name('reportLicense');

//    Route::get('lien-he', 'PageLienHe')->name('lien-he');
//    Route::post('lien-he', 'storeContact')->name('storeContact');

    Route::get('truyen-moi.html', 'pageStory')->name('pageStory');
    Route::get('truyen-hoan-thanh.html', 'completedStory')->name('completedStory');
    Route::get('truyen-hot.html', 'hotStory')->name('hotStory');
    Route::get('truyen-sang-tac.html', 'creativeStory')->name('creativeStory');
    Route::get('truyen-dai.html', 'longStory')->name('longStory');
    Route::get('tim-kiem', 'search')->name('search');

    Route::get('page/{slug1}.html', 'page')
        ->name('page.detail')
        ->where('any', '(.*)\/$');

    Route::get('the-loai/{slug1}.html', 'category')
        ->name('category.list')
        ->where('any', '(.*)\/$');

    //story detail
    Route::get('{slug1}.html', 'storyDetail')
        ->name('story.detail')
        ->where('any', '(.*)\/$');
    // news detail, chapter detail
    Route::get('{slug1}/{slug2}.html', 'singleDetails')
        ->name('chapter.detail');

    Route::get('pr/get-link', 'getLinkPR')->name('pr.getLink');
});

// translate team
Route::controller(AuthorController::class)->group(function () {
    Route::get('danh-sach-nhom', 'index')
        ->name('translateTeam.index');
    Route::get('user/{id}', 'detail')
        ->name('translateTeam.detail');
});

Route::controller(AjaxController::class)->group(function () {
    Route::get('ajax/get-chapter', 'getChapters')->name('ajax.getChapters');
    Route::post('ajax/check-register', 'check_register')->name('ajax.check_register');
    Route::post('ajax/story-add-view', 'storyAddView')->name('ajax.storyAddView');
    Route::post('ajax/dark-mode', 'darkMode')->name('ajax.darkMode');
    Route::get('ajax/load-more-chat', 'loadMoreChat')->name('ajax.loadMoreChat');

    Route::get('ajax/donate-info', 'donateInfo')->name('ajax.donateInfo');

    //get user header info
    Route::get('ajax/get-user-header-info', 'getUserHeaderInfo')->name('ajax.getUserHeaderInfo');

    //comment
    Route::get('ajax/showComment', 'showComment')->name('ajax.showComment');
    Route::post('ajax/more-comments', 'moreComment')->name('ajax.moreComment');
    Route::get('ajax/autocomplete', 'search')->name('ajax.search');

    //review
    Route::get('ajax/get-reviews', 'getReviews')->name('ajax.getReviews');
    Route::get('ajax/get-review-comments', 'getReviewComments')->name('ajax.getReviewComments');
});

Route::controller(ReviewController::class)->group(function () {
    Route::get('ajax/get-reviews', 'getReviews')
        ->name('ajax.getReviews');
    Route::get('ajax/get-review-comments', 'getReviewComments')
        ->name('ajax.getReviewComments');
    Route::get('ajax/load-more-review', 'loadMoreReview')
        ->name('ajax.loadMoreReview');

    Route::group(['middleware' => 'checkUserActiveEmail'], function () {
        Route::post('ajax/sent-story-review', 'sentReview')
            ->name('user.sentReview');
        Route::post('ajax/sent-reply-review', 'sentReplyReview')
            ->name('user.sentReplyReview');
    });
});
