<?php

namespace AMoschou\Scribo\App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MissingPdfException extends Exception
{
    private $data;

    public function withData($data = null)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // ...
    }
 
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response
    {
        return response()->view('scribo::errors.503-missing-pdf', [
            'message' => 'This PDF is currently unavailable. Please try again in a few minutes.',
            'data' => $this->data ?? [],
        ], 503);
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            //
        ];
    }
}