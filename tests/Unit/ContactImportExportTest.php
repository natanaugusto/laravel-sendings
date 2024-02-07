<?php

use App\Exports\ContactsExport;
use App\Models\Contact;
use Maatwebsite\Excel\Facades\Excel;

it('must export a spreadsheet in storage path', function () {
    $file = 'example.xlsx';
    $contacts = Contact::factory(10)->make();
    $export = new ContactsExport($contacts);
    Excel::fake();
    Excel::store($export, $file);
    Excel::assertStored($file);
});
