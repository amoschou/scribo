<?php

namespace AMoschou\Scribo\App\Classes;

use SplFileInfo;

class Folder
{
    private $binder;
    private $localPath;
    private $splFileInfo;

    public function __construct($binder, $localPath)
    {
        $this->binder = new Binder($binder);
        // ('default')

        $this->localPath = $localPath;
        // 'chapter-1/section-1'

        $pathname = $this->getPathname();
        // '/path/to/laravel/project/resources/vendor/scribo/src/default/chapter-1/section-1'

        $this->splFileInfo = new SplFileInfo($pathname);
    }

    public function getBinder()
    {
        return $this->binder;
    }

    private function getPathname()
    {
        return is_null($this->splFileInfo)
            ? implode('/', [config('scribo.paths.src'), $this->binder->getRoutePrefix(), "{$this->localPath}"])
            : $this->splFileInfo->getPathname();
    }
}
