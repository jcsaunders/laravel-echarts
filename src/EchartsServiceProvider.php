<?php

namespace Codebarista\LaravelEcharts;

use Codebarista\LaravelEcharts\Console\Commands\NodeEcharts;
use Illuminate\Support\ServiceProvider;

class EchartsServiceProvider extends ServiceProvider
{
    private const string CONFIG = __DIR__.'/../config/echarts.php';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG, 'echarts');

        $this->commands([
            NodeEcharts::class,
        ]);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG => $this->app->configPath('echarts.php'),
            ], 'config');
        }
    }
}
