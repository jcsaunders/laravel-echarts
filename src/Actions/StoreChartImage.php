<?php

namespace Codebarista\LaravelEcharts\Actions;

use Codebarista\LaravelEcharts\Enums\MimeTypes;
use Codebarista\LaravelEcharts\Exceptions\StoreChartImageException;
use Codebarista\LaravelEssentials\Traits\Makeable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Throwable;

use function crush_png;

class StoreChartImage
{
    use Makeable;

    private string $baseOptionPath;

    private MimeTypes $mimeType;

    private string $fileName;

    private string $filePath;

    private string $fullPath;

    private bool $optimize;

    private int $width;

    private int $height;

    public function baseOptionPath(string $value): static
    {
        $this->baseOptionPath = $value;

        return $this;
    }

    public function mimeType(MimeTypes $value): static
    {
        $this->mimeType = $value;

        return $this;
    }

    public function fileName(string $value): static
    {
        $this->fileName = $value;

        return $this;
    }

    public function filePath(string $value): static
    {
        $this->filePath = $value;

        return $this;
    }

    public function optimize(bool $value): static
    {
        $this->optimize = $value;

        return $this;
    }

    public function width(int $value): static
    {
        $this->width = $value;

        return $this;
    }

    public function height(int $value): static
    {
        $this->height = $value;

        return $this;
    }

    public function __construct()
    {
        $this->baseOptionPath(codebarista_path('resources/echarts/base-option.mjs'));
        $this->filePath(config('echarts.storage.path', 'app/public/vendor/echarts'));
        $this->mimeType(config('echarts.canvas.mime_type', MimeTypes::PNG));
        $this->optimize(config('echarts.optimize.png', false));
        $this->height(config('echarts.canvas.height', 600));
        $this->width(config('echarts.canvas.width', 600));
        $this->fileName(Str::random());
    }

    /**
     * @throws StoreChartImageException|Throwable
     */
    public function handle(array $option): string
    {
        $process = Process::path(codebarista_path('tools/echarts'))
            ->run($this->getCommand($option));

        throw_if($process->failed(),
            new StoreChartImageException($process->errorOutput()));

        if ($this->optimize && $this->mimeType === MimeTypes::PNG) {
            crush_png($this->fullPath);
        }

        return $this->fullPath;
    }

    private function getCommand(array $option): array
    {
        $filePath = storage_path($this->filePath);

        File::ensureDirectoryExists($filePath);

        $fileName = $this->fileName.match ($this->mimeType) {
            MimeTypes::PDF => '.pdf',
            MimeTypes::JPG => '.jpg',
            MimeTypes::PNG => '.png',
            MimeTypes::SVG => '.svg'
        };

        $this->fullPath = $filePath.DIRECTORY_SEPARATOR.$fileName;

        return [
            'node', 'render.js',
            '--baseOptionPath', $this->baseOptionPath,
            '--option', Arr::toJson($option),
            '--type', $this->mimeType->value,
            '--path', $this->fullPath,
            '--height', $this->height,
            '--width', $this->width,
        ];
    }
}
