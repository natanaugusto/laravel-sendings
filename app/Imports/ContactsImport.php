<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\Spreadsheet;
use Maatwebsite\Excel\Concerns\ToModel;

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
        return new Contact([
            'spreadsheet_id' => $this->spreadsheet->id,
            'name' => $row[1],
            'email' => $row[2],
            'phone' => $row[3],
            'document' => $row[4],
        ]);
    }
}
