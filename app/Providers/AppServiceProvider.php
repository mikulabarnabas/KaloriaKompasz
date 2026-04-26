<?php

namespace App\Providers;

use App\Models\FoodDiaryFoodPivot;
use App\Models\WorkoutDiaryPivot;
use Illuminate\Support\ServiceProvider;
use App\Models\Foods;
use App\Observers\FoodDiaryPivotObserver;
use App\Observers\FoodsObserver;
use App\Observers\WorkoutDiaryPivotObserver;
use App\Models\UserProfile;
use App\Observers\UserProfileObserver;
use Illuminate\Support\Facades\URL;

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
        UserProfile::observe(UserProfileObserver::class);
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
