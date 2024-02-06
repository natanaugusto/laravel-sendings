<?php

use App\Models\Contact;
use App\Models\Spreadsheet;

it('must create a contact', function () {
    $data = [
        'spreadsheet_id' => Spreadsheet::factory()->create()->id,
        'name' => 'João Doe',
        'email' => 'joao.doe@test.com',
        'phone' => '(909) 00909-0909',
        'document' => '333.333.333-33',
    ];
    $contact = Contact::create($data)->refresh();
    expect($contact->name)->toBe($data['name']);
    expect($contact->email)->toBe($data['email']);
    expect($contact->phone)->toBe($data['phone']);
    expect($contact->document)->toBe($data['document']);
    $this->assertDatabaseHas(Contact::class, $data);
});

it('must update an existent contact', function () {
    $contact = Contact::factory()->create();
    $data = [
        'name' => 'João Doe',
    ];
    $contact->update($data);
    $this->assertDatabaseHas(Contact::class, ['id' => $contact->id] + $data);
});

it('must delete an existent contact', function () {
    $contact = Contact::factory()->create();
    $contact->delete();
    $this->assertDatabaseMissing(Contact::class, ['id' => $contact->id]);
});

it('must belongs to a spreadsheet', function () {
    $contact = Contact::factory()->create([
        'spreadsheet_id' => Spreadsheet::factory()->create()->id
    ]);
    expect($contact->spreadsheet)->toBeInstanceOf(Spreadsheet::class);
});
