<?php

namespace App\Providers;

use App\Models\Project;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        $this->navigation();
    }


    public function navigation() {
        $navs = [];


        $navs['projectCount'] = Project::count();

        View::share('navs', $navs);
    }
}
