<?php

namespace App\Exports;

use App\Models\InvoiceSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportInvoiceSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
	protected $invoice_sheet;

    public function __construct(InvoiceSheet $invoiceSheet)
    {
        $this->invoice_sheet = $invoiceSheet;
    }

    public function collection()
    {
        return $this->invoice_sheet->sheetAwbs()->with('order')->orderBy('order_created_at')->get();
    }

    public function map($invoiceSheetAwb): array
    {
        return [
            $invoiceSheetAwb->awb ?? $invoiceSheetAwb->optional_product,
            $invoiceSheetAwb->sender_name,
            $invoiceSheetAwb->receiver_name,
            $invoiceSheetAwb->order_created_at,
            $invoiceSheetAwb->payment,
            $invoiceSheetAwb->order ? $invoiceSheetAwb->order->status_text : ''
        ];
    }

    public function headings(): array
    {
        return [
            'AWB/Descriere',
            'Nume expeditor',
            'Nume destinatar',
            'Data creare livrare',
            'Cost expediere',
            'Status',
        ];
    }
}