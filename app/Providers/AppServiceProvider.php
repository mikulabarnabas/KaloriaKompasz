<?php

namespace App\Providers;

use App\Models\FoodDiaryFoodPivot;
use App\Observers\FoodDiaryFoodPivotObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Foods;
use App\Observers\FoodsObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FoodDiaryFoodPivot::observe(FoodDiaryFoodPivotObserver::class);
        Foods::observe(FoodsObserver::class);
    }
}
