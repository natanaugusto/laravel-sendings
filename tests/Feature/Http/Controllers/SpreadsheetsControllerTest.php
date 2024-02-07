<?php

use App\Models\Contact;
use App\Models\User;
use App\Models\Spreadsheet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
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

it('must upload a spreadsheet', function () {
    $user = User::factory()->create();
    $fileName = 'example.xlsx';
    $exampleFile = storage_path("app/{$fileName}");
    $countContactsBeforeImport = Contact::count();
    Storage::fake('local');
    $response = $this
        ->actingAs($user)
        ->post(route('spreadsheets.store'), [
            'file' => UploadedFile::fake()
                ->createWithContent($fileName, file_get_contents($exampleFile))
        ]);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page->component('Spreadsheets')
    );
    $uploadFileName = now()->format('YmdHi') . "_{$fileName}";
    $where = [
        'user_id' => $user->id,
        'path' => $uploadFileName
    ];
    $this->assertDatabaseHas(Spreadsheet::class, $where);
    $spreadsheet = Spreadsheet::where($where)->first();
    expect($spreadsheet->rows)->toBeGreaterThan(0);
    expect($spreadsheet->imported)->toBeGreaterThan(0);
    expect(Contact::count())->toBeGreaterThan($countContactsBeforeImport);
    Storage::assertExists($uploadFileName);
});
