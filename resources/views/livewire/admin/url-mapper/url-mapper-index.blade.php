<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('front-ecommerce::url_mapper.title') }}</flux:heading>
        </div>
        <button wire:click="scan" class="px-4 py-2 bg-blue-500 cursor-pointer hover:bg-blue-800 text-white rounded-lg text-sm font-medium transition-colors">
            Scan Classes
        </button>
    </div>

    @if($statusMessage)
        <div x-data="{ show: true }"
            wire:key="notification-{{ $notificationKey }}"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <flux:callout variant="success" icon="check-circle">
                {{ $statusMessage }}
            </flux:callout>
        </div>
    @endif

    <flux:card class="space-y-4">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('front-ecommerce::url_mapper.controller') }}</flux:table.column>
                <flux:table.column>FQCN</flux:table.column>
                <flux:table.column>Mapped URLs</flux:table.column>
                <flux:table.column class="w-40">{{ __('front-ecommerce::url_mapper.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @if(!empty($controllers))
                    @foreach($controllers as $c)
                        <flux:table.row wire:key="controller-{{ $c['name'] }}">
                            <flux:table.cell>{{ $c['name'] }}</flux:table.cell>
                            <flux:table.cell class="text-sm text-zinc-500 font-mono">{{ $c['controller'] }}</flux:table.cell>
                            <flux:table.cell>
                                @php $items = $mappings->get($c['controller'], collect()); @endphp
                                @if($items->isNotEmpty())
                                    @foreach($items as $m)
                                        <span class="inline-block mr-2 text-sm">
                                            <strong> {{ $m->language->iso_code ?? $m->language_id }}:</strong> {{ $m->combined_url }}
                                        </span>
                                        <br>
                                    @endforeach
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button
                                    size="sm"
                                    variant="primary"
                                    icon="pencil"
                                    :href="route(config('ud-front-ecommerce.admin_route_prefix').'.url-mapper.edit', ['controllerName' => $c['name']])"
                                    wire:navigate
                                >
                                    {{ __('front-ecommerce::url_mapper.manage_urls') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @else
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8">
                            <flux:text class="text-zinc-500">No controllers found. Click "Scan Classes" to automatically discover controllers.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
