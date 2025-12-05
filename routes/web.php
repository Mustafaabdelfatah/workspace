<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounting\Models\Attachment;
use Modules\Accounting\Models\ModelAttachment;
use Modules\Core\Models\User;
use Modules\Core\Notifications\TestFcmNotification;
use Modules\Customer\Exports\AlmnabrForm\FullFormExport;
use Modules\Core\Services\ShiftService;



Route::get('/', function () {
});

Route::get('/generate/password/{password}', function ($password) {
    return Hash::make($password);
});


Route::get('/media/{encrypted_path?}', function ($encrypted_path = null) {
    // Dynamically handle path segments
    $fullPath = $encrypted_path ? implode('/', explode('/', $encrypted_path)) : '';

    try {
        return Storage::toBase64($fullPath);
    } catch (Exception $e) {
        return response()->json(['error' => 'File not found'], 404);
    }
})->where('path', '.')->name('media');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
