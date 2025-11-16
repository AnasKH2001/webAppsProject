<?php

use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\GovernmentEntity;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-relations', function () {
    $entity = \App\Models\GovernmentEntity::where('code', 'MOED')->first();
    dd($entity->users->toArray());
});
