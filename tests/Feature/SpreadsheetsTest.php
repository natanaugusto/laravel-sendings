<?php

use App\Models\User;

it('has index page to list spreadsheets', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('spreadsheets.index'));
    $response->assertStatus(200);
});
