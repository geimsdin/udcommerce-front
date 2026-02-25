<?php

namespace Unusualdope\FrontLaravelEcommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlMapper extends Model
{
    protected $table = 'url_mapper';

    protected $fillable = [
        'language_id',
        'friendly_url',
        'controller',
        'url_pattern',
    ];

    /**
     * Get the language associated with this URL mapping.
     */
    public function language(): BelongsTo
    {
        $languageModel = config('ud-front-ecommerce.language_model');

        return $this->belongsTo($languageModel, 'language_id');
    }

    /**
     * Get the combined URL display for the admin UI.
     * Returns "product/{id}-{slug}" format for display.
     */
    public function getCombinedUrlAttribute(): string
    {
        if (empty($this->url_pattern)) {
            return $this->friendly_url;
        }

        return $this->friendly_url.'/'.$this->url_pattern;
    }

    /**
     * Get the URL pattern for this mapping.
     * Returns null if not set.
     */
    public function getUrlPattern(): ?string
    {
        return $this->url_pattern;
    }

    /**
     * Get URL pattern by controller and language.
     * Static helper method for looking up patterns from the database.
     */
    public static function getPatternForController(string $controller, int $languageId): ?string
    {
        return static::where('controller', $controller)
            ->where('language_id', $languageId)
            ->value('url_pattern');
    }

    /**
     * Get all UrlMapper entries with a specific pattern.
     *
     * @param  string  $pattern  The URL pattern to search for (e.g., '{id}-{slug}')
     * @param  int|null  $languageId  Optional language filter
     *return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByPattern(string $pattern, ?int $languageId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::where('url_pattern', $pattern);

        if ($languageId !== null) {
            $query->where('language_id', $languageId);
        }

        return $query->with('language')->get();
    }

    /**
     * Find all UrlMapper entries for a specific controller across all languages.
     *
     * @param  string  $controller  The FQCN of the controller
     */
    public static function findByController(string $controller): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('controller', $controller)
            ->with('language')
            ->get();
    }

    /**
     * Get a single UrlMapper by controller and language.
     *
     * @param  string  $controller  The FQCN of the controller
     * @param  int  $languageId  The language ID
     */
    public static function findByControllerAndLanguage(string $controller, int $languageId): ?static
    {
        return static::where('controller', $controller)
            ->where('language_id', $languageId)
            ->first();
    }

    /**
     * Check if this mapper has a URL pattern set.
     */
    public function hasPattern(): bool
    {
        return ! empty($this->url_pattern);
    }

    /**
     * Extract parameter names from the URL pattern.
     * e.g., "{id}-{slug}" -> ['id', 'slug']
     *
     * @return array<int, string>
     */
    public function getPatternParameterNames(): array
    {
        if (empty($this->url_pattern)) {
            return [];
        }

        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $this->url_pattern, $matches);

        return $matches[1] ?? [];
    }
}
