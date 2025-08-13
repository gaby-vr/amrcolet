<?php

namespace App\Http\Livewire\Profile;

use Illuminate\Http\Request;
use Livewire\Component;

class Invoice extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    protected $listeners = [];

    public function mount()
    {
        $this->state = auth()->user()->invoiceInfo();
    }

    public function getUserProperty()
    {
        return auth()->user();
    }

    public function render()
    {
        return view('profile.invoice');
    }
}
