<?php

namespace App\Http\Livewire;

use App\Models\Wallet as UserWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
// use Illuminate\Support\Facades\Crypt;

class Wallet extends Component
{
    use WithPagination;

    public $bank_holder_name, $bank_name, $bank_account_number, $withdrawal_amount;

    public function updateBankAccountDetails($id)
    {
        $validatedData = $this->validate([
            'bank_holder_name' => 'required|string',
            'bank_name' => 'required|string',
            'bank_account_number' => 'required|numeric',
        ]);
        $wallet = UserWallet::where('user_id', $id)->first();
        $wallet->update([
            'bank_holder_name' => $this->bank_holder_name,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
        ]);

        session()->flash('message', 'Bank Details Updated');
        return redirect()->route('dashboard');
    }

    public function withdrawalRequest($user_id)
    {
        $validatedData = $this->validate([
            'withdrawal_amount' => 'required|numeric|min:10'
        ]);
        $wallet = UserWallet::where('user_id', $user_id)->first();
        if ($wallet->balance <= 50) {
            session()->flash('message', 'Insufficient balance');
            return redirect()->route('dashboard');
        }
        if ($wallet->bank_holder_name == NULL || $wallet->bank_name == NULL || $wallet->bank_account_number == NULL) {
            session()->flash('message', 'Please fill in withdrawal details');
            return redirect()->route('dashboard');
        }

        $new_balance = $wallet->balance - $this->withdrawal_amount;

        WalletTransaction::create([
            'user_id' => $user_id,
            'wallet_id' => $wallet->id,
            'balance' => $wallet->balance,
            'withdrawal' => $this->withdrawal_amount,
            'new_balance' => $new_balance,
            'status' => 1
        ]);
        $wallet->update([
            'status' => 2,
            'balance' => $new_balance
        ]);

        session()->flash('message', 'Request Withdrawal Success');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['id' => (string) Str::uuid(), 'name' => Auth::user()->name, 'commission' => 0, 'balance' => 0, 'status' => 1]
        );
        $withdrawal = $wallet->walletTransaction()
            ->where('user_id', Auth::user()->id)
            ->where(function ($query) {
                $query->where('status', 1)
                      ->orWhere('status', 2);
            })
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);
        $income = $wallet->walletTransaction()
            ->where(function ($query) {
                $query->where('status', 3)
                      ->orWhere('status', 4);
            })
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);
    
        return view('livewire.wallet', compact('wallet', 'withdrawal', 'income'));
    }
}
