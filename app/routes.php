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
Route::any('map/json/{game}', 			'PagesController@jsonView');
Route::any('map/latest', 				'PagesController@latestJson');

//-- Maps
Route::any('map/create', 				'MapController@createMap');

//-- Player stuff
Route::any('player/check/{game}/{coord}/{pname?}/{level}', 		'PlayerController@checkCoord');
Route::any('player/fight/{game}/{coord}/{pname?}/{level}', 		'PlayerController@fightMonster');
Route::any('player/loot/{game}/{coord}/{pname?}/{level}', 		'PlayerController@grabLoot');