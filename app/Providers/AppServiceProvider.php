<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\ProjectRoad;
use App\Models\UserRole;
use App\User;
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
        $navs['projectRoadCount'] = ProjectRoad::count();
        $navs['userCount'] = User::count();
        $navs['userRoleCount'] = UserRole::count();

        View::share('navs', $navs);
    }
}
