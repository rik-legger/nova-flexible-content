<?php

namespace Formfeed\NovaFlexibleContent;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;
use Formfeed\NovaFlexibleContent\Commands\CreateCast;
use Formfeed\NovaFlexibleContent\Commands\CreateLayout;
use Formfeed\NovaFlexibleContent\Commands\CreatePreset;
use Formfeed\NovaFlexibleContent\Commands\CreateResolver;
use Formfeed\NovaFlexibleContent\Http\Middleware\InterceptFlexibleAttributes;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addMiddleware();
      
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-flexible-content', __DIR__.'/../dist/js/field.js');
            Nova::style('nova-flexible-content', __DIR__.'/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->runningInConsole()) return;

        $this->commands([
            CreateCast::class,
            CreateLayout::class,
            CreatePreset::class,
            CreateResolver::class,
        ]);
    }
    
    /**
     * Adds required middleware for Nova requests.
     *
     * @return void
     */
    public function addMiddleware()
    {
        $router = $this->app['router'];
        
        if ($router->hasMiddlewareGroup('nova')) {
            $router->pushMiddlewareToGroup('nova', InterceptFlexibleAttributes::class);
            
            return;
        }
        
        if (! $this->app->configurationIsCached()) {
            config()->set('nova.middleware', array_merge(
                config('nova.middleware', []),
                [InterceptFlexibleAttributes::class]
            ));
        }
    }
}
