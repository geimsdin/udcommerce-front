<div class="max-w-2xl space-y-6">
    <div>
        <flux:heading size="xl">{{ __('front-ecommerce::url_mapper.edit_title') }}</flux:heading>
        <flux:subheading>{{ __('front-ecommerce::url_mapper.edit_subtitle', ['name' => $controllerName]) }}</flux:subheading>
        <p class="mt-1 text-sm text-zinc-500 font-mono">{{ $controllerFqcn }}</p>
    </div>

    @if(!empty($slugVars))
        <flux:callout variant="info">
            <div class="text-sm">
                <p class="font-semibold mb-2">{{ __('front-ecommerce::url_mapper.pattern_title') }}</p>
                <p><strong>{{ __('front-ecommerce::url_mapper.examples') }}:</strong>
                    <code class="ml-1">{{ $controllerName }}</code>
                    @if(in_array('id', $slugVars) && in_array('slug', $slugVars))
                        <code class="ml-3">{{ $controllerName }}/{id}-{slug}</code>
                    @endif
                    @if(in_array('category', $slugVars) && in_array('slug', $slugVars))
                        <code class="ml-3">{{ $controllerName }}/{category}/{slug}</code>
                    @endif
                </p>
            </div>
        </flux:callout>
    @endif

    <form wire:submit="save" class="space-y-6">
        <flux:card>
            <div class="space-y-4">
                @foreach($languages as $lang)
                    <flux:input
                        wire:model="url.{{ $lang['id'] }}"
                        :label="__('front-ecommerce::url_mapper.url') . ' (' . ($lang['name'] ?? $lang['iso_code']) . ')'"
                        placeholder="e.g. {{ $controllerName }} or {{ $controllerName }}/{id}-{slug}"
                    />
                    @if(!empty($slugVars))
                        <p class="mt-2 text-xs text-zinc-500">
                            <strong>{{ __('front-ecommerce::url_mapper.keywords') }}:</strong>
                            @foreach($slugVars as $var)
                                <code class="ml-1">{{ '{' . $var . '}' }}</code>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </p>
                    @endif
                @endforeach

                <flux:separator />
                <div class="flex items-center gap-4 justify-end">
                    <flux:button variant="ghost" :href="route(config('ud-front-ecommerce.admin_route_prefix').'.url-mapper.index')" wire:navigate>
                        {{ __('front-ecommerce::url_mapper.cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        {{ __('front-ecommerce::url_mapper.save') }}
                    </flux:button>
                </div>
            </div>
        </flux:card>
    </form>
</div>
