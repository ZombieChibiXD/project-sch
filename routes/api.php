<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('articles', 'api\ArticlesController@index');
Route::get('articles/tag/{tag}', ['uses' =>'api\SArticlesController@tag']);

Route::get('article/{id}', 'api\ArticlesController@show');

Route::post('article', 'api\ArticlesController@store');
Route::put('article', 'api\ArticlesController@store');
Route::delete('article/{id}', 'api\ArticlesController@destroy');