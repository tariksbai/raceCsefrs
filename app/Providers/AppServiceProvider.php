<?php

namespace App\Providers;

use App\Models\ExternalForm;
use App\Models\InternalForm;
use App\Models\InternalFormResponse;
use App\Policies\ExternalFormPolicy;
use App\Policies\InternalFormPolicy;
use App\Policies\ResponsePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register policies
        Gate::policy(ExternalForm::class, ExternalFormPolicy::class);
        Gate::policy(InternalForm::class, InternalFormPolicy::class);
        Gate::policy(InternalFormResponse::class, ResponsePolicy::class);

        // Use Bootstrap pagination
        Paginator::useBootstrapFive();

        // Admin gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        // Custom Blade directives
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('permission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission({$permission})): ?>";
        });

        Blade::directive('endpermission', function () {
            return '<?php endif; ?>';
        });
    }
}
