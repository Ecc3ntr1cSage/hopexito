@inject('carbon', 'Carbon\Carbon')

<section class="wallet-component" x-data="{ modal: false, nav: 1 }">
    <div class="wallet-heading">
        <div>
            <span class="wallet-kicker"><i></i> Creator economics / live ledger</span>
            <h2>Turn momentum<br><em>into movement.</em></h2>
            <p>Every sale keeps a trace. Watch your commissions build, then move them when the timing feels right.</p>
        </div>
        <div class="wallet-live-mark" aria-label="Wallet status active"><span></span> Active wallet</div>
    </div>

    <div class="wallet-overview-grid">
        <div class="wallet-balance-shell">
            <div class="wallet-balance-card">
                <div class="wallet-balance-topline">
                    <span>Available balance</span>
                    <span class="wallet-balance-code">RM / 01</span>
                </div>
                <div class="wallet-balance-value">RM {{ number_format($wallet->balance, 2) }}</div>
                <div class="wallet-balance-orbit" aria-hidden="true"><span></span><b>HX</b></div>
                <div class="wallet-balance-footer">
                    <div><span>Withdrawal status</span><strong>{{ $wallet->status == 2 ? 'On hold' : 'Ready when you are' }}</strong></div>
                    <button type="button" class="wallet-outline-button" @click="modal = true" aria-haspopup="dialog">
                        <span>Bank details</span><b aria-hidden="true">&nearr;</b>
                    </button>
                </div>
            </div>
        </div>

        <div class="wallet-action-stack">
            <div class="wallet-stat-grid">
                <div class="wallet-stat-card">
                    <span>Lifetime commission</span>
                    <strong>RM {{ number_format($wallet->commission, 2) }}</strong>
                    <small>All creator earnings</small>
                </div>
                <div class="wallet-stat-card wallet-stat-card-accent">
                    <span>Available to move</span>
                    <strong>{{ $wallet->balance > 50 ? 'Ready' : 'Building' }}</strong>
                    <small>Minimum request RM 10</small>
                </div>
            </div>

            <div class="wallet-withdraw-card">
                <div class="wallet-card-label"><span>Request a withdrawal</span><b>RM 10 min.</b></div>
                <div class="wallet-withdraw-form">
                    <label for="wallet-withdrawal-amount">Amount to move</label>
                    <div class="wallet-amount-field"><span>RM</span><input id="wallet-withdrawal-amount" type="number" min="10" step="0.01" wire:model.defer="withdrawal_amount" placeholder="0.00"></div>
                    @error('withdrawal_amount') <p class="wallet-error">{{ $message }}</p> @enderror
                    @if ($wallet->status != 2)
                        <button type="button" class="wallet-primary-button" wire:click="withdrawalRequest('{{ $wallet->user_id }}')" wire:loading.attr="disabled" wire:target="withdrawalRequest">
                            <span wire:loading.remove wire:target="withdrawalRequest">Request transfer</span>
                            <span wire:loading wire:target="withdrawalRequest">Processing...</span>
                            <b aria-hidden="true">&rarr;</b>
                        </button>
                    @else
                        <p class="wallet-hold-note">Your latest request is being reviewed. New withdrawals will reopen after approval.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="wallet-history-panel" aria-label="Wallet history">
        <div class="wallet-history-header">
            <div>
                <span class="wallet-kicker"><i></i> Activity stream</span>
                <h3>Where the balance came from.</h3>
            </div>
            <div class="wallet-tabs" role="tablist" aria-label="Wallet history tabs">
                <button type="button" role="tab" :aria-selected="nav === 1" :class="{ 'is-active': nav === 1 }" @click="nav = 1">Withdrawals</button>
                <button type="button" role="tab" :aria-selected="nav === 2" :class="{ 'is-active': nav === 2 }" @click="nav = 2">Income</button>
            </div>
        </div>

        <div x-show="nav === 1" x-transition.opacity class="wallet-history-view">
            <div class="wallet-table-heading wallet-withdrawal-table">
                <span>Old balance</span><span>Amount</span><span>New balance</span><span>Status</span><span>Updated</span>
            </div>
            @forelse ($withdrawal as $item)
                <div class="wallet-table-row wallet-withdrawal-table">
                    <span>RM {{ number_format($item->balance, 2) }}</span>
                    <strong>RM {{ number_format($item->withdrawal, 2) }}</strong>
                    <span>RM {{ number_format($item->new_balance, 2) }}</span>
                    <span class="wallet-status wallet-status-{{ $item->status == 1 ? 'pending' : 'approved' }}">{{ $item->status == 1 ? 'Pending' : 'Approved' }}</span>
                    <time datetime="{{ $item->updated_at->toIso8601String() }}">{{ $carbon::parse($item->updated_at)->format('M d, Y') }}</time>
                </div>
            @empty
                <div class="wallet-empty"><span>01</span><p>No withdrawals yet. Your first transfer will appear here.</p></div>
            @endforelse
            <div class="wallet-pagination">{{ $withdrawal->links() }}</div>
        </div>

        <div x-cloak x-show="nav === 2" x-transition.opacity class="wallet-history-view">
            <div class="wallet-table-heading wallet-income-table">
                <span>Old balance</span><span>Income</span><span>New balance</span><span>Type</span><span>Updated</span>
            </div>
            @forelse ($income as $item)
                <div class="wallet-table-row wallet-income-table">
                    <span>RM {{ number_format($item->balance, 2) }}</span>
                    <strong>RM {{ number_format($item->income, 2) }}</strong>
                    <span>RM {{ number_format($item->new_balance, 2) }}</span>
                    <span class="wallet-status wallet-status-income">{{ $item->status == 3 ? 'Commission' : 'Bonus' }}</span>
                    <time datetime="{{ $item->updated_at->toIso8601String() }}">{{ $carbon::parse($item->updated_at)->format('M d, Y') }}</time>
                </div>
            @empty
                <div class="wallet-empty"><span>02</span><p>No income has landed yet. Publish something worth finding.</p></div>
            @endforelse
            <div class="wallet-pagination">{{ $income->links() }}</div>
        </div>
    </section>

    <div x-cloak x-show="modal" x-transition.opacity class="wallet-modal" role="dialog" aria-modal="true" aria-labelledby="wallet-modal-title" @keydown.escape.window="modal = false">
        <div class="wallet-modal-scrim" @click="modal = false"></div>
        <div class="wallet-modal-card" @click.stop>
            <div class="wallet-modal-header">
                <div><span class="wallet-kicker"><i></i> Settlement profile</span><h3 id="wallet-modal-title">Where should it land?</h3></div>
                <button type="button" class="wallet-modal-close" @click="modal = false" aria-label="Close bank details">&times;</button>
            </div>
            <p class="wallet-modal-copy">Keep your payout details current so a successful request never has to wait on a follow-up.</p>
            <div class="wallet-modal-fields">
                <label class="wallet-modal-field"><span>Account holder</span><input type="text" wire:model.defer="bank_holder_name"></label>
                @error('bank_holder_name') <p class="wallet-error">{{ $message }}</p> @enderror
                <label class="wallet-modal-field"><span>Bank name</span><input type="text" wire:model.defer="bank_name"></label>
                @error('bank_name') <p class="wallet-error">{{ $message }}</p> @enderror
                <label class="wallet-modal-field"><span>Account number</span><input type="text" inputmode="numeric" wire:model.defer="bank_account_number"></label>
                @error('bank_account_number') <p class="wallet-error">{{ $message }}</p> @enderror
            </div>
            <button type="button" class="wallet-primary-button" wire:click="updateBankAccountDetails('{{ $wallet->user_id }}')" wire:loading.attr="disabled" wire:target="updateBankAccountDetails">
                <span wire:loading.remove wire:target="updateBankAccountDetails">Save bank details</span>
                <span wire:loading wire:target="updateBankAccountDetails">Saving...</span>
                <b aria-hidden="true">&rarr;</b>
            </button>
        </div>
    </div>
</section>
