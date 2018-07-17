<?php

namespace App\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Members\Member::class => \App\Members\Policies\MemberPolicy::class,
        \App\SelectionProcess\MemberApplication::class => \App\SelectionProcess\Policies\MemberApplicationPolicy::class,
        \App\SelectionProcess\SelectionProcess::class => \App\SelectionProcess\Policies\SelectionProcessPolicy::class,
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
    }
}
