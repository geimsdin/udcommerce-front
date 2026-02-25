<div>
    @if($brandColumns->isNotEmpty())
        <div class="border-t border-gray-200">
            <div class="container mx-auto px-4 py-8">
                <h3 class="text-sm font-semibold text-gray-900 mb-6">Top brand</h3>
                <div class="grid grid-cols-{{ $this->columns }} gap-x-8 gap-y-2 text-sm text-gray-600">
                    @foreach($brandColumns as $column)
                        <div class="space-y-2">
                            @foreach($column as $brand)
                                <a href="#"
                                    class="block hover:text-gray-900">
                                    {{ $brand->name }}
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
