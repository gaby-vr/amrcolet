<?php

namespace App\Http\Livewire\Profile;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Livewire\Component;

class Purse extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    protected $listeners = [];

    public function mount() {}

    public function getUserProperty()
    {
        return auth()->user();
    }

    public function render()
    {
        $account_balance = auth()->user()->meta('account_balance');
        return view('profile.purse', [
            'invoices' => Invoice::where('user_id', auth()->id())->has('livrare','0')->notByAdmin()->where('status', '1')->orderByDesc('created_at')->paginate(10),
            'account_balance' => $account_balance,
            'negative_balance' => $account_balance >= 0 || $account_balance == '' ? false : true,
            'has_invoice_info' => auth()->user()->invoiceInfoCount() > 2,
        ]);
    }
}
