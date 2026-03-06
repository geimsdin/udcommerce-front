<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Admin\UrlMapper;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Unusualdope\FrontLaravelEcommerce\Models\ClassList;
use Unusualdope\FrontLaravelEcommerce\Models\UrlMapper;

class UrlMapperIndex extends Component
{
    public string $statusMessage = '';
    public int $notificationKey = 0;

    /**
     * Map of absolute directory path → PHP namespace root.
     * Add more entries here to scan additional directories.
     */
    protected function scanPaths(): array
    {
        return [
            app_path('Http/Controllers/Front')
            => 'App\\Http\\Controllers\\Front',

            base_path('packages/udcommerce-front/src/Http/Controllers/Front')
            => 'Unusualdope\\FrontLaravelEcommerce\\Http\\Controllers\\Front',
        ];
    }

    public function scan(): void
    {
        $added = 0;
        $updated = 0;

        foreach ($this->scanPaths() as $path => $namespace) {
            if (!File::exists($path)) {
                continue;
            }

            foreach (File::files($path) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $fqcn = $namespace . '\\' . $file->getBasename('.php');

                if (!$this->isValidController($fqcn)) {
                    continue;
                }

                $name = str_replace('Controller', '', $file->getBasename('.php'));
                $classList = ClassList::register($name, $fqcn, 'front_controller');

                if ($classList->wasRecentlyCreated) {
                    $added++;
                    $this->createDefaultUrlMappings($fqcn, $name);
                } else {
                    $updated++;
                }
            }
        }

        Artisan::call('config:clear');

        $this->statusMessage = $this->buildStatusMessage($added, $updated);
        $this->notificationKey++;
    }

    /**
     * Build a human-readable scan result message.
     */
    protected function buildStatusMessage(int $added, int $updated): string
    {
        if ($added === 0 && $updated === 0) {
            return 'Scanned controllers. No new controllers found.';
        }

        return trim(implode(' ', array_filter([
            'Scanned controllers.',
            $added > 0 ? "Added {$added} new." : '',
            $updated > 0 ? "Updated {$updated} existing." : '',
        ])));
    }

    /**
     * Create default URL mappings for a newly discovered controller.
     */
    protected function createDefaultUrlMappings(string $fqcn, string $name): void
    {
        $languageModel = config('lmt.language_model', config('ud-front-ecommerce.language_model'));

        if (!class_exists($languageModel)) {
            return;
        }

        $defaultSlug = Str::kebab($name);

        foreach ($languageModel::getLanguagesForMultilangForm() as $lang) {
            $languageId = (int) ($lang['id'] ?? $lang['language_id'] ?? 0);

            if ($languageId <= 0) {
                continue;
            }

            UrlMapper::updateOrCreate(
                ['controller' => $fqcn, 'language_id' => $languageId],
                ['friendly_url' => $this->uniqueFriendlyUrl($languageId, $defaultSlug), 'url_pattern' => null]
            );
        }
    }

    /**
     * Return a slug that doesn't collide with existing URL mappings for the given language.
     */
    protected function uniqueFriendlyUrl(int $languageId, string $base): string
    {
        $slug = $base;
        $attempt = 0;

        while (UrlMapper::where('language_id', $languageId)->where('friendly_url', $slug)->exists()) {
            $slug = $base . '-' . ++$attempt;
        }

        return $slug;
    }

    /**
     * Return true if the class exists and exposes a handle() method.
     */
    protected function isValidController(string $fqcn): bool
    {
        return class_exists($fqcn)
            && (new \ReflectionClass($fqcn))->hasMethod('handle');
    }

    public function render()
    {
        $controllers = ClassList::frontControllers()
            ->get()
            ->map(fn($item) => ['name' => $item->name, 'controller' => $item->fqcn])
            ->toArray();

        $mappings = UrlMapper::query()
            ->with('language')
            ->get()
            ->groupBy('controller');

        return view('front-ecommerce::livewire.admin.url-mapper.url-mapper-index', compact('controllers', 'mappings'));
    }
}
