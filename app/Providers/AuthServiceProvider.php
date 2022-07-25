<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Gate::define('create-user', function($user) {
            return $user->getPermisosMenu('users.index')->create;
        });

        Gate::define('update-user', function($user) {
            return $user->getPermisosMenu('users.index')->update;
        });

        Gate::define('view-user', function($user) {
            return $user->getPermisosMenu('users.index')->view;
        });
    }
}
