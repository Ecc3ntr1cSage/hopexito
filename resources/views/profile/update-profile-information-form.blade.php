<div class="settings-panel">
    <form wire:submit.prevent="updateProfileInformation" enctype="multipart/form-data" class="settings-form">
        <div class="settings-form-intro">
            <span class="settings-form-kicker">Account details</span>
            <span class="settings-form-status"><i></i> Synced profile</span>
        </div>

        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }" class="settings-avatar-editor">
                <input type="file" class="hidden" wire:model="photo" x-ref="photo"
                    x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => { photoPreview = e.target.result; };
                        reader.readAsDataURL($refs.photo.files[0]);
                    " />
                <div class="settings-avatar-preview" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}">
                </div>
                <div class="settings-avatar-preview" x-show="photoPreview" x-cloak>
                    <span x-bind:style="'background-image: url(\'' + photoPreview + '\');'"></span>
                </div>
                <div class="settings-avatar-copy">
                    <span class="settings-field-label">Profile image</span>
                    <p>Use a clear mark that feels like you.</p>
                    <div class="settings-avatar-actions">
                        <button type="button" class="settings-button settings-button-quiet" x-on:click.prevent="$refs.photo.click()">Change image</button>
                        @if ($this->user->profile_photo_path)
                            <button type="button" class="settings-text-button" wire:click="deleteProfilePhoto">Remove</button>
                        @endif
                    </div>
                    <x-jet-input-error for="photo" />
                </div>
            </div>
        @endif

        <div class="settings-fields-grid">
            <label class="settings-field settings-field-wide">
                <span class="settings-field-label">Display name</span>
                <x-jet-input id="name" type="text" wire:model.defer="state.name" autocomplete="name" />
                <x-jet-input-error for="name" />
            </label>

            <label class="settings-field settings-field-wide">
                <span class="settings-field-label">Email address</span>
                <x-jet-input id="email" type="email" wire:model.defer="state.email" disabled autocomplete="email" />
                <x-jet-input-error for="email" />
            </label>

            <label class="settings-field">
                <span class="settings-field-label">Phone number</span>
                <span class="settings-phone-field"><b>+60</b><x-jet-input id="phone" type="text" wire:model.defer="state.phone" autocomplete="tel" /></span>
                <x-jet-input-error for="phone" />
            </label>

            <label class="settings-field">
                <span class="settings-field-label">Postcode</span>
                <x-jet-input id="postcode" type="text" wire:model.defer="state.postcode" autocomplete="postal-code" />
                <x-jet-input-error for="postcode" />
            </label>

            <label class="settings-field settings-field-wide">
                <span class="settings-field-label">Address</span>
                <x-jet-input id="address" type="text" wire:model.defer="state.address" autocomplete="street-address" />
                <x-jet-input-error for="address" />
            </label>

            <label class="settings-field settings-field-wide">
                <span class="settings-field-label">State</span>
                <select id="state" wire:model.defer="state.state" class="settings-select">
                    <option value="">Choose a state</option>
                    <option value="Johor">Johor</option>
                    <option value="Kedah">Kedah</option>
                    <option value="Kelantan">Kelantan</option>
                    <option value="Melaka">Melaka</option>
                    <option value="Negeri Sembilan">Negeri Sembilan</option>
                    <option value="Pahang">Pahang</option>
                    <option value="Perak">Perak</option>
                    <option value="Perlis">Perlis</option>
                    <option value="Pulau Pinang">Pulau Pinang</option>
                    <option value="Selangor">Selangor</option>
                    <option value="Terengganu">Terengganu</option>
                    <option value="Kuala Lumpur">Kuala Lumpur</option>
                    <option value="Putrajaya">Putrajaya</option>
                    <option value="Sarawak">Sarawak</option>
                    <option value="Sabah">Sabah</option>
                    <option value="Labuan">Labuan</option>
                </select>
                <x-jet-input-error for="state" />
            </label>
        </div>

        <div class="settings-form-footer">
            <x-jet-action-message on="saved">Saved to your account.</x-jet-action-message>
            <button class="settings-button settings-button-primary" wire:loading.attr="disabled" wire:target="photo" type="submit">
                <span>Save identity</span><b aria-hidden="true">&rarr;</b>
            </button>
        </div>
    </form>
</div>
