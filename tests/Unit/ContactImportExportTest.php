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
    Excel::store($export, $file, Spreadsheet::STORAGE_DISK);
    Excel::assertStored($file, Spreadsheet::STORAGE_DISK);
});

it('must import a contact spreadsheet', function () {
    $beforeImport = Contact::count();
    $count = 10;
    $file = 'export_example.xlsx';
    $contacts = Contact::factory($count)->make();
    $export = new ContactsExport($contacts);
    Excel::store($export, $file, Spreadsheet::STORAGE_DISK);
    $import = new ContactsImport(Spreadsheet::factory()->create([
        'name' => $file
    ]));
    Excel::import($import, $file, Spreadsheet::STORAGE_DISK);
    $this->assertDatabaseCount(Contact::class, $count + $beforeImport);
    unlink(config('filesystems.disks.spreadsheet.root') . "/{$file}");
});

it('must enqueu the importation a contact spreadsheet', function () {
    $count = 10;
    $file = 'export_example.xlsx';
    $contacts = Contact::factory($count)->make();
    $export = new ContactsExport($contacts);
    Excel::store($export, $file, Spreadsheet::STORAGE_DISK);
    Excel::fake();
    $import = new ContactsImport(Spreadsheet::factory()->create([
        'name' => $file
    ]));
    Excel::queueImport($import, $file, Spreadsheet::STORAGE_DISK);
    Excel::assertQueued($file, Spreadsheet::STORAGE_DISK);
    unlink(config('filesystems.disks.spreadsheet.root') . "/{$file}");
});
