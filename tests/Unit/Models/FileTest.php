<?php

use App\Models\File;
use App\Models\User;

it('must create a file', function () {
    $data = [
        'user_id' => User::factory()->create()->id,
        'path' => 'path/to/file',
        'size' => 2024,
    ];
    $file = new File($data);
    $file->fileable_id = 1;
    $file->fileable_type = 'App\Models\Test';
    $file->save();
    expect($file->path)->toBe($data['path']);
    expect($file->size)->toBe($data['size']);
    $this->assertDatabaseHas(File::class, $data);
});

it('must update an existent file', function () {
    $file = new File(File::factory()->make()->toArray());
    $file->fileable_id = 1;
    $file->fileable_type = 'App\Models\Test';
    $file->save();
    $data = [
        'path' => '/new/path/to/file',
    ];
    $file->update($data);
    $this->assertDatabaseHas(File::class, ['id' => $file->id] + $data);
});

it('must delete an existent file', function () {
    $file = new File(File::factory()->make()->toArray());
    $file->fileable_id = 1;
    $file->fileable_type = 'App\Models\Test';
    $file->save();
    $file->delete();
    $this->assertDatabaseMissing(File::class, ['id' => $file->id]);
});

it('must belongs to a user', function () {
    $file = new File(File::factory()->make()->toArray());
    $file->fileable_id = 1;
    $file->fileable_type = 'App\Models\Test';
    $file->save();
    expect($file->user)->toBeInstanceOf(User::class);
});
