<?php

use App\Models\Message;
use App\Models\User;

it('must create a message', function () {
    $data = [
        'user_id' => User::factory()->create()->id,
        'subject' => 'This is an email',
        'body' => '<h1>The email</h1><p>this is the email body</p>'
    ];
    $message = Message::create($data)->refresh();
    expect($message->user_id)->toBe($data['user_id']);
    expect($message->subject)->toBe($data['subject']);
    expect($message->body)->toBe($data['body']);
    $this->assertDatabaseHas(Message::class, $data);
});

it('must update an existent message', function () {
    $message = Message::factory()->create();
    $data = [
        'subject' => 'This is a subject',
    ];
    $message->update($data);
    $this->assertDatabaseHas(Message::class, ['id' => $message->id] + $data);
});

it('must delete an existent message', function () {
    $message = Message::factory()->create();
    $message->delete();
    $this->assertDatabaseMissing(Message::class, ['id' => $message->id]);
});

it('must belongs to a user', function () {
    $message = Message::factory()->create([
        'user_id' => User::factory()->create()->id
    ]);
    expect($message->user)->toBeInstanceOf(User::class);
});
