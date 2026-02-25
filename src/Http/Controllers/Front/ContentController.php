<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Unusualdope\FrontLaravelEcommerce\Support\UrlMapperResolver;

abstract class ContentController extends Controller
{
    protected array $slugVars = [];

    public static array $breadcrumb = [];

    abstract protected function render(Request $request, array $params): mixed;

    /**
     * Main entry point for catch-all routing via UrlMapper.
     * This dispatches to the appropriate controller based on URL mapping.
     */
    public function handle(Request $request, ?string $path = null): Response
    {
        $path = $path ?? $request->path();

        // Skip if path starts with admin
        if (str_starts_with($path, 'admin/') || $path === 'admin') {
            abort(404);
        }

        $resolved = UrlMapperResolver::resolveByPath($path, $request);

        if ($resolved === false) {
            abort(404);
        }

        // Create instance of resolved controller and call handleWithSlug
        $controllerInstance = app()->make($resolved['controller']);

        $result = $controllerInstance->handleWithSlug($request, $resolved['slug']);

        // Ensure we always return a Response
        if ($result instanceof Response) {
            return $result;
        }

        return response($result);
    }

    protected function getUrlPattern(): string
    {
        $controllerName = get_class($this);

        $languageId = $this->getCurrentLanguageId();
        if ($languageId) {
            $pattern = \Unusualdope\FrontLaravelEcommerce\Models\UrlMapper::getPatternForController(
                $controllerName,
                $languageId
            );
            if ($pattern) {
                return $pattern;
            }
        }

        $patterns = config('ud-front-ecommerce.url_patterns', []);

        return $patterns[$controllerName] ?? '';
    }

    protected function getCurrentLanguageId(): ?int
    {
        // Try session first (set by UrlMapperResolver)
        $locale = session('locale', App::getLocale());

        $languageModel = config('ud-front-ecommerce.language_model');
        if (!$languageModel) {
            return null;
        }

        $language = $languageModel::where('iso_code', $locale)->first();

        return $language ? $language->id : null;
    }

    protected function parseUrlFromPattern(string $pattern, string $url): array
    {
        $params = [];

        if (empty($pattern)) {
            return ['slug' => $url];
        }

        // Allow hyphens in placeholder names (e.g. {link-rewrite}); normalize to underscore for param keys
        preg_match_all('/\{([a-zA-Z0-9_-]+)\}/', $pattern, $matches);
        $placeholders = $matches[1] ?? [];

        if (empty($placeholders)) {
            return ['slug' => $url];
        }

        $regexPattern = $pattern;
        $lastPlaceholder = end($placeholders);

        foreach ($placeholders as $placeholder) {
            $paramKey = str_replace('-', '_', $placeholder);
            if (! in_array($paramKey, $this->slugVars, true)) {
                continue;
            }

            // Use normalized name for regex group (PHP allows only letters, digits, underscore)
            if ($placeholder === 'id') {
                $replacement = '(?P<' . $paramKey . '>\d+)';
            } elseif ($placeholder === $lastPlaceholder) {
                $replacement = '(?P<' . $paramKey . '>[a-zA-Z0-9-]+)';
            } else {
                $replacement = '(?P<' . $paramKey . '>[a-zA-Z0-9]+)';
            }

            $regexPattern = str_replace('{' . $placeholder . '}', $replacement, $regexPattern);
        }

        $regexPattern = str_replace('/', '\/', $regexPattern);
        $regexPattern = '/^' . $regexPattern . '$/';

        if (preg_match($regexPattern, $url, $matches)) {
            foreach ($placeholders as $placeholder) {
                $paramKey = str_replace('-', '_', $placeholder);
                if (isset($matches[$paramKey])) {
                    $params[$paramKey] = $matches[$paramKey];
                }
            }
        } else {
            // If pattern doesn't match, fall back to slug
            $params['slug'] = $url;
        }

        return $params;
    }

    /**
     * Handle request with slug - parses URL pattern and calls render.
     */
    public function handleWithSlug(Request $request, string $slug = ''): mixed
    {
        $urlPattern = $this->getUrlPattern();

        if (empty($urlPattern)) {
            return $this->render($request, ['slug' => $slug]);
        }

        $params = $this->parseUrlFromPattern($urlPattern, $slug);

        return $this->render($request, $params);
    }

    public function getSlugVars(): array
    {
        return $this->slugVars;
    }
}
