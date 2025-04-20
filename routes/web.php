<?php

use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/reports/{type}/pdf', [ReportController::class, 'generatePDF']);
Route::get('/reports/{type}/excel', [ReportController::class, 'generateExcel']);
