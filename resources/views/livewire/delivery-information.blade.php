<div class="checkout-page">
    <x-jet-session-message />

    <header class="checkout-hero">
        <div>
            <div class="checkout-kicker">HopeXito / checkout</div>
            <h1>Make it yours.</h1>
            <p>A few details, then you can review the payment simulation before anything is placed.</p>
        </div>
        <a class="checkout-close-link" href="{{ route('cart.index') }}" aria-label="Return to cart">&times;</a>
    </header>

    <nav class="checkout-progress" aria-label="Checkout progress">
        <span class="is-complete"><b>01</b> Bag</span>
        <i></i>
        <span class="is-active"><b>02</b> Delivery</span>
        <i></i>
        <span><b>03</b> Payment</span>
    </nav>

    <div class="checkout-layout">
        <section class="checkout-form-panel">
            <div class="checkout-panel-heading">
                <span class="checkout-index">02</span>
                <div>
                    <div class="checkout-kicker">Delivery details</div>
                    <h2>Where should we send it?</h2>
                </div>
            </div>

            @if (Auth::check())
                <div class="checkout-profile-note">
                    <span class="checkout-note-mark">i</span>
                    <p>Using the delivery details saved to your profile. <a href="{{ route('profile.show') }}">Edit profile</a></p>
                </div>
            @endif

            <div class="checkout-form-grid">
                <label class="checkout-field checkout-field-wide">
                    <span>Name</span>
                    <input type="text" wire:model.lazy="name" @if(Auth::check()) readonly @endif>
                    @error('name') <small>{{ $message }}</small> @enderror
                </label>
                <label class="checkout-field checkout-field-wide">
                    <span>Email</span>
                    <input type="email" wire:model.lazy="email" @if(Auth::check()) readonly @endif>
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>
                <label class="checkout-field">
                    <span>Phone number</span>
                    <div class="checkout-phone-field">
                        <b>+60</b>
                        <input type="text" wire:model.lazy="phone" @if(Auth::check()) readonly @endif>
                    </div>
                    @error('phone') <small>{{ $message }}</small> @enderror
                </label>
                <label class="checkout-field">
                    <span>Postcode</span>
                    <input type="text" wire:model.lazy="postcode" @if(Auth::check()) readonly @endif>
                    @error('postcode') <small>{{ $message }}</small> @enderror
                </label>
                <label class="checkout-field checkout-field-wide">
                    <span>Street address</span>
                    <input type="text" wire:model.lazy="address" @if(Auth::check()) readonly @endif>
                    @error('address') <small>{{ $message }}</small> @enderror
                </label>
                <label class="checkout-field checkout-field-wide">
                    <span>State</span>
                    <select wire:model.lazy="state" @if(Auth::check()) disabled @endif>
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
                    @error('state') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="checkout-form-footer">
                <a class="checkout-back-link" href="{{ route('cart.index') }}">&larr; Back to bag</a>
                <button class="checkout-primary-action" type="button" wire:click="storeDeliveryInfo" wire:loading.attr="disabled">
                    <span wire:loading.remove>Continue to payment <b>&rarr;</b></span>
                    <span wire:loading>Checking details&hellip;</span>
                </button>
            </div>
        </section>

        <aside class="checkout-aside">
            <div class="checkout-aside-label">Next / payment simulation</div>
            <div class="checkout-orbit" aria-hidden="true"><span></span><b>03</b></div>
            <h2>One last moment of choice.</h2>
            <p>Choose a demo payment method and decide whether this test order succeeds or returns you here for another pass.</p>
            <div class="checkout-aside-rule"></div>
            <div class="checkout-aside-detail"><span>Shipping</span><strong>State-based</strong></div>
            <div class="checkout-aside-detail"><span>Experience</span><strong>Demo only</strong></div>
            <div class="checkout-aside-detail"><span>Support</span><strong>Help available</strong></div>
        </aside>
    </div>
</div>
