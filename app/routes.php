<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//-- Visual Display
Route::any('/', 						'PagesController@showIndex');
Route::any('maps', 						'PagesController@showMaps');
Route::any('map/view/{game}', 			'PagesController@prettyView');

//-- Maps
Route::any('map/create', 				'MapController@createMap');

//-- Player stuff
Route::any('player/check/{game}/{coord}/{pname?}', 		'PlayerController@checkCoord');