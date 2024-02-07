<?php

use App\Exports\ContactsExport;
use App\Imports\ContactsImport;
use App\Models\Contact;
use App\Models\Spreadsheet;
use Maatwebsite\Excel\Facades\Excel;

it('must export a contact spreadsheet in storage path', function () {
    $file = 'example.xlsx';
    $contacts = Contact::factory(10)->make();
    $export = new ContactsExport($contacts);
    Excel::fake();
    Excel::store($export, $file);
    Excel::assertStored($file);
});

it('must import a contact spreadsheet', function () {
    $beforeImport = Contact::count();
    $count = 10;
    $file = 'export_example.xlsx';
    $contacts = Contact::factory($count)->make();
    $export = new ContactsExport($contacts);
    Excel::store($export, $file);
    $import = new ContactsImport(Spreadsheet::factory()->create([
        'path' => $file
    ]));
    Excel::import($import, $file);
    $this->assertDatabaseCount(Contact::class, $count + $beforeImport);
    unlink(storage_path("app/{$file}"));
});

it('must import a contact spreadsheet using queue', function () {
    $count = 10;
    $file = 'export_example.xlsx';
    $contacts = Contact::factory($count)->make();
    $export = new ContactsExport($contacts);
    Excel::store($export, $file);
    Excel::fake();
    $import = new ContactsImport(Spreadsheet::factory()->create([
        'path' => $file
    ]));
    Excel::queueImport($import, $file);
    Excel::assertQueued($file);
    unlink(storage_path("app/{$file}"));
});
