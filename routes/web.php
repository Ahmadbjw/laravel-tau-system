<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/trace',function(){
    $id = (string) Str::uuid();
    Log::info('Trace : route hit',['trace_id' => $id]);
    return response()->json([
        'ok' => true,
        'trace_id' => $id
    ]);
})->middleware('trace');