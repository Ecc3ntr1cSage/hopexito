<div class="settings-panel">
    <form wire:submit.prevent="store" class="settings-form">
        <div class="settings-form-intro">
            <span class="settings-form-kicker">Signal directory</span>
            <span class="settings-form-status"><i></i> Public when linked</span>
        </div>

        <div class="settings-fields-grid settings-fields-links">
            <label class="settings-field">
                <span class="settings-field-label">Instagram</span>
                <x-jet-input type="text" wire:model.defer="instagram" placeholder="instagram.com/" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">Behance</span>
                <x-jet-input type="text" wire:model.defer="behance" placeholder="behance.net/" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">Website</span>
                <x-jet-input type="text" wire:model.defer="website" placeholder="yourstudio.com" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">TikTok</span>
                <x-jet-input type="text" wire:model.defer="tiktok" placeholder="tiktok.com/" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">Twitter / X</span>
                <x-jet-input type="text" wire:model.defer="twitter" placeholder="x.com/" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">Dribbble</span>
                <x-jet-input type="text" wire:model.defer="dribble" placeholder="dribbble.com/" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">Pinterest</span>
                <x-jet-input type="text" wire:model.defer="pinterest" placeholder="pinterest.com/" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">DeviantArt</span>
                <x-jet-input type="text" wire:model.defer="deviantart" placeholder="deviantart.com/" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">Facebook</span>
                <x-jet-input type="text" wire:model.defer="facebook" placeholder="facebook.com/" />
            </label>
        </div>

        <div class="settings-form-footer">
            <x-jet-action-message on="saved">Links updated.</x-jet-action-message>
            <button class="settings-button settings-button-primary" wire:loading.attr="disabled" type="submit">
                <span>Save links</span><b aria-hidden="true">&rarr;</b>
            </button>
        </div>
    </form>
</div>
