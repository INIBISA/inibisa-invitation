<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuestsTemplateExport implements FromArray, WithHeadings
{
    /** @return array<int, array<int, string>> */
    public function array(): array
    {
        return [
            ['Budi Santoso', '6281234567890'],
            ['Siti Aminah', '081234567891'],
        ];
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return ['nama', 'whatsapp'];
    }
}
