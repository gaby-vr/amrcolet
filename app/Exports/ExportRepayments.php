<?php

namespace App\Exports;

use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportRepayments implements FromArray, WithHeadings
{
	protected $repayments;

    public function __construct(array $repayments)
    {
        $this->repayments = $repayments;
    }

    public function array(): array
    {
        return $this->repayments;
    }

    public function headings(): array
    {
        return [
            'Nr.',
            'AWB',
            'Data comanda',
            'Data livrare',
            'Platitor (nume/oras)',
            'Platitor (adresa)',
            'Titular cont',
            'Cont',
            'Status',
            'Data plata',
            'Suma (lei)',
            'Serie chitanta curier',
            'Data chitanta curier',
        ];
    }
}