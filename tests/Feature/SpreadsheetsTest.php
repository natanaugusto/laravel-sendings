<?php

use App\Models\User;
use App\Models\Spreadsheet;
use Inertia\Testing\AssertableInertia;

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
