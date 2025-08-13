<?php

namespace App\Http\Livewire\Profile;

use App\Models\Livrare;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Livewire\Component;
use DateTime;

class Orders extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    protected $listeners = [];

    protected $section = null;
    
    protected $itemId = null;
    
    protected $order = null;

    protected $conditions = [];

    public function mount(Request $request, $subsection = null, $id = null)
    {
        $this->conditions = $request->input();
        $this->section = $subsection;
        $this->itemId = $id;
    }

    public function getUserProperty()
    {
        return auth()->user();
    }

    public function render()
    {
        if($this->itemId && $order = auth()->user()->orders()->where('id', $this->itemId)->first()) {
            $order = Livrare::firstWhere('id',$this->itemId);
            return view('profile.orders.show', [
                'order' => $order,
                'sender' => $order->sender,
                'receiver' => $order->receiver,
                'packages' => $order->packages,
                'invoice' => $order->invoice,
            ]);
            
        } else {
            $orders = Livrare::where(function($query) {
                $query->where('user_id', auth()->id());
                $query->orWhere('email', auth()->user()->email);
            });
            if($this->conditions != []) {
                if(!empty($this->conditions['from']))
                {
                    try { 
                        $orders->whereDate('livrari.created_at', '>=', Carbon::createFromFormat('d/m/Y', $this->conditions['from'])->format('Y-m-d'));
                    } catch (\Exception $e) {}
                }
                if(!empty($this->conditions['to']))
                {
                    try { 
                        $orders->whereDate('livrari.created_at', '<=', Carbon::createFromFormat('d/m/Y', $this->conditions['to'])->format('Y-m-d'));
                    } catch (\Exception $e) {}
                }
                if(!empty($this->conditions['status']))
                {
                    $orders->where('livrari.status', $this->conditions['status']);
                }
                if(!empty($this->conditions['awb']))
                {
                    $orders->JoinAwbLables()->where('livrari.api_shipment_awb', 'like', $this->conditions['awb'].'%')
                        ->orWhere('parcel_awb_list', 'like', '%'.$this->conditions['awb'].'%');
                    // $orders->where('livrari.api_shipment_awb', 'like', $this->conditions['awb'].'%');
                }
                if(!empty($this->conditions['receiver_name']))
                {
                    $orders->whereHas('receiver', function (Builder $query) {
                        $query->where('name', 'like', $this->conditions['receiver_name'].'%');
                    });
                }
            }

            $orders->whereNotNull('user_id');
            if ($this->section === 'pending') {
                // Remove manual status filter to avoid conflict
                unset($this->conditions['status']);

                $orders = $orders->where('status', 16)
                                ->orderByDesc('created_at')
                                ->paginate(10);

                return view('profile.orders.pending', [
                    'condtitions' => $this->conditions,
                    'status_list' => Livrare::statusList(),
                    'orders' => $orders,
                ]);
            }

            $orders = $orders->where('status', '!=', 16)->orderByDesc('created_at')->paginate(10);
            return view('profile.orders.orders', [
                'condtitions' => $this->conditions,
                'status_list' => Livrare::statusList(),
                'orders' => $orders->appends(request()->query()),
            ]);
        }
    }
}
