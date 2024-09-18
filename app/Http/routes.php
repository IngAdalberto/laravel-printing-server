<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/arroz', function () {
    return 'arroz';
});

Route::get('/', 'PrintingJobsController@print');
Route::get('/jobs', 'PrintingJobsController@jobs');
Route::get('/reprint_job/{job_id}', 'PrintingJobsController@reprint_job');
Route::get('/cancel_job/{job_id}', 'PrintingJobsController@cancel_job');
