<?php
use Illuminate\Http\Request;

Route::get('articles', 'api\ArticlesController@index'); //show all
Route::get('articles/tag/{tag}', ['uses' =>'api\ArticlesController@tag']); //show all base on tags
Route::get('article/{id}', 'api\ArticlesController@show'); //show id only


//Modifying articles middleware
Route::group([
    'middleware' => 'auth:api'
  ], function() {
      Route::get('article/{id}/edit', 'api\ArticlesController@edit'); //show id only w/out increment
      Route::post('article', 'api\ArticlesController@store')->middleware('auth:api'); //create article
      // Route::put('article', 'api\ArticlesController@store')->middleware('auth:api'); //edit article
      Route::post('article', 'api\ArticlesController@store')->middleware('auth:api'); //edit article
      Route::delete('article/{id}', 'api\ArticlesController@destroy')->middleware('auth:api'); //delete article
  });


  //Comments middleware
  Route::group([
    'middleware' => 'auth:api'
  ], function() {
    Route::post('article/{id}/comment', 'api\CommentsController@store')->middleware('auth:api'); //post comments
    Route::get('article/{id}/comments', 'api\ArticlesController@comments')->middleware('auth:api'); //post comments
    Route::post('article/comment/edit/{id}', 'api\CommentsController@update')->middleware('auth:api'); //edit comments
    Route::delete('article/comment/delete/{id}', 'api\CommentsController@delete')->middleware('auth:api'); //DELETE comments
    //Route::get('article/comment/show/all/debug', 'api\CommentsController@index')->middleware('auth:api'); //Testing purposes
  });


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::put('update', 'AuthController@update');
    });
});