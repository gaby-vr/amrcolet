<?php

namespace App\Http\Livewire\Profile;

use Illuminate\Http\Request;
use Livewire\Component;

class Settings extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    public $section;

    protected $listeners = [];

    public function mount($subsection)
    {
        $this->section = $subsection;
        $this->state = $this->getPageState($subsection);
    }

    public function getPageState($section)
    {
        return auth()->user()->getMetas($section.'_');
    }

    public function getUserProperty()
    {
        return auth()->user();
    }

    public function render()
    {
        return view('profile.settings.'.$this->section);
    }
}
