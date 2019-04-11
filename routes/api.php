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
Route::get('articles/tag/{tag}', ['uses' =>'api\ArticlesController@tag']); //show all base on tags

Route::get('article/{id}', 'api\ArticlesController@show'); //show id only

Route::post('article', 'api\ArticlesController@store')->middleware('auth:api'); //create article
Route::put('article', 'api\ArticlesController@store')->middleware('auth:api'); //edit article
Route::delete('article/{id}', 'api\ArticlesController@destroy')->middleware('auth:api'); //delete article



// Route::group([
//     'prefix' => 'auth'
// ], function () {
//     Route::post('login', 'AuthController@login');
//     Route::post('signup', 'AuthController@signup');
  
//     Route::group([
//       'middleware' => 'auth:api'
//     ], function() {
//         Route::get('logout', 'AuthController@logout');
//         Route::get('user', 'AuthController@user');
//     });
// });