<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\Modules;
use App\Models\Project;
use App\Models\ProjectRoad;
use App\Models\UserRole;
use App\Models\Wallet;
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
        $navs['walletCount'] = Wallet::count();
        $navs['moduleCount'] = Modules::count();
        $navs['currencyCount'] = Currency::count();

        View::share('navs', $navs);
    }
}
