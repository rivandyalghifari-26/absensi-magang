<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\FaceDescriptorController;

Route::get('/', function () {
    return view('welcome');
});


// setup wajah user login
Route::post('/app/setup-face/store', function (Request $request) {

    $intern = auth()->user()->intern;

    $intern->update([
        'face_descriptor' => json_encode(
            $request->descriptor
        ),
    ]);

    return response()->json([
        'success' => true,
    ]);

})->middleware('auth');


// hapus wajah
Route::delete('/app/setup-face/destroy', function () {

    $intern = auth()->user()->intern;

    $intern->update([
        'face_descriptor' => null,
    ]);

    return response()->json([
        'success' => true,
    ]);

})->middleware('auth');