<?php

//Личный кабинет
Route::group(['prefix' => 'cabinet', 'as' => 'cabinet.', 'middleware' => 'auth'], function () {

    Route::get('/income/{id?}',['as' => 'icome','uses' => 'ProfileController@income']);

    Route::get('/dyntree/{parent_id}',['as'=>'dyntree','uses'=>'ProfileController@dyntree']);

});

