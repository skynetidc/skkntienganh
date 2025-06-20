<?php

namespace Binjuhor\PayOs\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class PayOsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        if (is_plugin_active('payment')) {
            $this->setNamespace('plugins/payos')
                ->loadHelpers()
                ->loadRoutes()
                ->loadAndPublishViews()
                ->loadAndPublishTranslations()
                ->publishAssets();

            $this->app->register(HookServiceProvider::class);
        }
    }
}
