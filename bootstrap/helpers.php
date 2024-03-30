<?php

use Illuminate\Support\Facades\App;

if (! function_exists('codebarista_path')) {
    function codebarista_path(string $path = ''): string
    {
        return App::joinPaths(dirname(__DIR__), $path);
    }
}
