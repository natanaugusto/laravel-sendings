<?php

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
    $response->assertStatus(200);
    $response->assertInertia();
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Spreadsheets/Index')
            ->has('spreadsheets')
            ->where('spreadsheets', Spreadsheet::paginate()->toArray())
    );
});

it('must upload a spreadsheet', function () {
    $fileName = 'example.xlsx';
    $exampleFile = storage_path("app/{$fileName}");
    Storage::fake('local');
    $response = $this
        ->actingAs(User::factory()->create())
        ->postJson(route('spreadsheets.store'), [
            'file' => UploadedFile::fake()
                ->createWithContent($fileName, file_get_contents($exampleFile))
        ]);
    $response->assertStatus(HttpResponse::HTTP_CREATED);
    $uploadFileName = now()->format('YmdHi') . "_{$fileName}";
    Storage::assertExists($uploadFileName);
});
