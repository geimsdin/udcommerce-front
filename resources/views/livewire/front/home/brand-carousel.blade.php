<div class="w-full py-8 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-semibold text-center text-gray-800 mb-8 tracking-wide">BRANDS CAROUSEL</h2>
        <div class="grid grid-cols-5 grid-rows-3 gap-y-8 gap-x-8 place-items-center">
            @foreach($brands as $brand)
                <a href="#" class="flex flex-col items-center group">
                    <div class="w-28 h-20 flex items-center justify-center">
                        <img src="{{ !empty($brand->image) ? $brand->image : 'https://placehold.co/110x64/png?text=Brand' }}" alt="{{ $brand->name }}" class="object-contain max-h-16 max-w-[110px] grayscale group-hover:grayscale-0 transition duration-200 bg-white rounded shadow" loading="lazy">
                    </div>
                    <span class="mt-2 text-xs text-gray-700 group-hover:text-blue-700 text-center">{{ $brand->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
