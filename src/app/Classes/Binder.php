<?php

namespace AMoschou\Scribo\App\Classes;

use Romans\Filter\IntToRoman;

class Binder
{
    public $name;
    private $rootFolder;
    public $path;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->path = config("scribo.binders.{$name}.path");
        $this->rootFolder = new MdFolder($this, '');
    }

    public static function get(string $name)
    {
        return new Binder($name);
    }

    public function getTitle()
    {
        return config("scribo.binders.{$this->name}.title");
    }

    public function getMdPath()
    {
        return config("scribo.paths.md") . "/{$this->path}";
    }

    public function getPdfPath()
    {
        return config("scribo.paths.pdf") . "/{$this->path}";
    }

    public function getRootFolder()
    {
        return $this->rootFolder;
    }

    public function getTree()
    {
        return $this->getRootFolder()->getTree();
    }

    public function getFlatTree()
    {
        return $this->getRootFolder()->getFlatTree();
    }

    public function getPartwiseOrders()
    {
        $flatTree = $this->getFlatTree();

        $partwiseOrders = [
            '' => null,
            '/' => null,
        ];

        foreach ($flatTree as $localpath => $item) {
            $globalOrder = explode('.', $item->global_order);

            $partwiseOrders[$localpath] = count($globalOrder) === 1
                ? (new IntToRoman)->filter($globalOrder[0])
                : implode('.', array_slice($globalOrder, 1));
        }

        return $partwiseOrders;
    }
}
