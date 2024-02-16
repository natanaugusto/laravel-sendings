<?php

use App\Exports\ContactsExport;
use App\Jobs\EnqueueSpreadsheetImportJob;
use App\Models\Contact;
use App\Models\File;
use App\Models\User;
use App\Models\Spreadsheet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

it('has index page to list spreadsheets', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('spreadsheets.index'));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Spreadsheets')
            ->has('spreadsheets')
            ->where('spreadsheets', Spreadsheet::paginate()->toArray())
    );
});

// I could not do the Queue assert for EnqueueSpreadsheetImportJob on
// this test. May be is something related to Queue::fake
// #TODO: try with Job Chains https://laravel.com/docs/10.x/queues#testing-job-chains
it('must upload a spreadsheet', function () {
    $user = User::factory()->create();
    $file = 'example.xlsx';
    $contacts = Contact::factory(10)->make();
    $export = new ContactsExport($contacts);
    Excel::store($export, $file);
    $exampleFile = storage_path("app/{$file}");
    Storage::fake(Spreadsheet::getStorageDisk());
    Excel::fake();
    $response = $this
        ->actingAs($user)
        ->post(route('spreadsheets.store'), [
            'file' => UploadedFile::fake()
                ->createWithContent($file, file_get_contents($exampleFile))
        ]);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page->component('Spreadsheets')
    );
    $uploadFileName = now()->format('YmdHi') . "_{$file}";
    $this->assertDatabaseHas(Spreadsheet::class, [
        'user_id' => $user->id,
        'name' => $uploadFileName
    ]);
    $this->assertDatabaseHas(File::class, [
        'user_id' => $user->id,
        ['path', 'like', "%/{$uploadFileName}"]
    ]);
    Excel::assertQueued($uploadFileName, Spreadsheet::getStorageDisk());
    Storage::disk(Spreadsheet::getStorageDisk())->assertExists($uploadFileName);
    unlink($exampleFile);
});

it('must fails if the file was not sended', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->post(route('spreadsheets.store'), ['file' => null]);
    $response->assertRedirect()->withErrors(['file']);
});


it('must dispatch an EnqueueSpreadsheetImportJob', function () {
    $user = User::factory()->create();
    $file = 'example.xlsx';
    $contacts = Contact::factory(10)->make();
    $export = new ContactsExport($contacts);
    Excel::store($export, $file);
    $exampleFile = storage_path("app/{$file}");
    Queue::fake(EnqueueSpreadsheetImportJob::class);
    $response = $this
        ->actingAs($user)
        ->post(route('spreadsheets.store'), [
            'file' => UploadedFile::fake()
                ->createWithContent($file, file_get_contents($exampleFile))
        ]);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page->component('Spreadsheets')
    );
    $uploadFileName = now()->format('YmdHi') . "_{$file}";
    Queue::assertPushedOn(
        Spreadsheet::getQueueConnection(),
        EnqueueSpreadsheetImportJob::class,
        function (EnqueueSpreadsheetImportJob $job) use ($uploadFileName) {
            return $job->spreadsheet->name === $uploadFileName;
        }
    );
    unlink(storage_path("app/{$file}"));
});
