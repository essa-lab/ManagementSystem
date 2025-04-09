<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Aquestion\OrderController;
use App\Http\Controllers\Admin\Article\ArticleController;
use App\Http\Controllers\Admin\Dashboard\ContactController;
use App\Http\Controllers\Admin\Dashboard\NavigationController;
use App\Http\Controllers\Admin\Dashboard\QuickLinksController;
use App\Http\Controllers\Admin\Dashboard\SocialFooterController;
use App\Http\Controllers\Admin\DigitalResource\DigitalResourceController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\Research\ResearchController;
use App\Http\Controllers\Admin\PatronController;
use App\Http\Controllers\Admin\PrivilageController;
use App\Http\Controllers\Admin\Resource\CirculationController;
use App\Http\Controllers\Admin\Resource\LibrarySettingController;
use App\Http\Controllers\Admin\Resource\PenaltyValueController;
use App\Http\Controllers\Admin\Resource\ResourceCopyController;
use App\Http\Controllers\Admin\Resource\ResourceSettingController;
use App\Http\Controllers\Admin\Resource\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Article\ArticleTypeController;
use App\Http\Controllers\Admin\Article\ScientificBranchesController;
use App\Http\Controllers\Admin\Article\SpecificationController;
use App\Http\Controllers\Admin\Book\BookController;
use App\Http\Controllers\Admin\Book\PoetryCollectionController;
use App\Http\Controllers\Admin\Book\PrintConditionController;
use App\Http\Controllers\Admin\Book\PrintTypeController;
use App\Http\Controllers\Admin\Book\TranslationTypeController;
use App\Http\Controllers\Admin\DigitalResource\DigitalFormatController;
use App\Http\Controllers\Admin\DigitalResource\DigitalResourceTypeController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Admin\Research\EducationLevelController;
use App\Http\Controllers\Admin\Research\ResearchTypeController;
use App\Http\Controllers\Admin\Research\ResearchFormatController;
use App\Http\Controllers\Admin\Resource\LanguageController;
use App\Http\Controllers\Admin\Resource\LibraryController;
use App\Http\Controllers\Admin\Resource\ResourceController;
use App\Http\Controllers\Admin\Resource\SourceController;
use App\Http\Controllers\Admin\Resource\SubjectController;
use App\Http\Controllers\MeilisearchController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patron\PatronController as CustomerPatronController;


