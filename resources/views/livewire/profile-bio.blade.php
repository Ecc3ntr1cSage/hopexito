<div class="settings-panel">
    <form wire:submit="updateBio" class="settings-form">
        <div class="settings-form-intro">
            <span class="settings-form-kicker">Public profile</span>
            <span class="settings-form-status"><i></i> Visible when written</span>
        </div>

        <label class="settings-field settings-field-wide">
            <span class="settings-field-label">Bio</span>
            <textarea id="bio" wire:model="bio" maxlength="750" rows="6" placeholder="Describe the point of view behind your work."></textarea>
            @error('bio') <span class="settings-field-error">{{ $message }}</span> @enderror
        </label>

        <div class="settings-form-footer">
            <x-jet-action-message on="saved">Bio updated.</x-jet-action-message>
            <button class="settings-button settings-button-primary" wire:loading.attr="disabled" type="submit">
                <span>Save bio</span><b aria-hidden="true">&rarr;</b>
            </button>
        </div>
    </form>
</div>
