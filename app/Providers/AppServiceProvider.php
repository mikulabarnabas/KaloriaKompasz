<?php

namespace App\Providers;

use App\Models\FoodDiaryFoodPivot;
use App\Models\WorkoutDiaryPivot;
use Illuminate\Support\ServiceProvider;
use App\Models\Foods;
use App\Observers\FoodDiaryPivotObserver;
use App\Observers\FoodsObserver;
use App\Observers\WorkoutDiaryPivotObserver;

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
        FoodDiaryFoodPivot::observe(FoodDiaryPivotObserver::class);
        WorkoutDiaryPivot::observe(WorkoutDiaryPivotObserver::class);
        Foods::observe(FoodsObserver::class);
    }
}
