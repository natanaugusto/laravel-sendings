<?php

use App\Models\User;
use App\Models\Spreadsheet;

it('must create a spreadsheet', function () {
    $data = [
        'user_id' => User::factory()->create()->id,
        'path' => 'path/to/file',
    ];
    $spreadsheet = Spreadsheet::create($data)->refresh();
    expect($spreadsheet->path)->toBe($data['path']);
    expect($spreadsheet->rows)->toBe(0);
    expect($spreadsheet->imported)->toBe(0);
    expect($spreadsheet->fails)->toBe(0);
    $this->assertDatabaseHas(Spreadsheet::class, $data);
});

it('must update an existent spreadsheet', function () {
    $spreadsheet = Spreadsheet::factory()->create();
    $data = [
        'path' => '/new/path/to',
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
