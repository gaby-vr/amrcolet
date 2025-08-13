<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportBorderouri implements FromArray, WithHeadings, ShouldAutoSize
{
	protected $awbs;

    public function __construct($awbs)
    {
        $this->awbs = $awbs;
    }

    public function array(): array
    {
        return $this->awbs;
    }

    public function headings(): array
    {
        return [
            'AWB',
            'Nume expeditor',
            'Nume destinatar',
            'Data creare livrare',
            'Valoare',
            'IBAN',
            'Titular cont'
        ];
    }
}