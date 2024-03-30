<?php

declare(strict_types=1);

namespace Codebarista\LaravelEcharts\Tests;

use Codebarista\LaravelEcharts\Actions\StoreChartImage;
use Codebarista\LaravelEcharts\EchartsServiceProvider;
use Codebarista\LaravelEssentials\EssentialsServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class PackageTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EssentialsServiceProvider::class,
            EchartsServiceProvider::class,
        ];
    }

    protected function storeChartImage(): StoreChartImage
    {
        return StoreChartImage::make()
            ->filePath(codebarista_path('storage/charts'))
            ->fileName('codebarista')
            ->width(1200)
            ->height(600);
    }
}
