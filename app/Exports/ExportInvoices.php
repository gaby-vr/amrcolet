<?php

namespace App\Exports;

use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Invoice;
use Carbon\Carbon;

class ExportInvoices implements WithHeadings, WithMapping, FromCollection, ShouldAutoSize
{
    protected $filters = null;

    public function __construct($filters = null)
    {
        if(!empty($filters)) {
            $this->filters = $filters;
        }
    }

    public function collection()
    {
        if(!empty($this->filters)) {
            $invoices = Invoice::with('user');
            if(!empty($this->filters['from']))
            {
                $invoices->whereDate('invoices.payed_on', '>=', $this->filters['from']);
            }
            if(!empty($this->filters['to']))
            {
                $invoices->whereDate('invoices.payed_on', '<=', $this->filters['to']);
            }
            if(!empty($this->filters['status']))
            {
                $invoices->where('invoices.status', '=', $this->filters['status']);
            }
            if(!empty($this->filters['email']))
            {
                $invoices->whereHas('user', function($query) {
                    $query->where('email', 'like', $this->filters['email'].'%');
                });
            }
            return $invoices->get();
        } else {
            return Invoice::with('user')->get();
        }
    }

    public function headings(): array
    {
        return [
            'Nr.',
            'Denumire',
            'Suma (RON)',
            'Platita pe',
            'Status',
            'Platitor',
            'Email',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->series.$invoice->number,
            $invoice->meta('created_by_admin') == '1' ? __('Creata manual de catre admin') : ( $invoice->livrare_id == 0 ? __('Reincarcare cont cu credite') : __('Comanda').' #'.$invoice->livrare_id),
            $invoice->total,
            Carbon::parse($invoice->payed_on)->format('d/m/Y'),
            $invoice->status_text,
            $invoice->user ? $invoice->user->name : $invoice->meta('client_last_name').' '.$invoice->meta('client_first_name'),
            $invoice->meta('client_email')
        ];
    }
}