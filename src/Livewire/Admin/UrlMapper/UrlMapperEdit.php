<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Admin\UrlMapper;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Unusualdope\FrontLaravelEcommerce\Models\ClassList;
use Unusualdope\FrontLaravelEcommerce\Models\UrlMapper;

class UrlMapperEdit extends Component
{
    public string $controllerName = '';

    public string $controllerFqcn = '';

    public array $url = [];

    public $languageModel;

    public function mount(string $controllerName): void
    {
        $this->controllerName = $controllerName;

        $found = ClassList::where('name', $controllerName)
            ->where('type', 'front_controller')
            ->where('is_active', true)
            ->first();

        if (! $found) {
            abort(404, 'Controller not found in registry.');
        }

        $this->controllerFqcn = $found->fqcn;

        $this->languageModel = config('lmt.language_model', config('ud-front-ecommerce.language_model'));
        $languages = $this->languageModel::getLanguagesForMultilangForm();

        foreach ($languages as $lang) {
            $mapper = UrlMapper::query()
                ->where('controller', $this->controllerFqcn)
                ->where('language_id', $lang['id'])
                ->first();

            // Combine friendly_url and url_pattern for display
            $this->url[$lang['id']] = $mapper?->combined_url ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();

        $rules = [];
        foreach ($languages as $lang) {
            $existing = UrlMapper::query()
                ->where('controller', $this->controllerFqcn)
                ->where('language_id', $lang['id'])
                ->first();

            $rule = ['nullable', 'string', 'max:255'];
            if (filled($this->url[$lang['id']] ?? null)) {
                $rule[] = Rule::unique('url_mapper', 'friendly_url')
                    ->where('language_id', $lang['id'])
                    ->ignore($existing?->id);
            }
            $rules["url.{$lang['id']}"] = $rule;
        }
        $this->validate($rules, [], [
            'url' => __('front-ecommerce::url_mapper.url'),
        ]);

        foreach ($languages as $lang) {
            $combinedUrl = trim($this->url[$lang['id']] ?? '');

            if ($combinedUrl === '') {
                UrlMapper::query()
                    ->where('controller', $this->controllerFqcn)
                    ->where('language_id', $lang['id'])
                    ->delete();

                continue;
            }

            // Parse the combined URL into friendly_url and url_pattern
            [$friendlyUrl, $urlPattern] = $this->parseCombinedUrl($combinedUrl);

            UrlMapper::updateOrCreate(
                [
                    'controller' => $this->controllerFqcn,
                    'language_id' => $lang['id'],
                ],
                [
                    'friendly_url' => $friendlyUrl,
                    'url_pattern' => $urlPattern,
                ]
            );
        }

        session()->flash('status', __('front-ecommerce::url_mapper.saved'));
        $this->redirect(route(config('ud-front-ecommerce.admin_route_prefix').'.url-mapper.index'), navigate: true);
    }

    protected function parseCombinedUrl(string $combinedUrl): array
    {
        // Check if the URL contains a pattern (has curly braces)
        if (str_contains($combinedUrl, '{')) {
            // Split by first / to separate friendly_url from pattern
            $parts = explode('/', $combinedUrl, 2);

            if (count($parts) === 2) {
                return [
                    trim($parts[0]),
                    trim($parts[1]),
                ];
            }
        }

        // No pattern found, use entire string as friendly_url
        return [$combinedUrl, null];
    }

    public function render()
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();

        // Get the available slug variables from the controller
        $slugVars = [];
        if (class_exists($this->controllerFqcn)) {
            $controllerInstance = app($this->controllerFqcn);
            if (method_exists($controllerInstance, 'getSlugVars')) {
                $slugVars = $controllerInstance->getSlugVars();
            }
        }

        return view('front-ecommerce::livewire.admin.url-mapper.url-mapper-edit', [
            'languages' => $languages,
            'slugVars' => $slugVars,
        ]);
    }
}
