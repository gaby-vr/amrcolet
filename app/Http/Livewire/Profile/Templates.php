<?php

namespace App\Http\Livewire\Profile;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Template;
use Livewire\Component;

class Templates extends Component
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
        return view('profile.templates', [
            'templates' => Template::where('user_id', auth()->id())->orderByDesc('favorite')->orderBy('name')->paginate(10),
        ]);
    }
}
