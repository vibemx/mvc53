<?php

// Definir rutas para el frontend
Route::get('api/example/{id}', 'ExampleController@show');
Route::get('api/example', 'ExampleController@index');
Route::post('api/example', 'ExampleController@store');
Route::put('api/example/{id}', 'ExampleController@update');
Route::get('inicio/home', 'HomeController@index');

