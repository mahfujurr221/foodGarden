<?php

namespace App\Providers;

use App\views\composer\BrandOption;
use Illuminate\Support\Facades\View;
use App\views\composer\ProductOptions;
use Illuminate\Support\ServiceProvider;

class InputProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['components.product.*'], ProductOptions::class);
    }
}
