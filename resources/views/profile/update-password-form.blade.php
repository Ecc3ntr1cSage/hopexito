<div class="settings-panel settings-panel-compact">
    <form wire:submit.prevent="updatePassword" class="settings-form">
        <div class="settings-form-intro">
            <span class="settings-form-kicker">Password control</span>
            <span class="settings-form-status"><i></i> Protected</span>
        </div>

        <div class="settings-fields-grid settings-fields-security">
            @if (!is_null(Auth::user()->password))
                <label class="settings-field settings-field-wide">
                    <span class="settings-field-label">Current password</span>
                    <x-jet-input id="current_password" type="password" wire:model.defer="state.current_password" autocomplete="current-password" />
                    <x-jet-input-error for="current_password" />
                </label>
            @endif
            <label class="settings-field">
                <span class="settings-field-label">New password</span>
                <x-jet-input id="password" type="password" wire:model.defer="state.password" autocomplete="new-password" />
                <x-jet-input-error for="password" />
            </label>
            <label class="settings-field">
                <span class="settings-field-label">Confirm password</span>
                <x-jet-input id="password_confirmation" type="password" wire:model.defer="state.password_confirmation" autocomplete="new-password" />
                <x-jet-input-error for="password_confirmation" />
            </label>
        </div>

        <div class="settings-form-footer">
            <x-jet-action-message on="saved">Password updated.</x-jet-action-message>
            <button class="settings-button settings-button-primary" wire:loading.attr="disabled" type="submit">
                <span>Update password</span><b aria-hidden="true">&rarr;</b>
            </button>
        </div>
    </form>
</div>
