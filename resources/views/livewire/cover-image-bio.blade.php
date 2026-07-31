<div class="p-5 my-5 border-2 border-indigo-500 shadow-md bg-black/50 shadow-rose-500 md:col-span-2 rounded-2xl">
    <x-jet-section-title>
        <x-slot name="title">
            {{ __('Profile Personalization') }}
        </x-slot>
        <x-slot name="description">
            {{ __('Update your profile\'s cover image and bio for a more personalized look.') }}
        </x-slot>
    </x-jet-section-title>
    <div class="flex flex-col py-5 rounded-2xl">
        <form method="POST" action="{{ route('upload.cover') }}" enctype="multipart/form-data" class="">
            @csrf
            <x-jet-label for="cover-image" value="{{ __('Cover Image') }}" />
            @if (Auth::user()->profile?->cover_image)
                <img src="{{ asset('storage/cover-image/' . Auth::user()->profile->cover_image) }}"
                    class="w-full rounded-lg h-48 md:h-72 lg:h-96">
            @endif
            <input type="file" id="cover-image" name="cover_image" wire:model.defer="cover_image"
                class="w-full mt-2">
            <x-jet-button class="float-right my-2">Save</x-jet-button>
        </form>
        <div>
            <x-jet-label for="bio" value="{{ __('Bio') }}" />
            <textarea id="bio" name="bio" rows="8" wire:model.defer="bio" maxlength="750"
                class="mb-4 block p-2.5 w-full caret-teal-500 bg-neutral-800 border border-neutral-500 focus:ring-indigo-500 rounded-md text-white"
                placeholder="Describe yourself"></textarea>
            <x-jet-button type="button" class="float-right my-2" wire:click="updateBio()">Save</x-jet-button>
        </div>
    </div>
    {{-- Include Filepond --}}
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script>
        FilePond.registerPlugin(FilePondPluginImagePreview);
        const fileInput = document.querySelector('input[id="cover-image"]');
        const pond = FilePond.create(fileInput);
        FilePond.setOptions({
            server: {
                url: '{{ route('upload') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });
    </script>
</div>
