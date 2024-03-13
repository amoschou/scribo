<?php

namespace AMoschou\Scribo\App\Http\Controllers;

use AMoschou\Scribo\App\Classes\MdFile;
use AMoschou\Scribo\App\Classes\MdFolder;
use AMoschou\Scribo\App\Classes\MdNode;
use AMoschou\Scribo\App\Classes\Binder;
use Howtomakeaturn\PDFInfo\PDFInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use mikehaertl\pdftk\Pdf as PdfTk;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\LaravelPdf\Facades\Pdf as LaravelPdf;

class ScriboController extends Controller
{
    private function checkAuthorization()
    {
        Gate::authorize('is-hsc-teacher');
    }

    public function binderHtml(Request $request, string $binder) {
        return $this->binder($request, $binder, 'html');
    }

    public function binderPdf(Request $request, string $binder) {
        return $this->binder($request, $binder, 'pdf');
    }

    public function binderContentsHtml(Request $request, string $binder) {
        return $this->binderContents($request, $binder, 'html');
    }

    public function binderContentsPdf(Request $request, string $binder) {
        return $this->binderContents($request, $binder, 'pdf');
    }

    private function binder(Request $request, string $binder, string $format = 'html', $options = []) {
        $this->checkAuthorization();

        $binder = Binder::get($binder);

        $request->merge(['format' => $format]);

        return $this->view('scribo::bs-page', [
            'binder' => $binder,
            'nodeItem' => $binder->getRootFolder(),
            'isBinder' => true,
            'options' => $options,
        ], $format);
    }

    private function binderContents(Request $request, string $binder, string $format = 'html', $options = []) {
        $this->checkAuthorization();

        $binder = Binder::get($binder);

        $request->merge(['format' => $format]);

        return $this->view('scribo::bs-page', [
            'binder' => $binder,
            'nodeItem' => $binder->getRootFolder(),
            'isBinder' => true,
            'isContent' => true,
            'options' => $options,
        ], $format);
    }

    public function binderPath(Request $request, string $binder, string $path, $options = []) {
        $this->checkAuthorization();

        $binder = Binder::get($binder);

        $isPdf = Str::endsWith($path, '.pdf');

        $format = $isPdf ? 'pdf' : 'html';

        $request->merge(['format' => $format]);

        $localPath = $isPdf ? Str::replaceLast('.pdf', '', $path) : $path;

        return $this->view('scribo::bs-page', [
            'binder' => $binder,
            'nodeItem' => MdNode::new($binder, $localPath),
            'isBinder' => false,
        ], $format, $options);
    }

    private function view($view, $context, $format = 'html', $options = []) {
        $margin = 10;

        if ($format === 'html') {
            $html = view($view, $context);

            return $html;
        }

        if ($format === 'pdf') {
            $disk = Storage::build([
                'driver' => 'local',
                'root' => $context['binder']->getPdfPath(),
            ]);

            $frontOrMainMatter = $context['isBinder'] ? 'frontmatter/' : 'mainmatter/';
    
            if ($disk->exists($frontOrMainMatter . $context['nodeItem']->getLocalPath() . '.pdf')) {
                return response()->file($disk->path($frontOrMainMatter . $context['nodeItem']->getLocalPath() . '.pdf'));
            }
    
            abort_unless(in_array(config('app.env'), [
                'local',
                'github_runner',
            ]), 503, 'You requested a PDF file, but it is not yet ready. Hopefully, this page will refresh automatically, so keep the tab open and check here again in a few minutes.', [
                'Retry-After' => 60,
            ]);

            $pdf = LaravelPdf::view($view, $context)->paperSize(210.0, 297.0, 'mm');
        
            $pdf = $context['isBinder'] ? $pdf : $pdf->footerView('scribo::components.app-pdf-footer');

            $pdf->withBrowsershot(function (Browsershot $browsershot) use ($options) {
                    $browsershot->initialPageNumber($options['initialPageNumber'] ?? 1);
                })
                ->margins(10, 14, 20, 14, Unit::Millimeter) /* (top, right, bottom, left) */
                ->name($context['nodeItem']->yamlData('title'));
            
            return $pdf;
        }
    }

    public function completeBinderPdf(Request $request, string $binder)
    {
        $this->checkAuthorization();

        $binder = Binder::get($binder);

        $disk = Storage::build([
            'driver' => 'local',
            'root' => $binder->getPdfPath(),
        ]);

        if ($disk->exists('binder.pdf')) {
            return response()->file($disk->path('binder.pdf'));

            // return $disk->download('binder.pdf');
        }

        abort_unless(in_array(config('app.env'), [
            'local',
            'github_runner',
        ]), 503, 'You requested a PDF file, but it is not yet ready. Hopefully, this page will refresh automatically, so keep the tab open and check here again in a few minutes.', [
            'Retry-After' => 60,
        ]);

        $flatTree = $binder->getFlatTree();

        $pdfMainmatterList = [];
        $pdfFrontmatterList = [];
        $pageNumbers = [];

        $pageNumber = 1;

        $i = 0;
        $limit = (int) ($request->input('limit') ?? -1);
        foreach ($flatTree as $localpath => $item) {
            if ($limit < 0 || $i++ < $limit) {
                $laravelPdf = $this->binderPath($request, $binder->name, "{$localpath}.pdf", [
                    'initialPageNumber' => $pageNumber,
                ]);

                $laravelPdfContent = invade($laravelPdf)->getBrowsershot()->pdf();

                $disk->put("mainmatter/{$localpath}.pdf", $laravelPdfContent);

                $fullPathToPdf = $binder->getPdfPath() . "/mainmatter/{$localpath}.pdf";

                $pdfMainmatterList[] = $fullPathToPdf;
                $pageNumbers[$localpath] = $pageNumber;

                $pageNumber = $pageNumber + (new PDFInfo($fullPathToPdf))->pages;
            }
        }

        $cover = $disk->put(
            'frontmatter/cover.pdf',
            invade($this->binder($request, $binder->name, 'pdf', [
                'withWarning' => true,
            ]))->getBrowsershot()->pdf()
        );
        $pdfFrontmatterList[] = $binder->getPdfPath() . '/frontmatter/cover.pdf';

        $contents = $disk->put(
            'frontmatter/contents.pdf',
            invade($this->binderContents($request, $binder->name, 'pdf', [
                'pageNumbers' => $pageNumbers,
            ]))->getBrowsershot()->pdf()
        );
        $pdfFrontmatterList[] = $binder->getPdfPath() . '/frontmatter/contents.pdf';

        (new PdfTk(array_merge($pdfFrontmatterList, $pdfMainmatterList)))->cat()->send("{$binder->getTitle()}.pdf");
    }
}
