<?php

namespace App\Exports;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ContactsExport implements FromCollection
{
    public function __construct(protected Collection $contacts)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->contacts->map(function (Contact $contact) {
            return collect($contact)->except('spreadsheet_id')->all();
        });
    }
}
