<?php

namespace AMoschou\Scribo\App\Classes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class MdFolder
{
    use NodeItem;

    private $fileInfo;
    public $type = 'folder';

    public function __construct($binder, $localPath)
    {
        $this->binder = $binder instanceof Binder ? $binder : new Binder($binder);

        $this->localPath = $localPath;

        $pathname = $this->getPathname();

        $this->fileInfo = new SplFileInfo($pathname);

        $this->setYamlData();

        $this->setMetadata();
    }

    public static function new($binder, $localPath)
    {
        return new MdFolder($binder, $localPath);
    }

    private function getPathname()
    {
        $path = config('scribo.paths.md'). (
            $this->localPath === ''
                ? "/{$this->binder->path}"
                : "/{$this->binder->path}/{$this->localPath}"
        );

        $pathname = is_null($this->fileInfo) ? $path : $this->fileInfo->getPathname();

        return $pathname;
    }

    public function exists()
    {
        return $this->fileInfo->isDir();
    }
    
    private function setYamlData()
    {
        $default = [
            'ord' => 0,
            'title' => $this->fileInfo->getFilename(),
            'ignore' => false,
        ];

        $yamlDataPath = $this->fileInfo->getRealPath() . '/' . config('scribo.folder_info');

        try {
            $this->yamlData = array_merge($default, Yaml::parseFile($yamlDataPath));
        } catch (\Throwable) {
            $this->yamlData = $default;
        }
    }

    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    private function setMetadata()
    {
        $this->metadata = [
            'title' => $this->yamlData('title'),
            'formattedTitle' => $this->yamlData('title'),
            'ord' => $this->yamlData('ord'),
            'localPath' => $this->localPath,
            'breadcrumbs' => $this->breadcrumbs(),
            'updateCarbon' => null,
            'editLink' => null,
            'sourceLink' => 'https://github.com/' . config("scribo.binders.{$this->binder->name}.github_repo") . "/tree/main/{$this->localPath}",
        ];
    }

    public function getHtml()
    {
        return 'THIS SHOULD NEVER HAPPEN';
    }

    public function getFlatTree($localOrderGroup = '')
    {
        $subtree = $this->getTree($localOrderGroup);

        $flatTree = [];

        $this->getFlatTreeRecursive($subtree, $flatTree);

        return $flatTree;
    }

    private function getFlatTreeRecursive($subtree, &$flatTree)
    {
        foreach ($subtree as $item) {
            $flatTree[$item->object->metadata('localPath')] = $item;

            if (! is_null($item->tree)) {
                $this->getFlatTreeRecursive($item->tree, $flatTree);
            }
        }
    }

    public function getTree($localOrderGroup = '')
    {
        $localOrder = 0;

        $disk = Storage::build([
            'driver' => 'local',
            'root' => $this->getPathname(),
        ]);

        dd('GET TREE DEBUG', $this->getPathname(), $disk);

        $ignore = ['.git', '.github', '_index.yaml'];

        $folderList = array_filter($disk->directories(), function ($el) use ($ignore) {
            return ! in_array($el, $ignore);
        });

        $fileList = array_filter($disk->files(), function ($el) use ($ignore) {
            return ! in_array($el, $ignore);
        });

        $orderedList = [];

        foreach ($folderList as $filename) {
            $localPath = $this->localPath === ''
                ? $filename
                : "{$this->localPath}/{$filename}";

            $folder = MdFolder::new($this->binder, $localPath);

            $ignore = $folder->yamlData('ignore');

            if (! $ignore) {
                $ord = $folder->metadata('ord');

                $orderedList[$ord][] = $filename;
            }
        }

        foreach ($fileList as $filename) {
            if (Str::endsWith($filename, '.md')) {
                $filenameWithoutSuffix = Str::replaceLast('.md', '', $filename);

                $localPath = $this->localPath === ''
                    ? $filenameWithoutSuffix
                    : "{$this->localPath}/{$filenameWithoutSuffix}";
                
                $readme = $this->binder->getRootFolder()->yamlData('readme');

                if ("{$localPath}.md" !== $readme) {
                    $file = MdFile::new($this->binder, $localPath);

                    $ord = $file->metadata('ord');
    
                    $orderedList[$ord][] = $filenameWithoutSuffix;    
                }
            }
        }

        ksort($orderedList);

        $orderedList = Arr::flatten($orderedList);

        $tree = [];

        foreach ($orderedList as $key => $item) {
            $localOrder++;

            $globalOrder = $localOrderGroup === ''
                ? $localOrder
                : "{$localOrderGroup}.{$localOrder}";
            
            $pathToItem = $this->getPathname() . '/' . $item;

            $localPathToItem = $this->localPath === ''
                ? $item
                : "{$this->localPath}/{$item}";
            
            if (is_file("{$pathToItem}.md")) {
                $object = MdFile::new($this->binder, $localPathToItem);
            }

            if (is_dir($pathToItem)) {
                $object = MdFolder::new($this->binder, $localPathToItem);
            }

            $treeItem = (object) [
                'item' => $item,
                'object' => $object,
                'tree' => $object->type === 'folder' ? $object->getTree($globalOrder) : null,
                'local_order' => $localOrder,
                'global_order' => $globalOrder,
                'partwise_order' => '???? FIX THIS ????',
                'depth' => count(explode('.', $globalOrder)),
                'trueDepth' => count(explode('/', $this->localPath)),
                'local_relative' => (
                    $key === array_key_first($orderedList) ? 'first' : (
                    $key === array_key_last($orderedList) ? 'last' :
                    'middle'
                ))
            ];

            $tree[$item] = $treeItem;
        }

        return $tree;
    }
}
