<?php

namespace App\Imports;

use App\Enums\IncreaseType;
use App\Models\Contact;
use App\Models\Spreadsheet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Throwable;

class ContactsImport implements ToModel, ShouldQueue, WithChunkReading
{
    public function __construct(protected Spreadsheet $spreadsheet)
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            Contact::create([
                'spreadsheet_id' => $this->spreadsheet->id,
                'name' => $row[0],
                'email' => $row[1],
                'phone' => empty($row[2]) ? null : (string)$row[2],
                'document' => empty($row[3]) ? null : (string)$row[3],
            ]);
            $this->spreadsheet->increase();
        } catch (Throwable  $e) {
            Log::error($e);
            $this->spreadsheet->increase(IncreaseType::FAILS);
        }
    }

    public function chunkSize(): int
    {
        return config('excel.exports.chunk_size');
    }
}
