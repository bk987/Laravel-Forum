<?php

Route::get('/', 'ThreadsController@index')->name('home');

Auth::routes(['verify' => true]);

Route::get('/categories/{category}', 'CategoriesController@show')->name('categories.show');

Route::resource('/threads', 'ThreadsController');

Route::post('/locked-threads/{thread}', 'LockedThreadsController@store')->name('locked-threads.store');
Route::delete('/locked-threads/{thread}', 'LockedThreadsController@destroy')->name('locked-threads.destroy');

Route::post('/threads/{thread}/subscriptions', 'ThreadSubscriptionsController@store')
    ->name('thread-subscriptions.store');
Route::delete('/threads/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')
    ->name('thread-subscriptions.destroy');

Route::resource('/threads/{thread}/replies', 'RepliesController');

Route::post('/threads/{thread}/replies/{reply}/favorites', 'FavoritesController@store')
    ->name('favorites.store');
Route::delete('/threads/{thread}/replies/{reply}/favorites', 'FavoritesController@destroy')
    ->name('favorites.destroy');
Route::post('/threads/{thread}/replies/{reply}/best-reply', 'BestRepliesController@store')
    ->name('best-replies.store');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index')->name('user.notifications');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy')
    ->name('user.notifications.destroy');

Route::post('api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('api.avatar.store');