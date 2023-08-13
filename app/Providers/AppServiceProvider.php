<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract; 
use Illuminate\Support\ServiceProvider;

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
    public function boot(GateContract $gate): void
    {
        $gate->define('update-event', function ( $user, $event) {
            return $user->id === $event->user_id;
        });

        $gate->define('delete-event', function ( $user, $event, $attendees) {
            return $user->id === $event->user_id ||
            $user->id === $attendees->user_id;
        });
    }
}
