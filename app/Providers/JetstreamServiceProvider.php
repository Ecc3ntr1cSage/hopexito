<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(resource_path('views/vendor/jetstream/components/*.blade.php')) as $component) {
            $this->registerComponent(basename($component, '.blade.php'));
        }
    }
    protected function registerComponent(string $component)
    {
        Blade::component('vendor.jetstream.components.'.$component, 'jet-'.$component);
    }
}
