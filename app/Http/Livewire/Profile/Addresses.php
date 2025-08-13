<?php

namespace App\Http\Livewire\Profile;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Addresses extends Component
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
        
    }

    public function getUserProperty()
    {
        return auth()->user();
    }

    public function render()
    {
        return view('profile.addresses', [
            'addresses' => Address::where('user_id', auth()->id())->orderByDesc('favorite')->orderBy('address_name')->paginate(10),
        ]);
    }
}
