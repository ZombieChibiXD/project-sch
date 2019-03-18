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

Route::get('articles', 'api\ArticlesController@index'); //show all
Route::get('articles/tag/{tag}', ['uses' =>'api\SArticlesController@tag']); //show all base on tags

Route::get('article/{id}', 'api\ArticlesController@show'); //show id only

Route::post('article', 'api\ArticlesController@store'); //create article
Route::put('article', 'api\ArticlesController@store'); //edit article
Route::delete('article/{id}', 'api\ArticlesController@destroy'); //delete article