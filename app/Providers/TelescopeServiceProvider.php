<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');
        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return true;
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                'abdullah@almnabr.com',
                'maabdelgawad@almnabr.com',
                'mohamedalaa80000@gmail.com',
                'raja@almnabr.com'
            ]);
        });
    }

    public function boot()
    {
        $this->registerCustomJsDirective();

        parent::boot();
    }

    protected function registerCustomJsDirective()
    {
        Blade::directive('telescopeScripts', function () {
            $jsPath = base_path('vendor/laravel/telescope/dist/app.js');

            if (!file_exists($jsPath)) {
                throw new \RuntimeException('Unable to load the Telescope dashboard JavaScript.');
            }

            $js = file_get_contents($jsPath);
            $path = config('telescope.path');
            if (env('APP_ON_SERVER')) {
                $path = 'api/telescope';
            }
            return new HtmlString(<<<HTML
                    <script type="module">
                        window.Telescope = {'path': '{$path}'};
                        {$js}
                    </script>
                HTML);
        });
    }
}
