<?php

use App\Models\Spreadsheet;
use Inertia\Testing\Assert;

it('has index page to list spreadsheets', function () {
    $response = $this->get(route('spreadsheets.index'));
    $response->assertStatus(302);
});
