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

    public function scan(): void
    {
        // Define directories to scan
        $scanPaths = [
            app_path('Http/Controllers/Front'),
            base_path('packages/udcommerce-front/src/Http/Controllers/Front'),
        ];

        $found = [];
        $added = 0;
        $updated = 0;

        foreach ($scanPaths as $path) {
            if (!File::exists($path)) {
                continue;
            }

            $files = File::files($path);
            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $fqcn = $this->getFQCNFromFile($file, $path);

                if (!$fqcn || !$this->isValidController($fqcn)) {
                    continue;
                }

                // Extract display name from class name
                $className = $file->getBasename('.php');
                $name = str_replace('Controller', '', $className);

                // Register or update
                $classList = ClassList::register($name, $fqcn, 'front_controller');

                if ($classList->wasRecentlyCreated) {
                    $added++;
                    $this->createDefaultUrlMappings($fqcn, $name);
                } else {
                    $updated++;
                }

                $found[] = $name . ' (' . basename(str_replace('\\', '\\', $fqcn)) . ')';
            }
        }

        // Clear cache so changes take effect
        Artisan::call('config:clear');

        $message = 'Scanned controllers. ';
        if ($added > 0) {
            $message .= " Added {$added} new. ";
        }
        if ($updated > 0) {
            $message .= " Updated {$updated} existing.";
        }
        if ($added === 0 && $updated === 0) {
            $message .= ' No new controllers found.';
        }

        $this->statusMessage = $message;
        $this->notificationKey++;
    }

    /**
     * Create default URL mappings for a newly discovered controller.
     */
    protected function createDefaultUrlMappings(string $fqcn, string $name): void
    {
        $languageModel = config('lmt.language_model', config('ud-front-ecommerce.language_model'));
        if (! class_exists($languageModel)) {
            return;
        }

        $languages = $languageModel::getLanguagesForMultilangForm();
        $defaultFriendlyUrl = Str::kebab($name);

        foreach ($languages as $lang) {
            $languageId = (int) ($lang['id'] ?? $lang['language_id'] ?? 0);
            if ($languageId <= 0) {
                continue;
            }

            $friendlyUrl = $defaultFriendlyUrl;
            $attempt = 0;
            while (UrlMapper::where('language_id', $languageId)->where('friendly_url', $friendlyUrl)->exists()) {
                $attempt++;
                $friendlyUrl = $defaultFriendlyUrl.'-'.$attempt;
            }

            UrlMapper::updateOrCreate(
                [
                    'controller' => $fqcn,
                    'language_id' => $languageId,
                ],
                [
                    'friendly_url' => $friendlyUrl,
                    'url_pattern' => null,
                ]
            );
        }
    }

    /**
     * Get Fully Qualified Class Name from file path
     */
    protected function getFQCNFromFile(\SplFileInfo $file, string $basePath): ?string
    {
        $relativePath = str_replace($basePath, '', $file->getPathname());
        $relativePath = str_replace(['/', '\\'], '\\', $relativePath);
        $relativePath = ltrim($relativePath, '\\');

        // Remove .php extension
        $class = str_replace('.php', '', $relativePath);

        // Determine namespace based on path
        if (str_contains($file->getPathname(), 'packages/udcommerce-front')) {
            return 'Unusualdope\\FrontLaravelEcommerce\\Http\\Controllers\\Front\\'.$class;
        } else {
            return 'App\\Http\\Controllers\\Front\\' . $class;
        }
    }

    /**
     * Check if class is a valid controller
     */
    protected function isValidController(string $fqcn): bool
    {
        if (!class_exists($fqcn)) {
            return false;
        }

        $reflection = new \ReflectionClass($fqcn);

        // Check if it has a handle method
        if (!$reflection->hasMethod('handle')) {
            return false;
        }

        return true;
    }

    public function render()
    {
        $controllers = ClassList::frontControllers()
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'controller' => $item->fqcn,
                ];
            })
            ->toArray();

        $mappings = UrlMapper::query()
            ->with('language')
            ->get()
            ->groupBy('controller');

        return view('front-ecommerce::livewire.admin.url-mapper.url-mapper-index', [
            'controllers' => $controllers,
            'mappings' => $mappings,
        ]);
    }
}
