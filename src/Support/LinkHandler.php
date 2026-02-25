<?php

namespace Unusualdope\FrontLaravelEcommerce\Support;

use Illuminate\Support\Facades\App;
use Unusualdope\FrontLaravelEcommerce\Models\ClassList;
use Unusualdope\FrontLaravelEcommerce\Models\UrlMapper;
use Unusualdope\LaravelEcommerce\Models\Language;

/**
 * Link generation helper for front ecommerce URLs
 * Similar to CMS LinkHandler pattern with PrestaShop-style URL pattern support
 */
class LinkHandler
{
    public string $controller;

    public string $slug;

    public string $locale;

    public array $params;

    public function __construct(
        string $controller,
        string $slug = '',
        string $locale = '',
        array $params = []
    ) {
        $this->controller = $controller;
        $this->slug = $slug;
        $this->locale = $locale !== '' ? $locale : App::getLocale();
        $this->params = $params;
    }

    /**
     * Get the multilingual link based on controller, slug, and locale
     *
     * @return string The generated URL path
     */
    public function getMultiLanguageLink(): string
    {
        $frontPrefix = config('ud-front-ecommerce.front_route_prefix', 'shop');

        // Find the friendly_url for this controller and locale
        $mapper = UrlMapper::query()
            ->where('controller', $this->controller)
            ->whereHas('language', function ($query) {
                $query->where('iso_code', $this->locale);
            })
            ->first();

        if (! $mapper) {
            return '/link-not-found-error';
        }

        $url = '/'.$frontPrefix;

        // Add locale segment if it's not the default language
        $language = Language::where('iso_code', $this->locale)->first();
        $defaultLanguageId = (int) Language::getDefaultLanguage();

        if ($language && $defaultLanguageId && (int) $language->id !== $defaultLanguageId) {
            $url .= '/'.$this->locale;
        }

        $url .= '/'.$mapper->friendly_url;

        // Build slug based on URL pattern if available
        $slug = $this->buildSlugFromPattern($mapper);

        if (! empty($slug)) {
            $url .= '/'.$slug;
        } elseif (! empty($this->slug)) {
            $url .= '/'.$this->slug;
        }

        return $url;
    }

    /**
     * Build slug from URL pattern and provided parameters.
     * This implements the PrestaShop-style URL pattern generation.
     */
    protected function buildSlugFromPattern(UrlMapper $mapper): string
    {
        // Get URL pattern from database (via mapper) first, then config
        $pattern = $mapper->url_pattern ?? '';

        // If no pattern in database, check config as fallback
        if (empty($pattern)) {
            $patterns = config('ud-front-ecommerce.url_patterns', []);
            $pattern = $patterns[$this->controller] ?? '';
        }

        // If no pattern is set, use the legacy slug
        if (empty($pattern)) {
            return $this->slug;
        }

        // Get the controller's slugVars to validate available placeholders
        $controllerClass = $this->controller;
        if (! class_exists($controllerClass)) {
            return $this->slug;
        }

        $controllerInstance = app($controllerClass);
        if (! method_exists($controllerInstance, 'getSlugVars')) {
            return $this->slug;
        }

        $slugVars = $controllerInstance->getSlugVars();
        $slug = $pattern;

        // Replace placeholders with actual values from params
        foreach ($this->params as $key => $value) {
            if (in_array($key, $slugVars, true)) {
                $slug = str_replace('{'.$key.'}', $value, $slug);
            }
        }

        // If slug still contains placeholders, they weren't provided
        if (preg_match('/\{[a-zA-Z0-9_]+\}/', $slug)) {
            // Fallback to regular slug if placeholders remain
            return $this->slug;
        }

        return $slug;
    }

    /**
     * Generate a link for a controller
     *
     * @param  string  $controller  FQCN of the controller
     * @param  string  $slug  The slug to append (legacy)
     * @param  string  $locale  The locale (default: current locale)
     * @param  array  $params  Parameters for URL pattern generation
     * @return string The generated URL path
     */
    public static function generate(
        string $controller,
        string $slug = '',
        string $locale = '',
        array $params = []
    ): string {
        $linkGenerator = new LinkHandler($controller, $slug, $locale, $params);

        return $linkGenerator->getMultiLanguageLink();
    }

    /**
     * Generate a link for ProductController by registered name
     *
     * @param  array  $params  Parameters for URL pattern (e.g., ['id' => 123, 'slug' => 'my-product'])
     * @param  string  $slug  The slug to append (legacy, for backward compatibility)
     * @param  string  $locale  The locale (default: current locale)
     * @return string The generated URL path
     */
    public static function generateProductLink(array $params = [], string $slug = '', string $locale = ''): string
    {
        $controller = ClassList::where('name', 'Product')
            ->where('type', 'front_controller')
            ->where('is_active', true)
            ->value('fqcn');

        if (! $controller) {
            return '/controller-not-found: Product';
        }

        return self::generate($controller, $slug, $locale, $params);
    }

    /**
     * Generate a link for any registered controller by name
     *
     * @param  string  $name  The registered controller name
     * @param  array  $params  Parameters for URL pattern generation
     * @param  string  $slug  The slug to append (legacy, for backward compatibility)
     * @param  string  $locale  The locale (default: current locale)
     * @return string The generated URL path
     */
    public static function generateByControllerName(string $name, array $params = [], string $slug = '', string $locale = ''): string
    {
        $controller = ClassList::where('name', $name)
            ->where('type', 'front_controller')
            ->where('is_active', true)
            ->value('fqcn');

        if (! $controller) {
            return '/controller-not-found: '.$name;
        }

        return self::generate($controller, $slug, $locale, $params);
    }

    /**
     * Generate a link with pattern-based parameters.
     * This is the new recommended method for generating URLs.
     *
     * @param  string  $controller  FQCN of the controller
     * @param  array  $params  Parameters to fill the URL pattern (e.g., ['id' => 123, 'slug' => 'my-product'])
     * @param  string  $locale  The locale (default: current locale)
     * @return string The generated URL path
     */
    public static function generateWithPattern(string $controller, array $params, string $locale = ''): string
    {
        return self::generate($controller, '', $locale, $params);
    }
}
