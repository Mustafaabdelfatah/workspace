<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Services\StampService;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/stamp', function (Request $request) {
    return (new StampService())->generateStamp($request->title, $request->companyNameEn, $request->companyNameAr, $request->content);
});
