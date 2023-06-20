<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissionImageUploadController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramAdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerExcelController;
use App\Http\Controllers\ProgramStageController;
use App\Http\Controllers\DownloadFileController;


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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


    // Route::get('submisstion', 'SubmissionController@index');

    Route::middleware('auth:api')->group(function () {
        Route::get('user', [UserController::class, 'index']);

        Route::get('submission-count-is-pending', [SubmissionImageUploadController::class, 'count_is_pending']);
        Route::get('submission-count-is-pending-level2', [SubmissionImageUploadController::class, 'count_is_pending_level2']);
        Route::get('submission-tree', [SubmissionController::class, 'submissionTree']);
        Route::get('submission-tree', [SubmissionController::class, 'submissionTree']);
        Route::get('submission', [SubmissionController::class, 'index']);
        Route::post('submission-register', [SubmissionController::class, 'store']);
        Route::patch('submission-update/{id}', [SubmissionController::class, 'update']);
        Route::delete('submission-delete/{id}', [SubmissionController::class, 'destroy']);
        Route::get('submission-list-image', [SubmissionImageUploadController::class, 'index']);
        Route::post('submission-upload-image', [SubmissionImageUploadController::class, 'store']);
        Route::patch('submission-update-image/{id}', [SubmissionImageUploadController::class, 'update']);
        Route::delete('submission-delete-image/{id}', [SubmissionImageUploadController::class, 'destroy']);
        Route::get('submission-image-page', [SubmissionImageUploadController::class, 'image_page']);
        Route::put('submission-is_approved/{id}', [SubmissionController::class, 'is_approved']);
        Route::put('submission-is_rejected/{id}', [SubmissionController::class, 'is_rejected']);
        Route::put('submission-is_approved_level2/{id}', [SubmissionController::class, 'is_approved_level2']);
        Route::put('submission-is_rejected_level2/{id}', [SubmissionController::class, 'is_rejected_level2']);

        Route::get('program', [ProgramController::class, 'index']);
        Route::get('show-join-program', [ProgramController::class, 'show_join_program']);
        Route::post('program_store', [ProgramController::class, 'store']);        
        Route::put('program_put/{id}', [ProgramController::class, 'update']);
        Route::delete('program_delete/{id}', [ProgramController::class, 'destroy']);

        Route::get('customer-page', [CustomerController::class, 'customer_page']);
        Route::get('optimize-customer', [CustomerController::class, 'optimize_customer']);
        Route::get('customer', [CustomerController::class, 'index']);
        Route::post('customer_store', [CustomerController::class, 'store']);
        Route::put('customer_put/{id}', [CustomerController::class, 'update']);
        Route::delete('customer_delete/{id}', [CustomerController::class, 'destroy']);
        Route::get('lookup-customer', [CustomerController::class, 'lookup_customer']);


        Route::get('program_admin', [ProgramAdminController::class, 'index']);
        Route::post('program_admin_store', [ProgramAdminController::class, 'store']);
        Route::patch('program_admin_patch/{id}', [ProgramAdminController::class, 'update']);
        Route::delete('program_admin_delete/{id}', [ProgramAdminController::class, 'destroy']);
        Route::post('all-approved', [SubmissionController::class, 'all_is_approved']);
        Route::post('all-approved-level2', [SubmissionController::class, 'all_is_approved_level2']);

        Route::post('customer-import-excel', [CustomerExcelController::class, 'store']);
        Route::patch('submission-image-send-date/{id}', [SubmissionImageUploadController::class, 'send_date']);

        Route::get('program-stages-open', [ProgramStageController::class, 'program_stage_open']);
        Route::get('program-stages', [ProgramStageController::class, 'index']);
        Route::post('program-stages-store', [ProgramStageController::class, 'store']);
        Route::put('program-stages-update/{id}', [ProgramStageController::class, 'update']);
        Route::delete('program-stages-delete/{id}', [ProgramStageController::class, 'destroy']);
        Route::patch('program-stages-open/{id}', [ProgramStageController::class, 'stage_open']);
        Route::patch('program-stages-close/{id}', [ProgramStageController::class, 'stage_close']);

        Route::get('submission-register-search/{page}', [SubmissionController::class, 'submission_register']);

        Route::get('submissions-export', [SubmissionController::class, 'export']);
        Route::post('download_file/{idfile}', [DownloadFileController::class, 'download_file']);

    });




    Route::prefix('/user')->group(function () {
        Route::post('/login', 'LoginController@login');
    });
