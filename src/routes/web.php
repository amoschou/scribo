<?php

use AMoschou\Scribo\App\Classes\File;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use Spatie\LaravelPdf\Facades\Pdf;

$middleware = config('scribo.middleware');

Route::middleware($middleware)->group(function () {
    $binderKeys = array_keys(config('scribo.binders') ?? []);

    Route::get('{binder}', function (string $binder) {
        // Front cover of binder
    })->whereIn('binder', $binderKeys);

    Route::get('{binder}/{path}', function (string $binder, string $path) {
        $isPdf = Str::endsWith($path, '.pdf');

        $localPath = $isPdf ? Str::replaceLast('.pdf', '', $path) : $path;

        $file = new File($binder, $localpath);
    })->whereIn('binder', $binderKeys)->where('path', '.+');
});
