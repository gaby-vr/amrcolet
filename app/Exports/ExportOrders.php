<?php

namespace App\Exports;

use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Livrare;
use Carbon\Carbon;

class ExportOrders implements WithHeadings, WithMapping, FromQuery, ShouldAutoSize
{
    public $from = null;
    public $to = null;
    public $status = null;
    public $awb = null;
    public $receiver_name = null;
    public $owned = null;

    public function __construct($array = [])
    {
        $this->from = $array['from'] ?? now()->subDays(7)->format('Y-m-d');
        $this->to = $array['to'] ?? now()->format('Y-m-d');
        $this->status = $array['status'] ?? null;
        $this->awb = $array['awb'] ?? null;
        $this->receiver_name = $array['receiver_name'] ?? null;
        $this->owned = $array['owned'] ?? null;
    }

    public function query()
    {
        $query = $this->owned ? auth()->user()->orders() : Livrare::query();
        $query->with(['user','invoice','sender','receiver']);
        if($this->from)
        {
            $query->whereDate('livrari.created_at', '>=', $this->from);
        }
        if($this->to)
        {
            $query->whereDate('livrari.created_at', '<=', $this->to);
        }
        if($this->status)
        {
            $query->where('livrari.status', $this->status);
        }
        if($this->awb)
        {
            $query->where('livrari.api_shipment_awb', 'like', $this->awb.'%');
        }
        if($this->receiver_name)
        {
            $query->whereHas('receiver', function ($subquery) {
                $subquery->where('name', 'like', $this->receiver_name.'%');
            });
        }
        return $query;
    }

    public function headings(): array
    {
        return array_merge([
            '#',
            'Total (RON)',
            'Nume client',
            'Email client',
            $this->owned ? 'Telefon destinatar' : 'Tip client',
            'Email expeditor',
            'Data',
            'Curier',
            'Awb',
            'Expeditor',
            'Destinatar',
            'Ramburs',
            'IBAN',
            'Status',
        ], ($this->owned ? [] : [
            'Greutate totala'
        ]));
    }

    public function map($livrare): array
    {
        $user = $livrare->user;
        $invoice = $livrare->invoice;
        $expeditor = $livrare->sender;
        $destinatar = $livrare->receiver;

        try {
            return array_merge([
                $livrare->id,
                $livrare->value,
                $user 
                    ? $user->name 
                    : ($invoice 
                        ? $invoice->meta('client_last_name').' '.$invoice->meta('client_first_name')
                        : ($expeditor->name ?? null)
                    ),
                $user ? $user->email : ($invoice ? $invoice->meta('client_email') : $expeditor->email ?? null),
                $this->owned ? \Str::start($destinatar->phone ?? '', '+') : ($user && $user->role == 2 ? __('Contractant') : __('Normal')),
                $expeditor->email ?? null,
                Carbon::parse($livrare->created_at)->format('d/m/Y'),
                $livrare->curier,
                $livrare->api_shipment_awb,
                $expeditor ? ($expeditor->name.", \r\n".$expeditor->locality) : ("?, \r\n?"),
                $destinatar ? ($destinatar->name.", \r\n".$destinatar->locality) : ("?, \r\n?"),
                $livrare->ramburs_value,
                $livrare->iban ?? null,
                $livrare->status_text
            ], ($this->owned ? [] : [
                $livrare->total_weight ?? ''
            ]));
        } catch(\Exception $e) {
            \Log::info('Export error:');
            \Log::info($e->getMessage());
            \Log::info('ID livrare');
            \Log::info($livrare->id);
        }
        return [];
    }
}