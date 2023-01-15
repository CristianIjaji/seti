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
            return isset($user->getPermisosMenu('users.index')->create) ? $user->getPermisosMenu('users.index')->create : false;
        });

        Gate::define('update-user', function($user) {
            return isset($user->getPermisosMenu('users.index')->update) ? $user->getPermisosMenu('users.index')->update : false;
        });

        Gate::define('view-user', function($user) {
            return isset($user->getPermisosMenu('users.index')->view) ? $user->getPermisosMenu('users.index')->view : false;
        });
    }
}
