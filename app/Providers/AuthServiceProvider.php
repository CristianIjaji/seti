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
            return in_array($user->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
        });

        Gate::define('update-user', function($user) {
            return in_array($user->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
        });

        Gate::define('view-user', function($user) {
            return in_array($user->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
        });
    }
}
