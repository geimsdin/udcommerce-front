@if (isset($breadcrumb) && is_array($breadcrumb) && count($breadcrumb) > 0)
    <div id="breadcrumb" class="container mx-auto my-4 px-4">
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb flex items-center gap-2 text-sm">
                @foreach ($breadcrumb as $label => $url)
                    @if ($url !== '')
                        <li class="breadcrumb-item">
                            <a href="" class="text-blue-600 hover:text-blue-800">{{ $label }}</a>
                        </li>
                        @if (!$loop->last)
                            <li class="breadcrumb-separator text-gray-400">&gt;</li>
                        @endif
                    @else
                        <li class="breadcrumb-item active text-gray-500" aria-current="page">
                            {{ $label }}
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endif