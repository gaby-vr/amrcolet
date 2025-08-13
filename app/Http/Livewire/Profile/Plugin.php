<?php

namespace App\Http\Livewire\Profile;

use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use DB;

class Plugin extends Component
{

    public $active;
    public $domain;
    public $api_key;

    protected $listeners = [];

    public function mount()
    {
        foreach(auth()->user()->getMetas('wordpress_') as $key => $value) {
            $this->{$key} = $value;
        }
        // $this->state = auth()->user()->getMetas('wordpress_');
        $this->api_key = $this->api_key ?? self::randomKey();
    }

    public function randomKey()
    {
        $user = auth()->user();
        if($user->meta('wordpress_api_key') == '') {
            do {
                $str = Str::random(24);
            } while($user->metas()->where('name','wordpress_api_key')->where('value', $str)->count() > 0);
            return $str;
        } else {
            return '';
        }
    }

    public function getUserProperty()
    {
        return auth()->user();
    }

    public function update()
    {
        $user = auth()->user();
        $attributes = $this->validate([
            'active' => ['nullable', 'boolean', 'exclude_unless:active,1'],
            'domain' => ['nullable', 'required_if:active,1', 'exclude_unless:active,1', 'max:255'],
            'api_key' => ['nullable', 'required_if:active,1', 'exclude_unless:active,1', 'max:24', 'min:24', 
                // Rule::unique('user_metas', 'value')->ignore($user->metas()->where('name','wordpress_api_key')->first()->id ?? '')
                'unique:user_metas,value,'.($user->metas()->where('name','wordpress_api_key')->first()->id ?? '')
            ],
        ],[],[
            'active' => 'folosesc plugin',
            'domain' => 'domeniul',
            'api_key' => 'cheia api',
        ]);

        $user->unsetMetas('wordpress_');
        if(isset($attributes['active'])) {
            $user->setMetas([
                'active' => '1',
                'domain' => $attributes['domain'],
                'api_key' => $attributes['api_key']
            ], 'wordpress_');
        } else {
            $this->reset();
            $this->api_key = self::randomKey();
        }
        $this->emit('saved');
    }

    public function render()
    {
        return view('profile.plugin');
    }
}
