<?php

namespace AMoschou\Scribo\App\Classes;

use Romans\Filter\IntToRoman;

trait NodeItem
{
    private $binder;
    private $localPath;
    private $yamlData;
    private $metadata;

    public function getBinder()
    {
        return $this->binder;
    }

    public function yamlData($key = null, $default = null)
    {
        return is_null($key) ? $this->yamlData : $this->yamlData[$key] ?? $default;
    }

    public function metadata($key = null)
    {
        return is_null($key) ? $this->metadata : $this->metadata[$key];
    }

    public function getLocalPath()
    {
        return $this->localPath;
    }

    public function getDepth()
    {
        return count(explode('/', $this->localPath));
    }

    public function breadcrumbs($withContents = true)
    {
        $pathArray = explode('/', $this->localPath);

        $breadcrumbs = [];

        if ($withContents) {
            $breadcrumbs[] = [
                'binder' => $this->binder->name,
                'partial-path' => '/',
                'title' => 'Contents',
            ];
        }

        for ($i = 1; $i < count($pathArray); $i++) {
            $partialPath = implode('/', array_slice($pathArray, 0, $i));

            $filename =  config('scribo.paths.md') . '/' . $partialPath;

            $breadcrumbs[] = [
                'binder' => $this->binder->name,
                'partial-path' => $partialPath,
                'title' => MdNode::new($this->binder, $partialPath)->yamlData('title'),
            ];
        }

        return $breadcrumbs;
    }

    public function href($suffix = null)
    {
        $localBinderPath = $this->localPath === ''
            ? route('scribo.binder', [
                'binder' => $this->binder->name,
            ])
            : route('scribo.path', [
                'binder' => $this->binder->name,
                'path' => $this->localPath,
            ]);

        return is_null($suffix) ? $localBinderPath : "{$localBinderPath}.{$suffix}";
    }

    public function findGlobalOrder($tmp_SPEEDTHINGSUPBYUSINGTHISBINDERTREE = null)
    {
        $tree = (object) ['tree' => $tmp_SPEEDTHINGSUPBYUSINGTHISBINDERTREE ?? $this->binder->getTree()];

        $localPath = explode('/', $this->localPath);

        for ($i = 0; $i < count($localPath); $i++) {
            $tree = $tree->tree[$localPath[$i]];
        }

        return $tree->global_order;
    }

    public function findPartwiseOrder($tmp_SPEEDTHINGSUPBYUSINGTHISBINDERTREE = null)
    {
        $globalOrder = $this->findGlobalOrder($tmp_SPEEDTHINGSUPBYUSINGTHISBINDERTREE);

        $explodedGlobalOrder = explode('.', $globalOrder);

        if (count($explodedGlobalOrder) <= 1) {
            return (new IntToRoman)->filter($globalOrder);
        }

        return implode('.', array_slice($explodedGlobalOrder, 1));
    }

    public function findPart()
    {
        return MdFolder::new($this->getBinder(), explode('/', $this->localPath)[0]);
    }
}
