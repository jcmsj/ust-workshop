<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Lead;
use App\Models\LeadAssignment;
use App\Models\ReserveRequest;
use App\Models\User;
use App\Observers\ArticleObserver;
use App\Observers\LeadObserver;
use App\Observers\LeadAssignmentObserver;
use App\Observers\RegistrationObserver;
use App\Observers\ReserveRequestObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
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
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }
        Article::observe(ArticleObserver::class);
        ReserveRequest::observe(ReserveRequestObserver::class);
        Lead::observe(LeadObserver::class);
        LeadAssignment::observe(LeadAssignmentObserver::class);
        User::observe(RegistrationObserver::class);
        User::observe(\App\Observers\UserApprovalObserver::class);


        // Changing timezone: https://v2.filamentphp.com/tricks/multiple-user-timezones  (works albeit being Filament V2)
        DateTimePicker::configureUsing(fn (DateTimePicker $component) => $component->timezone(config('app.user_timezone')));
        TextColumn::configureUsing(fn (TextColumn $column) => $column->timezone(config('app.user_timezone')));
    }
}
