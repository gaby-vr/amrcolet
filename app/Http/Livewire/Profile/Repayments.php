<?php

namespace App\Http\Livewire\Profile;

use App\Models\Repayment;
use Illuminate\Http\Request;
use Livewire\Component;
use DateTime;

class Repayments extends Component
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
        $repayments = Repayment::where('user_id', auth()->id())->where('awb', '<>', '0');
        if($this->conditions != [] && count($this->conditions) > 1) {
            if(isset($this->conditions['from']) && $this->conditions['from'] != "")
            {
                $repayments->whereDate('repayments.date_order', '>=', DateTime::createFromFormat('d/m/Y', $this->conditions['from'])->format('Y-m-d'));
            }
            if(isset($this->conditions['to']) && $this->conditions['to'] != "")
            {
                $repayments->whereDate('repayments.date_order', '<=', DateTime::createFromFormat('d/m/Y', $this->conditions['to'])->format('Y-m-d'));
            }
            if(isset($this->conditions['repayment_status']) && $this->conditions['repayment_status'] != "")
            {
                switch ($this->conditions['repayment_status']) {
                    case '1':
                        $repayments->whereNotNull('repayments.date_delivered');
                        break;
                    case '2':
                        $repayments->whereNull('repayments.date_delivered');
                        break;
                    case '3':
                        break;
                    case '4':
                        $repayments->whereNotNull('repayments.date_delivered')->where('type', '3');
                        break;
                    default:
                        break;
                }
            }
        }
        $clone = clone $repayments;
        // $total = $clone->sum('total');
        // $nelivrate = $clone->whereNull('date_delivered')->sum('total');
        $repayments = $repayments->orderByDesc('created_at')->paginate(10);
        // $repayments->appends(request()->query());
        // dd($clone);
        // $seeQuery = $clone->whereNull('date_delivered')->where('awb', function($query) {
        //     $query->select('api_shipment_awb')
        //             ->from('livrari')
        //             ->whereIn('livrari.status', ['0','4'])
        //             ->whereColumn('livrari.api_shipment_awb', 'repayments.awb');
        // });
        // dd(\Str::replaceArray('?', $seeQuery->getBindings(), $seeQuery->toSql()));
        return view('profile.repayments', [
            'condtitions' => $this->conditions,
            'repayments' => $repayments->appends(request()->query()),
            'total' => round($clone->where('awb', function($query) {
                $query->select('api_shipment_awb')
                        ->from('livrari')
                        ->where('livrari.status', '<>', '5')
                        ->whereColumn('livrari.api_shipment_awb', 'repayments.awb');
            })->sum('total'), 2),
            'platite' => round($clone->whereNotNull('date_delivered')->sum('total'), 2),
            'nelivrate' => round($clone->whereNull('date_delivered')->where('awb', function($query) {
                $query->select('api_shipment_awb')
                        ->from('livrari')
                        ->whereNotIn('livrari.status', ['1','5'])
                        ->whereColumn('livrari.api_shipment_awb', 'repayments.awb');
            })->sum('total'), 2),
            'laAMR' => round($clone->whereNotNull('date_delivered')->where('type', '3')->sum('total'), 2),
        ]);
    }
}
