<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/cc-plant/{cast_item_name}', function($cast_item_name) {
    return App\Models\CcPlant::where('cast_item_name', $cast_item_name)->first();
});
