<?php

use AMoschou\Scribo\App\Http\Controllers\ScriboController;
use AMoschou\Scribo\Scribo;
use Illuminate\Support\Facades\Route;

$middleware = Scribo::config_arr('scribo.default_middleware');

Route::middleware($middleware)->prefix(config('scribo.prefix') ?? null)->group(function () {
    $binderKeys = array_keys(config('scribo.binders') ?? []);

    Route::get('{binder}', [ScriboController::class, 'binderHtml'])
        ->whereIn('binder', $binderKeys)
        ->name('scribo.binder');

    Route::get('{binder}.pdf', [ScriboController::class, 'binderPdf'])
        ->whereIn('binder', $binderKeys)
        ->name('scribo.binder.pdf');

    Route::get('{binder}/contents', [ScriboController::class, 'binderContentsHtml'])
        ->whereIn('binder', $binderKeys)
        ->name('scribo.contents');

    Route::get('{binder}/contents.pdf', [ScriboController::class, 'binderContentsPdf'])
        ->whereIn('binder', $binderKeys)
        ->name('scribo.contents.pdf');
    
    Route::get('{binder}/contents/{path}', [ScriboController::class, 'binderPath'])
        ->whereIn('binder', $binderKeys)
        ->where('path', '.+')
        ->name('scribo.path');
        
    Route::get('{binder}/binder.pdf', [ScriboController::class, 'completeBinderPdf'])
        ->whereIn('binder', $binderKeys)
        ->name('scribo.complete.binder.pdf');
});
