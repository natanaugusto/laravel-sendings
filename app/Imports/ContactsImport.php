<?php

namespace App\Imports;

use App\Enums\IncreaseType;
use App\Models\Contact;
use App\Models\Spreadsheet;
use Maatwebsite\Excel\Concerns\ToModel;
use Throwable;

class ContactsImport implements ToModel
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
            $contact = Contact::create([
                'spreadsheet_id' => $this->spreadsheet->id,
                'name' => $row[0],
                'email' => $row[1],
                'phone' => empty($row[2]) ? null : (string)$row[2],
                'document' => empty($row[3]) ? null : (string)$row[3],
            ]);
            $this->spreadsheet->increase();
            return $contact;
        } catch (Throwable  $e) {
            $this->spreadsheet->increase(IncreaseType::FAILS);
        }
    }
}
