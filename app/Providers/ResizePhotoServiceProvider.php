<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ResizePhotoServiceProvider extends ServiceProvider{

    public function register (){
        $this->app->bind('resizePhoto', 'App\Services\ResizePhoto');
    }
}