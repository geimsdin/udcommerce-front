<?php

namespace Unusualdope\FrontLaravelEcommerce\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Unusualdope\FrontLaravelEcommerce\Models\UrlMapper;

class UrlMapperResolver
{
    /**
     * @return array{controller: string, slug: string, locale: string}|false
     */
    public static function resolveByPath(?string $path, Request $request): array|false
    {
        $segments = $path ? array_values(array_filter(explode('/', $path))) : [];

        $resolved = static::resolveLanguageId($segments);
        if (! $resolved) {
            return false;
        }

        // Set app locale and session (CMS pattern)
        App::setLocale($resolved['locale']);
        session()->put('locale', $resolved['locale']);

        $firstSegment = $segments[0] ?? null;
        if ($firstSegment === null) {
            return false;
        }

        $mapper = UrlMapper::query()
            ->where('language_id', $resolved['language_id'])
            ->where('friendly_url', $firstSegment)
            ->first();

        if (! $mapper || ! class_exists($mapper->controller)) {
            return false;
        }

        if (! method_exists($mapper->controller, 'handle')) {
            return false;
        }

        $slug = isset($segments[1]) ? implode('/', array_slice($segments, 1)) : '';

        return [
            'controller' => $mapper->controller,
            'slug' => $slug,
            'locale' => $resolved['locale'],
            'params' => static::extractPatternParams($mapper, $slug),
        ];
    }

    /**
     * Extract parameters from slug based on URL pattern.
     * e.g., pattern "{id}-{slug}" with slug "123-my-product" returns ['id' => 123, 'slug' => 'my-product']
     *
     * @return array<string, string|int>
     */
    protected static function extractPatternParams(UrlMapper $mapper, string $slug): array
    {
        if (empty($slug) || empty($mapper->url_pattern)) {
            return [];
        }

        $paramNames = $mapper->getPatternParameterNames();
        if (empty($paramNames)) {
            return [];
        }

        // Build a regex pattern from the URL pattern
        // e.g., "{id}-{slug}" becomes "^(\d+)-([a-zA-Z0-9-]+)$"
        $regexPattern = $mapper->url_pattern;
        $lastParam = end($paramNames);

        // Replace placeholders with capture groups
        // Use appropriate regex based on parameter name and position
        foreach ($paramNames as $param) {
            if ($param === 'id') {
                // ID is always numeric
                $replacement = '(\d+)';
            } elseif ($param === $lastParam) {
                // Last parameter can contain hyphens (greedy is OK)
                $replacement = '([a-zA-Z0-9-]+)';
            } else {
                // Non-last parameters: match until the next separator (non-greedy)
                // This prevents {id}-{slug} from matching "1-example" as id
                $replacement = '([a-zA-Z0-9]+)';
            }

            $regexPattern = str_replace(
                '{'.$param.'}',
                $replacement,
                $regexPattern
            );
        }

        $regexPattern = '#^'.$regexPattern.'$#';

        if (! preg_match($regexPattern, $slug, $matches)) {
            return [];
        }

        // Remove the full match and map captured groups to parameter names
        array_shift($matches);

        $params = [];
        foreach ($paramNames as $index => $paramName) {
            $params[$paramName] = $matches[$index] ?? '';
        }

        return $params;
    }

    /**
     * Resolve a URL by pattern directly.
     * Useful for finding which controller/params match a given pattern + slug combination.
     *
     * @param  string  $pattern  The URL pattern (e.g., '{id}-{slug}')
     * @param  string  $slug  The slug to match (e.g., '123-my-product')
     * @param  int|null  $languageId  Optional language filter
     * @return array{controller: string, params: array, locale: string, mapper: UrlMapper}|false
     */
    public static function resolveByPattern(string $pattern, string $slug, ?int $languageId = null): array|false
    {
        $query = UrlMapper::where('url_pattern', $pattern);

        if ($languageId !== null) {
            $query->where('language_id', $languageId);
        }

        $mappers = $query->get();

        foreach ($mappers as $mapper) {
            $params = static::extractPatternParams($mapper, $slug);
            if (! empty($params)) {
                return [
                    'controller' => $mapper->controller,
                    'params' => $params,
                    'locale' => $mapper->language->iso_code ?? '',
                    'mapper' => $mapper,
                ];
            }
        }

        return false;
    }

    /**
     * Get data by matching a URL pattern.
     * This is a convenience method for querying data based on URL patterns.
     *
     * @param  string  $controller  The controller FQCN
     * @param  array  $params  Parameters extracted from URL pattern (e.g., ['id' => 123, 'slug' => 'my-product'])
     * @return array<string, mixed>|false The data found or false
     */
    public static function getDataByPattern(string $controller, array $params): array|false
    {
        if (! class_exists($controller)) {
            return false;
        }

        $controllerInstance = app($controller);

        // Check if controller has a method to fetch data by pattern params
        if (method_exists($controllerInstance, 'getDataByPatternParams')) {
            return $controllerInstance->getDataByPatternParams($params);
        }

        // Default behavior: return params as-is
        return $params;
    }

    /**
     * @param  array<int, string>  $segments
     * @return array{language_id: int, locale: string}|false
     */
    protected static function resolveLanguageId(array &$segments): array|false
    {
        $languageModel = config('ud-front-ecommerce.language_model');
        $languages = $languageModel::all();
        $isoToId = [];

        foreach ($languages as $lang) {
            $isoToId[$lang->iso_code] = $lang->id;
        }

        // Check if first segment is a valid locale
        if (! empty($segments) && isset($isoToId[$segments[0]])) {
            $locale = array_shift($segments);
            $languageId = $isoToId[$locale];
            if ($languageId) {
                return [
                    'language_id' => (int) $languageId,
                    'locale' => $locale,
                ];
            }
        }

        // Use default language
        $defaultId = $languageModel::getDefaultLanguage();
        $defaultLanguage = $languages->firstWhere('id', $defaultId) ?? $languages->first();

        if (! $defaultLanguage) {
            return false;
        }

        return [
            'language_id' => (int) $defaultLanguage->id,
            'locale' => $defaultLanguage->iso_code,
        ];
    }
}
