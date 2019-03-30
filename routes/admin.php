<?php

use Illuminate\Http\Request;

/**Public routes */
Route::namespace('Admin')->group(function(){
    Route::get('test', function(){
        return 'admin';
    });
    /**Private routes */
    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
});