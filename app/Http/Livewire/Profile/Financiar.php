<?php

namespace App\Http\Livewire\Profile;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use DateTime;

class Financiar extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    public $conditions = [];

    protected $listeners = [];

    public function mount(Request $request)
    {
        $this->conditions = $request->input();
    }

    public function getUserProperty()
    {
        return auth()->user();
    }

    public function render()
    {
        $invoices = Invoice::where('user_id', auth()->id());
        if($this->conditions != []) {
            if(isset($this->conditions['from']) && $this->conditions['from'] != "")
            {
                $invoices->whereDate('invoices.payed_on', '>=', DateTime::createFromFormat('d/m/Y', $this->conditions['from'])->format('Y-m-d'));
            }
            if(isset($this->conditions['to']) && $this->conditions['to'] != "")
            {
                $invoices->whereDate('invoices.payed_on', '<=', DateTime::createFromFormat('d/m/Y', $this->conditions['to'])->format('Y-m-d'));
            }
        }
        $invoices = $invoices->orderByDesc('created_at')->paginate(10);
        return view('profile.financiar', [
            'condtitions' => $this->conditions,
            'invoices' => $invoices->appends(request()->query()),
        ]);
    }
}
