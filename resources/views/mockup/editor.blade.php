@php
    $mode = 'product';
    $action = route('product.store');
    $frontName = 'image_front';
    $backName = 'image_back';
    $frontPosition = $template->front_position;
    $backPosition = $template->back_position;
    $colorValue = fn ($color) => match (strtolower($color)) {
        'black' => '#111111',
        'gray', 'grey' => '#808080',
        'navy' => '#1f2a44',
        'red' => '#b91c1c',
        'blue' => '#2563eb',
        default => '#ffffff',
    };
    $firstColor = trim($colors[0] ?? 'White');
@endphp

@section('title', $template->category . ' | HopeXito')
<x-app-layout>
    <x-jet-session-message />
    <div class="min-h-screen bg-neutral-900 pb-24 text-white" data-mockup-editor x-data="{ open: false, price: '', accepted: false, preview: false, selectedColor: '{{ $firstColor }}', size: '', quantity: 1 }">
        <div class="mx-auto max-w-7xl px-4 py-6">
            <div class="mb-5 flex flex-wrap items-center gap-2 text-sm">
                <a href="{{ route('product.create') }}" class="rounded-md px-2 py-1 transition hover:bg-indigo-500/50">
                    Product Selection
                </a>
                <span class="text-gray-500">/</span>
                <p class="text-indigo-300">{{ $template->category }}</p>
            </div>

            <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid gap-8 lg:grid-cols-[380px_1fr]">
                @csrf
                <input type="hidden" name="product_type" value="{{ $template->type }}">
                <input type="hidden" name="template_front" value="{{ $template->mockup_image }}">
                <input type="hidden" name="template_back" value="{{ $template->mockup_image_2 }}">
                <input type="hidden" name="preview_color" data-selected-color value="{{ $firstColor }}">
                <input type="hidden" name="front_x" value="{{ $frontPosition['x'] }}">
                <input type="hidden" name="front_y" value="{{ $frontPosition['y'] }}">
                <input type="hidden" name="front_w" value="{{ $frontPosition['w'] }}">
                <input type="hidden" name="front_h" value="{{ $frontPosition['h'] }}">
                <input type="hidden" name="back_x" value="{{ $backPosition['x'] }}">
                <input type="hidden" name="back_y" value="{{ $backPosition['y'] }}">
                <input type="hidden" name="back_w" value="{{ $backPosition['w'] }}">
                <input type="hidden" name="back_h" value="{{ $backPosition['h'] }}">

                <aside class="space-y-5">
                    <div class="space-y-3">
                        <div>
                            <x-jet-label for="front-design" value="{{ __('Front Design') }}" />
                            <input id="front-design" name="{{ $frontName }}" data-design-input="front" type="file" accept="image/*" class="mt-1 block w-full rounded-md border border-neutral-700 bg-neutral-800 p-2 text-sm" required>
                            @error($frontName)<p class="mt-1 text-rose-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <x-jet-label for="back-design" value="{{ __('Back Design') }}" />
                            <input id="back-design" name="{{ $backName }}" data-design-input="back" type="file" accept="image/*" class="mt-1 block w-full rounded-md border border-neutral-700 bg-neutral-800 p-2 text-sm">
                            @error($backName)<p class="mt-1 text-rose-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    @if ($mode === 'product')
                        <div class="space-y-3">
                            <div>
                                <x-jet-label for="title" value="{{ __('Title') }}" />
                                <x-jet-input id="title" name="title" type="text" class="mt-1 block w-full" placeholder="Artwork title" />
                                @error('title')<p class="mt-1 text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <x-jet-label for="tags" value="{{ __('Tags') }}" />
                                <x-jet-input id="tags" name="tags" type="text" class="mt-1 block w-full" placeholder="panda, bear, snake" />
                                @error('tags')<p class="mt-1 text-rose-400">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endif

                    <div>
                        <x-jet-label value="{{ $mode === 'custom' ? __('Choose Color') : __('Available Colors') }}" />
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($colors as $color)
                                @php $color = trim($color); @endphp
                                <button type="button" data-color-swatch="{{ $color }}" data-color-value="{{ $colorValue($color) }}" x-on:click="selectedColor = '{{ $color }}'" class="h-9 w-9 rounded-full border border-white/40" style="background-color: {{ $colorValue($color) }}" title="{{ $color }}"></button>
                                @if ($mode === 'product')
                                    <label class="flex items-center gap-1 rounded-md border border-neutral-700 px-2 py-1 text-xs">
                                        <input type="checkbox" name="color[]" value="{{ $color }}" class="rounded bg-neutral-800" @checked($loop->first)>
                                        {{ $color }}
                                    </label>
                                @endif
                            @endforeach
                        </div>
                        @if ($mode === 'custom')
                            <input type="hidden" name="color" x-bind:value="selectedColor">
                        @endif
                        @error('color')<p class="mt-1 text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    @if ($mode === 'product')
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-md border border-indigo-500 bg-indigo-500/10 p-3">
                                <x-jet-label value="{{ __('Fixed price') }}" />
                                <p class="mt-1 text-xl text-indigo-300">RM{{ number_format($template->min, 2) }}</p>
                                <p class="mt-1 text-xs text-gray-400">15% creator commission on external purchases</p>
                            </div>
                            <div>
                                <x-jet-label for="visibility" value="{{ __('Visibility') }}" />
                                <select id="visibility" name="visibility" class="mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white">
                                    <option value="public" selected>Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="rounded-md border border-neutral-700 p-2 text-center">
                                <input type="radio" name="preview" value="0" class="mr-1" checked>
                                Front preview
                            </label>
                            <label class="rounded-md border border-neutral-700 p-2 text-center">
                                <input type="radio" name="preview" value="1" class="mr-1">
                                Back preview
                            </label>
                        </div>
                        <label class="flex gap-3 text-xs text-gray-300">
                            <input type="checkbox" class="mt-1 rounded bg-neutral-800" x-model="accepted">
                            <span>I have the right to sell products containing this artwork.</span>
                        </label>
                    @else
                        <div class="space-y-4">
                            <div>
                                <x-jet-label value="{{ __('Choose Size') }}" />
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach (['XS', 'S', 'M', 'L', 'XL', '2XL'] as $size)
                                        <label class="rounded-md border border-neutral-700 px-3 py-2 text-sm" :class="size === '{{ $size }}' ? 'border-indigo-400 text-lime-300' : ''">
                                            <input type="radio" name="size" value="{{ $size }}" class="hidden" x-on:click="size = '{{ $size }}'">
                                            {{ $size }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('size')<p class="mt-1 text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <x-jet-label value="{{ __('Quantity') }}" />
                                <div class="mt-2 flex w-40 items-center justify-between rounded-md border border-indigo-500">
                                    <button type="button" class="px-4 py-2" x-on:click="quantity = Math.max(1, quantity - 1)">-</button>
                                    <input type="text" name="quantity" x-model="quantity" class="w-14 border-0 bg-transparent text-center text-white">
                                    <button type="button" class="px-4 py-2" x-on:click="quantity++">+</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <x-jet-button class="w-full py-3" x-bind:disabled="{{ $mode === 'product' ? '!accepted' : 'false' }}">
                        <span class="mx-auto">{{ $mode === 'custom' ? 'Add Custom Product' : 'Save Product' }}</span>
                    </x-jet-button>
                </aside>

                <section class="grid gap-5 xl:grid-cols-2">
                    @foreach (['front' => $frontPosition, 'back' => $backPosition] as $side => $position)
                        <div class="rounded-lg border border-neutral-700 bg-neutral-950/60 p-3">
                            <div class="mb-2 flex items-center justify-between text-sm text-gray-300">
                                <span>{{ ucfirst($side) }}</span>
                                <button type="button" class="rounded-md px-2 py-1 text-indigo-300 hover:bg-indigo-500/20" x-on:click="open = {{ $side === 'back' ? 'true' : 'false' }}">
                                    View {{ $side }}
                                </button>
                            </div>
                            <div data-template-preview class="relative mx-auto aspect-[44/45] max-w-[520px] overflow-hidden rounded-md" style="background-color: {{ $colorValue($firstColor) }}">
                                <img src="{{ asset('' . ($side === 'front' ? $template->mockup_image : $template->mockup_image_2)) }}" alt="{{ $template->category }} {{ $side }}" class="h-full w-full object-contain">
                                <div class="absolute" style="left: {{ ($position['x'] / 880) * 100 }}%; top: {{ ($position['y'] / 900) * 100 }}%; width: {{ ($position['w'] / 880) * 100 }}%; height: {{ ($position['h'] / 900) * 100 }}%;">
                                    <img data-design-preview="{{ $side }}" alt="" class="hidden h-full w-full object-contain">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </section>
            </form>
        </div>
    </div>
</x-app-layout>
