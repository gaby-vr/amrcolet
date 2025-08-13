<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    public $title;

    public $itemId;

    public $section;

    public $subsection;

    public $data;

    public function mount(Request $request, $id = null)
    {   
        $sections = $this->getSectionName();
        $this->itemId = $id;
        $this->section = $sections[0];
        $this->subsection = $sections[1];
        $this->fill([
            'state' => Auth::user()->withoutRelations()->toArray(),
            'title' => $this->getSectionTitle($sections[0]),
        ]);
    }

    public function getSectionName()
    {
        $sections = explode(".", Route::currentRouteName());
        return [$sections[1] , $sections[count($sections) - 1]];
    }

    public function getSectionTitle($section)
    {
        switch ($section) {
            case 'invoice':
                $title = __('Date de facturare');
                break;
            case 'settings':
                switch ($this->subsection) {
                    case 'repayment':
                        $title = __('Setari').' <small>></small> '.__('Rambursuri');
                        break;
                    case 'security':
                        $title = __('Setari').' <small>></small> '.__('Securitate');
                        break;
                    case 'notifications':
                        $title = __('Setari').' <small>></small> '.__('Notificari');
                        break;
                    case 'print':
                        $title = __('Setari').' <small>></small> '.__('Printare');
                        break;
                    case 'schedule':
                        $title = __('Setari').' <small>></small> '.__('Program');
                        break;
                }
                break;
            case 'addresses':
                $title = __('Adrese');
                break;
            case 'purse':
                $title = __('Plata in avans');
                break;
            case 'invoices':
                $title = __('Lista facturi');
                break;
            case 'orders':
                $title = __('Lista comenzi');
                break;
            case 'financiar':
                $title = __('Financiar');
                break;
            case 'wordpress':
                $title = __('Wordpress');
                break;
            default:
                $title = '';
                break;
        }
        return $title;
    }

    public function render()
    {
        return view('dashboard');
    }
}
