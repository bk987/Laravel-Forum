<?php

namespace App\Providers;

use App\Category;
use App\Thread;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('*', function ($view) {
            $categories = \Cache::rememberForever('categories', function () {
                return Category::all();
            });
            $trending = Thread::trending()->take(5)->get();
            $view->with('categories', $categories)->with('trending', $trending);
        });
        
        \Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }
}
