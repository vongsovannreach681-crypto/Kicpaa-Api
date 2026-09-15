<?php

use App\Http\Controllers\AddLinkController;
use App\Http\Controllers\AddThemeController;
use App\Http\Controllers\DesignSettingController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\MySettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('add-links/reorder', [AddLinkController::class, 'reorder']);
Route::post('add-links/{addLink}/view', [AddLinkController::class, 'recordView']);
Route::apiResource('add-links', AddLinkController::class);
Route::apiResource('addThemes', AddThemeController::class);
Route::get('design-settings', [DesignSettingController::class, 'show']);
Route::get('design-options', [DesignSettingController::class, 'options']);
Route::patch('design-settings', [DesignSettingController::class, 'update']);


Route::apiResource('my-settings', MySettingController::class);
