<?php

use App\Models\File;
use App\Models\User;

it('must create a file', function () {
    $data = [
        'user_id' => User::factory()->create()->id,
        'path' => 'path/to/file',
        'size' => 2024,
    ];
    $file = File::create($data)->refresh();
    expect($file->path)->toBe($data['path']);
    expect($file->size)->toBe($data['size']);
    $this->assertDatabaseHas(File::class, $data);
});

it('must update an existent file', function () {
    $file = File::factory()->create();
    $data = [
        'path' => '/new/path/to/file',
    ];
    $file->update($data);
    $this->assertDatabaseHas(File::class, ['id' => $file->id] + $data);
});

it('must delete an existent file', function () {
    $file = File::factory()->create();
    $file->delete();
    $this->assertDatabaseMissing(File::class, ['id' => $file->id]);
});

it('must belongs to a user', function () {
    $file = File::factory()->create([
        'user_id' => User::factory()->create()->id
    ]);
    expect($file->user)->toBeInstanceOf(User::class);
});
