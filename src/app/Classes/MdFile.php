<?php

namespace AMoschou\Scribo\App\Classes;

// use AMoschou\CommonMark\Alert\AlertExtension;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverter;
use SplFileInfo;

class MdFile
{
    use NodeItem;

    private $mdFileInfo;
    private $pdfFileInfo;
    private $md;
    private $html;
    private $toc;
    public $type = 'file';

    public function __construct($binder, $localPath)
    {
        $this->binder = $binder instanceof Binder ? $binder : new Binder($binder);

        $this->localPath = $localPath;

        $mdPathname = $this->getPathname('md');

        $pdfPathname = $this->getPathname('pdf');

        $this->mdFileInfo = new SplFileInfo($mdPathname);

        $this->pdfFileInfo = new SplFileInfo($pdfPathname);

        $this->setYamlData();

        $this->setMetadata();
    }

    public static function new($binder, $localPath)
    {
        return new MdFile($binder, $localPath);
    }

    private function getPathname($filetype)
    {
        abort_if(! in_array($filetype, ['md', 'pdf']), 500, 'Only MD and PDF filetypes are supported here.');

        $file = match ($filetype) {
            'md' => $this->mdFileInfo,
            'pdf' => $this->pdfFileInfo,
        };

        $path = config("scribo.paths.{$filetype}") . "/{$this->binder->path}/{$this->localPath}.{$filetype}";

        return is_null($file) ? $path : $file->getPathname();
    }

    public function exists()
    {
        return $this->mdFileInfo->isFile();
    }

    private function setYamlData()
    {
        $default = [
            'ord' => 0,
            'title' => $this->mdFileInfo->getBaseName('.md'),
        ];

        $frontmatter = (new FrontMatterParser(new SymfonyYamlFrontMatterParser()))
                    ->parse(file_get_contents($this->mdFileInfo->getRealPath()))
                    ->getFrontMatter();

        $frontmatter = is_null($frontmatter) ? [] : $frontmatter;

        try {
            $this->yamlData = array_merge($default, $frontmatter);
        } catch (\Throwable $t) {
            $this->yamlData = $default;
        }
    }

    private function setMetadata()
    {
        // This slows it down a lot. Comment out unncessary things.

        $this->metadata = [
            'title' => $this->yamlData('title'),
            'formattedTitle' => Str::inlineMarkdown($this->yamlData('title')), //$this->yamlData('title'), // Str::inlineMarkdown($this->yamlData('title')),
            'ord' => $this->yamlData('ord'),
            'localPath' => $this->localPath,
            // 'breadcrumbs' => $this->breadcrumbs(),
            // 'updateCarbon' => Carbon::createFromTimestamp($this->mdFileInfo->getMTime()),
            'editLink' => 'https://github.com/' . config("scribo.binders.{$this->binder->name}.github_repo") . "/edit/main/{$this->localPath}.md",
            'sourceLink' => 'https://github.com/' . config("scribo.binders.{$this->binder->name}.github_repo") . "/blob/main/{$this->localPath}.md",
        ];
    }

    public function getGitDetails($key = null, $forHumans = true)
    {
        $git = [
            'date' => Process::path($this->binder->getMdPath())
                ->run('git log -1 --pretty="format:%aI" ' . "{$this->localPath}.md")
                ->output(),
            'author' => Process::path($this->binder->getMdPath())
                ->run('git log -1 --pretty="format:%aN" ' . "{$this->localPath}.md")
                ->output(),
        ];

        if (is_null($key)) {
            return $git;
        }

        $git = $git[$key];

        if (! $forHumans) {
            return $git;
        }

        return match ($key) {
            'date' => Carbon::parse($git)->setTimezone(config('app.timezone'))->format('g:i A, l, j F Y'),
            'author' => $git,
        };
    }

    public function getMetadata()
    {
        ////
    }

    private function setHtml($option = 'gfm')
    {
        // THERE ARE THREE OPTIONS TO CONVERT MD TO HTML
        // INDICATED BY 'gfm', 'ext' and 'api'.

        $md = $this->getMd();

        $html = match ($option) {
            'gfm' => (new GithubFlavoredMarkdownConverter())->convert($md),
            'ext' => (new MarkdownConverter(
                    (new Environment([]))
                        ->addExtension(new CommonMarkCoreExtension())
                        ->addExtension(new GithubFlavoredMarkdownExtension())
                        // ->addExtension(new AlertExtension())
                ))
                ->convert($md),
            'api' => Http::accept('application/vnd.github+json')
                ->withToken(config("scribo.binders.{$this->binder->name}.github_api_token"))
                ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28'])
                ->post('https://api.github.com/markdown', ['text' => $md, 'mode' => 'gfm'])
                ->body(),
        };

        $this->html = $html;
    }

    public function getHtml()
    {
        if (is_null($this->html)) {
            $this->setHtml();
        }

        return $this->html;
    }

    private function getMd()
    {
        if (is_null($this->md)) {
            $this->setMd();
        }

        return $this->md;
    }

    private function setMd()
    {
        $this->md = $this->md();
    }

    private function md()
    {
        return (new FrontMatterExtension())
            ->getFrontMatterParser()
            ->parse(file_get_contents($this->mdFileInfo->getPathname()))
            ->getContent();
    }

    private function setToc()
    {
        preg_match_all('/<h([1-6])*[^>]*>(.*?)<\/h[1-6]>/', $this->getHtml(), $matches);

        $tags = $matches[0];
        $levels = $matches[1];
        $texts = $matches[2];

        $count = count($tags);

        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = [
                'level' => (int) $levels[$i],
                'text' => $texts[$i],
                'formattedText' => Str::inlineMarkdown($texts[$i]),
            ];
        }

        $this->toc = [
            'count' => count($tags),
            'items' => $items,
        ];
    }

    public function getToc()
    {
        if (is_null($this->toc)) {
            $this->setToc();
        }

        return $this->toc;
    }

    public function getTree()
    {
        return [];
    }

    public function getFlatTree()
    {
        return [];
    }
}


