<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CategoryExtraFieldController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\InitController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UpdateCommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ContentSearchController;
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


Route::post('sessions', [SessionController::class, 'store']);

Route::name('admin.')->middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {

    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('comments', AdminCommentController::class);
    Route::apiResource('contents', AdminContentController::class);
    Route::apiResource('settings', SettingController::class);
    Route::apiResource('media', MediaController::class)->only('store');

    Route::delete('sessions', [SessionController::class, 'destroy']);
    Route::get('init', [InitController::class, 'index']);



    Route::apiResource('categories.extra-fields', CategoryExtraFieldController::class)->only(['index', 'store']);
    Route::get('extra-fields/{extraField}', [CategoryExtraFieldController::class, 'show']);
    Route::patch('extra-fields/{extraField}', [CategoryExtraFieldController::class, 'update']);
    Route::delete('extra-fields/{extraField}', [CategoryExtraFieldController::class, 'destroy']);

    Route::patch('comments/{comment}/edit', [UpdateCommentController::class, 'update']);

//    Route::get('contents-trash', [ContentTrashController::class, 'index']);
//    Route::delete('contents/{id}/force-delete', [ContentTrashController::class, 'destroy']);
//    Route::patch('contents/{id}/restore', [ContentTrashController::class, 'update']);
});

Route::apiResource('contents', ContentController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class);
Route::get('/categories/{category}/{content}', [ContentController::class, 'show']);
Route::post('comments', [CommentController::class, 'store']);
Route::get('contents-search', [ContentSearchController::class, 'index']);




