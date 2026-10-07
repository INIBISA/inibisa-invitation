<?php

namespace App\Imports;

use App\Models\Invitation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuestsImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public int $duplicates = 0;

    public int $invalid = 0;

    public function __construct(private readonly Invitation $invitation) {}

    /** @param Collection<int, Collection<string, mixed>> $rows */
    public function collection(Collection $rows): void
    {
        $knownNumbers = $this->invitation->guests()
            ->whereNotNull('whatsapp')
            ->pluck('whatsapp')
            ->mapWithKeys(fn (string $number): array => [$this->normalizeWhatsapp($number) => true])
            ->all();

        // ponytail: synchronous imports stop at 1,000 rows; queue chunks if larger files become necessary.
        foreach ($rows->take(1000) as $row) {
            $name = trim((string) $row->get('nama', ''));
            $whatsapp = $this->normalizeWhatsapp((string) $row->get('whatsapp', ''));
            $validator = Validator::make(
                ['name' => $name, 'whatsapp' => $whatsapp ?: null],
                ['name' => ['required', 'string', 'max:150'], 'whatsapp' => ['nullable', 'regex:/^62[0-9]{8,13}$/']],
            );

            if ($validator->fails()) {
                $this->invalid++;

                continue;
            }

            if ($whatsapp !== '' && isset($knownNumbers[$whatsapp])) {
                $this->duplicates++;

                continue;
            }

            $this->invitation->guests()->create(['name' => $name, 'whatsapp' => $whatsapp ?: null]);
            $this->imported++;

            if ($whatsapp !== '') {
                $knownNumbers[$whatsapp] = true;
            }
        }

        if ($rows->count() > 1000) {
            $this->invalid += $rows->count() - 1000;
        }
    }

    private function normalizeWhatsapp(string $number): string
    {
        $number = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($number, '0')) {
            return '62'.substr($number, 1);
        }

        return $number;
    }
}
