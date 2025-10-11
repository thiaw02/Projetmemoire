<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Vérifier si la compression est supportée
        $acceptEncoding = $request->server('HTTP_ACCEPT_ENCODING', '');
        
        if (!str_contains($acceptEncoding, 'gzip')) {
            return $response;
        }

        $content = $response->getContent();
        
        // Ne compresser que si le contenu est suffisamment grand
        if (strlen($content) < 1024) {
            return $response;
        }

        // Compresser le contenu
        $compressedContent = gzencode($content, 6); // Niveau 6 = bon compromis
        
        if ($compressedContent !== false) {
            $response->setContent($compressedContent);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Content-Length', strlen($compressedContent));
            $response->headers->set('Vary', 'Accept-Encoding');
        }

        return $response;
    }
}