Route::get('test',function(){
    $client = new \MeiliSearch\Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY')); // Update with your Meilisearch URL and API key

    $client->index('resources')->deleteAllDocuments();
});
Route::get('/meilisearch/key', [MeilisearchController::class, 'getKey']);

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

    // Book Routes
    Route::get('books', [BookController::class,'index']);
    Route::get('books/{id}', [BookController::class,'show']);

    Route::post('books', [BookController::class,'store']);
    Route::post('books/{bookId}/poetry-collection', [BookController::class,'storePoetryCollection']);
    Route::delete('poetry-collection-name/{id}', [BookController::class,'deletePoetryCollection']);

    Route::post('books/{bookId}/specific-subject', [BookController::class,'storeSpecificSubject']);
    Route::delete('specific-subject/{id}', [BookController::class,'deleteSpecificSubject']);

    Route::post('books/{bookId}/translator-type', [BookController::class,'storeTranslatorType']);
    Route::delete('translator/{id}', [BookController::class,'deleteTranslator']);

    Route::post('books/{bookId}/print-information', [BookController::class,'storePrintInformation']);

    Route::apiResource('poetry-collections', PoetryCollectionController::class);
    Route::apiResource('translation-types', TranslationTypeController::class);
    Route::apiResource('print-types', PrintTypeController::class);
    Route::apiResource('print-conditions', PrintConditionController::class);
    // Research Routes
    Route::get('researches', [ResearchController::class,'index']);
    Route::get('researches/{id}', [ResearchController::class,'show']);

    Route::post('researches', [ResearchController::class,'store']);
    Route::post('researches/{researcheId}/keywords', [ResearchController::class,'storeKeywords']);
    Route::delete('research-keyword/{id}', [ResearchController::class,'deleteResearchKeyword']);


    Route::apiResource('research-types', ResearchTypeController::class);
    Route::apiResource('research-formats', ResearchFormatController::class);
    Route::apiResource('education-levels', EducationLevelController::class);
    // Article Routes
    Route::get('articles', [ArticleController::class,'index']);
    Route::get('articles/{id}', [ArticleController::class,'show']);

    Route::post('articles', [ArticleController::class,'store']);
    Route::post('articles/{articleId}/keywords', [ArticleController::class,'storeKeywords']);
    Route::delete('article-keyword/{id}', [ArticleController::class,'deleteArticleKeyword']);

    Route::apiResource('scientific-classification', ScientificBranchesController::class);
    Route::apiResource('article-types', ArticleTypeController::class);
    Route::apiResource('article-specifications', SpecificationController::class);
    // Digital Resource Routes
    Route::get('digital-resources', [DigitalResourceController::class,'index']);
    Route::get('digital-resources/{id}', [DigitalResourceController::class,'show']);

    Route::post('digital-resources', [DigitalResourceController::class,'store']);
    Route::post('digital-resources/{digitalResourceId}/relations',[DigitalResourceController::class,'storeRelations']);
    Route::post('digital-resources/{digitalResourceId}/right', [DigitalResourceController::class,'storeRight']);
    Route::post('digital-resources/{digitalResourceId}/specific-subject', [DigitalResourceController::class,'storeSpecificSubject']);

    Route::apiResource('digital-formats', DigitalFormatController::class);
    Route::apiResource('digital-types', DigitalResourceTypeController::class);
    // Resource Routes
    Route::apiResource('resources', ResourceController::class);
    Route::post('resources/{resourceId}/subject',[ResourceController::class,'storeSubject']);
    Route::post('resources/{resourceId}/media',[ResourceController::class,'storeMedia']);
    Route::post('resources/{resourceId}/curator',[ResourceController::class,'storeCurators']);

    Route::delete('curator/{id}',[ResourceController::class,'deleteCurator']);
    // Route::post('resources/{resourceId}/curators',[ResourceController::class,'storeCurators']);

    Route::delete('media/{id}',[ResourceController::class,'deleteMedia']);
    // Route::post('resources/{resourceId}/medias',[ResourceController::class,'storeMedias']);

    Route::post('resources/{resourceId}/authors',[ResourceController::class,'storeCurators']);
    Route::delete('author/{id}',[ResourceController::class,'deleteCurator']);

    Route::get('resource/{id}/copies',[ResourceCopyController::class,'showCopyForResource']);

    Route::post('resources/{resourceId}/source',[ResourceController::class,'storeSource']);
    Route::post('resources/{resourceId}/editor',[ResourceController::class,'storeEditor']);

    Route::post('resource-setting',[ResourceSettingController::class,'store']);
    Route::put('resource-setting/{id}',[ResourceSettingController::class,'update']);
    Route::get('resource/{resourceId}/settings',[ResourceSettingController::class,'getResourceSetting']);

    Route::apiResource('languages', LanguageController::class);
    Route::apiResource('libraries', LibraryController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('sources', SourceController::class);
    Route::apiResource('resource-copies', ResourceCopyController::class);

    Route::apiResource('navigations', NavigationController::class);
    Route::apiResource('quick-links', QuickLinksController::class);
    Route::apiResource('footer-social', SocialFooterController::class);
    Route::apiResource('footer-contact', ContactController::class);

    // Admin and Auth Routes
    Route::apiResource('users', UserController::class);
    Route::post('users/update-profile', [UserController::class,'updateProfile']);
    Route::apiResource('patrons', PatronController::class);
    Route::post('patrons/suspend/{id}', [PatronController::class,'suspend']);
    Route::get('privilages', [PrivilageController::class,'index']); 
    Route::get('library-settings', [LibrarySettingController::class,'index']);
    Route::get('library-settings/{id}', [LibrarySettingController::class,'show']);
    Route::put('library-settings/{id}', [LibrarySettingController::class,'update']);

    Route::apiResource('penalty-values', PenaltyValueController::class);

    Route::post('patron-self-register', [UserController::class,'selfRegisteration']);
    Route::get('patron-self-register', [UserController::class,'getselfRegisteration']);
    Route::get('generate-resource-link', [ResourceController::class,'generateLink']);

    Route::post('file/upload', [FileController::class,'uploadFile']);

    Route::get('resource/resource-counts-per-library-language',[ResourceController::class,'resourceCountPerLibraryAndLanguage']);
    Route::get('resource/resource-counts-per-library',[ResourceController::class,'resourceCountPerLibrary']);

    Route::post('resource-check-in',[CirculationController::class,'adminCheckIn']);
    Route::post('resource-check-out',[CirculationController::class,'adminCheckOut']);
    Route::post('change-circulation-status',[CirculationController::class,'changeCirculationStatus']);
    Route::post('renew-circulation-status',[CirculationController::class,'changeCirculationRenewStatus']);

    Route::get('circulations',[CirculationController::class,'index']);
    Route::get('overdue-circulations/{id}',[CirculationController::class,'showOverdue']);

    Route::get('circulation-logs/{id}',[CirculationController::class,'logIndex']);
    Route::get('pay-penalty/{id}',[CirculationController::class,'payPenalty']);
    Route::post('waive-penalty',[CirculationController::class,'waivePenalty']);

    Route::get('/reviews',[ReviewController::class,'index']);
    Route::post('/ban-review',[ReviewController::class,'banReview']);


    Route::get('track-inventory',[CirculationController::class,"getResourceCopyCounts"]);
    // Route::get('availability-list',[ResourceCopyController::class,"showResourceCopy"]);

    Route::get('/check-patron-penalty',[CirculationController::class,'checkPenaltyForPatron']);
    Route::get('/generate-patron-penalty',[CirculationController::class,'generatePenaltyForPatron']);

    Route::get('/resource-overview',[ResourceController::class,'viewResources']);

    Route::get('top-ten',[ResourceController::class,'topTen']);

    Route::get('generate-report',[ExportController::class,'exportResources']);
    Route::get('renew-circulation-requests',[CirculationController::class,'getRenewalRequest']);

    Route::get('generate-season-report',[ExportController::class,'season']);
    Route::post('send-season-report',[ExportController::class,'sendEmailToHeads']);
    Route::get('count-per-subject',[ResourceController::class,'resourceCountPerSubject']);

    Route::get('orders',[OrderController::class,'index']);
    Route::post('orders',[OrderController::class,'store']);
    Route::get('orders/{id}',[OrderController::class,'show']);
    Route::put('orders/{id}',[OrderController::class,'changeStatus']);
    Route::post('order-items/{id}',[OrderController::class,'storeItem']);

    Route::get('count-per-subject',[ResourceController::class,'resourceCountPerSubject']);
    Route::get('count-per-subject',[ResourceController::class,'resourceCountPerSubject']);
    Route::get('generate-barcode/{id}',[ResourceCopyController::class,'getBarcode']);

    Route::get('activity-logs',[ActivityLogController::class,'index']);


    Route::get('home-banner',[HomeController::class,'getHomeConetnt']);
    Route::get('about-banner',[HomeController::class,'getAbout']);
    Route::get('about-contact',[HomeController::class,'getAboutContent']);

    Route::put('home-banner',[HomeController::class,'updateHomeConetnt']);
    Route::put('about-banner',[HomeController::class,'updateAbout']);
    Route::put('about-contact',[HomeController::class,'updateAboutContent']);

    
});

