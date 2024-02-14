<?php

namespace AMoschou\Scribo\App\Classes;

class MdNode
{
    public static function new($binder, $localPath)
    {
        $binderPath = $binder instanceof Binder ? $binder->path : $binder;

        $pathToItem = config('scribo.paths.md') . "/{$binderPath}/{$localPath}";

        $nodeItem = null;

        if (is_file("{$pathToItem}.md")) {
            $nodeItem = new MdFile($binder, $localPath);
        } elseif (is_dir($pathToItem)) {
            $nodeItem = new MdFolder($binder, $localPath);
        }

        abort_if(is_null($nodeItem) || ! $nodeItem->exists(), 404);

        return $nodeItem;
    }
}
