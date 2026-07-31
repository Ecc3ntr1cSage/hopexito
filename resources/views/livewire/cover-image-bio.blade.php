<div class="settings-panel">
    <div class="settings-cover-preview">
        <div class="settings-cover-art">
            @if (Auth::user()->profile?->cover_image)
                <img src="{{ asset('storage/cover-image/' . Auth::user()->profile->cover_image) }}" alt="Current profile cover image">
            @else
                <div class="settings-cover-placeholder"><span>HX</span></div>
            @endif
            <span class="settings-cover-overlay" aria-hidden="true"></span>
            <span class="settings-cover-meta">Public profile / cover image</span>
        </div>
    </div>

    <div class="settings-cover-grid">
        <form method="POST" action="{{ route('upload.cover') }}" enctype="multipart/form-data" class="settings-cover-upload">
            @csrf
            <span class="settings-field-label">Cover image</span>
            <p>Set the atmosphere before anyone reads a word.</p>
            <input type="file" id="cover-image" name="cover_image" wire:model.defer="cover_image" class="settings-file-input">
            <button class="settings-button settings-button-quiet" type="submit"><span>Upload cover</span><b aria-hidden="true">&uarr;</b></button>
        </form>

        <div class="settings-bio-editor">
            <label class="settings-field">
                <span class="settings-field-label">Public bio</span>
                <textarea id="bio" name="bio" rows="5" wire:model.defer="bio" maxlength="750" placeholder="Describe the point of view behind your work."></textarea>
            </label>
            <button class="settings-button settings-button-primary" type="button" wire:click="updateBio">
                <span>Save bio</span><b aria-hidden="true">&rarr;</b>
            </button>
        </div>
    </div>

    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script>
        FilePond.registerPlugin(FilePondPluginImagePreview);
        const fileInput = document.querySelector('input[id="cover-image"]');
        if (fileInput) {
            FilePond.create(fileInput);
            FilePond.setOptions({
                server: {
                    url: '{{ route('upload') }}',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }
            });
        }
    </script>
</div>