Route::middleware(['jwt.patron', LocalizationMiddleware::class])->group(function () {

    // Book Routes
    Route::get('/resource-counts',[CustomerPatronController::class,'resourceCount']);
    Route::get('/library-list',[CustomerPatronController::class,'libraryList']);

});
Route::middleware(['jwt.patron','verified', LocalizationMiddleware::class])->group(function () {

    Route::post('/request-resource',[CirculationController::class,'requestResource']);
    Route::post('/renew-resource',[CirculationController::class,'renewResource']);
    Route::get('/patron-circulation',[CirculationController::class,'circulationPatronLog']);

    Route::post('/review-resource',[ReviewController::class,'store']);
    Route::put('/review-resource/{id}',[ReviewController::class,'update']);
    Route::delete('/review-resource/{id}',[ReviewController::class,'delete']);
    Route::get('/my-review',[ReviewController::class,'show']);
    Route::get('/review/{id}',[ReviewController::class,'getOneReview']);

    Route::get('/check-penalty',[CirculationController::class,'checkPenalty']);
    
    Route::post('image/upload', [FileController::class,'uploadFile']);


});
Route::middleware([LocalizationMiddleware::class])->group(function () {

Route::get('self-register', [UserController::class,'getselfRegisteration']);
Route::get('/library-homepage',[DashboardController::class,"getLibraryData"]);
Route::get('/library-about',[DashboardController::class,"getAboutPage"]);

Route::get('/library-navigation',[DashboardController::class,"getLibraryNavigation"]);
Route::get('/review-resource',[ReviewController::class,'showReview']);

});
Route::middleware([LocalizationMiddleware::class])->get('/test-localization', function (Request $request) {
    return response()->json(['message' => __('messages.welcome')]);
});
