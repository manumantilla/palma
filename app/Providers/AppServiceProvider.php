<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
    public function boot(): void
    {
        // El Super Admin pasa cualquier @can / authorize() / middleware permission:
        // así no hay que darle cada permiso nuevo a mano. null = que decidan las reglas normales.
        Gate::before(fn (User $user) => $user->hasRole(User::SUPER_ADMIN) ? true : null);
    }
}
