<?php

namespace Unusualdope\FrontLaravelEcommerce\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerViewPaths();
        $this->registerPublishableViews();
    }

    /**
     * Register view paths with priority.
     * Published views are checked first, then package default views.
     */
    protected function registerViewPaths(): void
    {
        $publishedPath = resource_path('views/vendor/ud-front-ecommerce');
        $packagePath = __DIR__.'/../../resources/views';

        // Get the current view paths
        $viewPaths = config('view.paths', [resource_path('views')]);

        // Only add published path if it exists
        if (File::exists($publishedPath)) {
            // Add published views at the beginning (highest priority)
            array_unshift($viewPaths, $publishedPath);
        }

        // Set the updated view paths
        config(['view.paths' => $viewPaths]);

        // Load package views with namespace (fallback)
        $this->loadViewsFrom($packagePath, 'front-ecommerce');
    }

    /**
     * Register views that can be published to the application.
     */
    protected function registerPublishableViews(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish front templates
            $this->publishes([
                $this->getPackageViewsPath().'/front' => resource_path('views/vendor/ud-front-ecommerce/front'),
            ], 'ud-front-ecommerce-views');

            // Publish all views (including admin)
            $this->publishes([
                $this->getPackageViewsPath() => resource_path('views/vendor/ud-front-ecommerce'),
            ], 'ud-front-ecommerce-all-views');
        }
    }

    /**
     * Get the package views path.
     */
    protected function getPackageViewsPath(): string
    {
        return __DIR__.'/../../resources/views';
    }
}
