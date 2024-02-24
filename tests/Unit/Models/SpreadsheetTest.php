<?php

use App\Models\User;
use App\Models\File;
use App\Models\Contact;
use App\Models\Spreadsheet;
use App\Enums\IncreaseType;
use App\Exports\ContactsExport;
use App\Models\Contracts\FileableInterface;
use App\Models\Contracts\QueuelableInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

it('must implement App\Models\{Fileable, Queuelable} and interfaces', function () {
    $implementedInterface = class_implements(Spreadsheet::class);
    expect($implementedInterface)->toContain(FileableInterface::class);
    expect($implementedInterface)->toContain(QueuelableInterface::class);
});

it('must create a spreadsheet', function () {
    $data = [
        'user_id' => User::factory()->create()->id,
        'name' => 'filename.xlsx',
    ];
    $spreadsheet = Spreadsheet::create($data)->refresh();
    expect($spreadsheet->name)->toBe($data['name']);
    expect($spreadsheet->rows)->toBe(0);
    expect($spreadsheet->imported)->toBe(0);
    expect($spreadsheet->fails)->toBe(0);
    $this->assertDatabaseHas(Spreadsheet::class, $data);
});

it('must update an existent spreadsheet', function () {
    $spreadsheet = Spreadsheet::factory()->create();
    $data = [
        'name' => 'new_filename.xlsx',
    ];
    $spreadsheet->update($data);
    $this->assertDatabaseHas(Spreadsheet::class, $data);
});

it('must delete an existent spreadsheet', function () {
    $spreadsheet = Spreadsheet::factory()->create();
    $spreadsheet->delete();
    $this->assertDatabaseMissing(Spreadsheet::class, ['id' => $spreadsheet->id]);
});

it('must belongs to an user', function () {
    $spreadsheet = Spreadsheet::factory()->create();
    expect($spreadsheet->user)->toBeInstanceOf(User::class);
});

it('can have many contacts', function () {
    $count = 10;
    $spreadsheet = Spreadsheet::factory()->create();
    Contact::factory($count)->create([
        'spreadsheet_id' => $spreadsheet->id
    ]);
    expect($spreadsheet->contacts->count())->toBe($count);
});

it('must have methods to increase fails and imported values', function () {
    $sheet = Spreadsheet::factory()->create();
    $sheet->increase();
    expect($sheet->imported)->toBe(1);
    $sheet->increase(IncreaseType::FAILS);
    expect($sheet->fails)->toBe(1);
    $sheet->increase(count: 2);
    expect($sheet->imported)->toBe(3);
});

it('must have a file', function () {
    $sheet = Spreadsheet::factory()->create();
    $sheet->file()->create(File::factory()->make()->toArray());
    $sheet->save();
    $sheet->refresh();
    expect($sheet->file)->toBeInstanceOf(File::class);
});

it('must fill the attribute rows with the total of file rows', function () {
    $count = 10;
    $file = 'example.xlsx';
    $contacts = Contact::factory($count)->make();
    $export = new ContactsExport($contacts);
    Excel::store($export, $file, Spreadsheet::getStorageDisk());
    $sheet = Spreadsheet::factory()->create([
        'name' => $file,
    ]);
    $sheet->refresh();
    expect($sheet->rows)->toBe($count);
    unlink(Storage::disk(Spreadsheet::getStorageDisk())->path($file));
});


it('must upload a file', function () {
    $count = 10;
    $file = 'example.xlsx';
    $export = new ContactsExport(Contact::factory($count)->make());
    Excel::store($export, $file);
    $exampleFile = storage_path("app/{$file}");
    Storage::fake(Spreadsheet::getStorageDisk());
    $file = UploadedFile::fake()
        ->createWithContent($file, file_get_contents($exampleFile));
    $spreadsheet = Spreadsheet::create([
        'user_id' => User::factory()->create()->id,
        'name' => Spreadsheet::generateFilename($file),
    ]);
    $spreadsheet->storeFile($file);
    $spreadsheet->save();
    $spreadsheet->refresh();
    Storage::disk(Spreadsheet::getStorageDisk())->assertExists($spreadsheet->name);
    expect($spreadsheet->rows)->toBe($count);
});
