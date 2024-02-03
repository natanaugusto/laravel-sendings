<?php

use App\Models\Spreadsheet;
use App\Models\User;

it('must create a spreadsheet', function () {
    $data = [
        'user_id' => User::factory()->create()->id,
        'path' => 'path/to/file',
        'rows' => 10,
    ];
    $spreadsheet = Spreadsheet::create($data)->refresh();
    expect($spreadsheet->path)->toBe($data['path']);
    expect($spreadsheet->rows)->toBe($data['rows']);
    expect($spreadsheet->imported)->toBe(0);
    expect($spreadsheet->fails)->toBe(0);
    $this->assertDatabaseHas(Spreadsheet::class, $data);
});

it('must update an existent spreadsheet', function () {
    $spreadsheet = Spreadsheet::factory()->create();
    $data = [
        'rows' => 15,
    ];
    $spreadsheet->update($data);
    $this->assertDatabaseHas(Spreadsheet::class, $data);
});

it('must delete an existent spreadsheet', function () {
    $spreadsheet = Spreadsheet::factory()->create();
    $spreadsheet->delete();
    $this->assertDatabaseMissing(Spreadsheet::class, ['id' => $spreadsheet->id]);
});