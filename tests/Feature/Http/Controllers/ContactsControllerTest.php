<?php

use App\Models\User;
use App\Models\Contact;

use Inertia\Testing\AssertableInertia;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

it('has a list page', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('contacts.index'));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Contacts')
            ->has('contacts')
            ->where('contacts', Contact::paginate()->toArray())
    );
});

it('has a create page', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('contacts.create'));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Contacts')
            ->has('contacts')
            ->where('contacts', Contact::paginate()->toArray())
            ->has('showModalForm')
            ->where('showModalForm', true)
    );
});

it('must create a new contact', function () {
    $contact = Contact::factory()->make();
    $response = $this->actingAs(User::factory()->create())
        ->post(
            route('contacts.store', $contact->toArray())
        );
    $response->assertStatus(HttpResponse::HTTP_FOUND);
    $response->assertRedirect(route('contacts.index'));
    $this->assertDatabaseHas(Contact::class, $contact->only(['email']));
});

it('must to have an edit page for contact', function () {
    $contact = Contact::factory()->create();
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('contacts.edit', ['contact' => $contact->id]));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Contacts')
            ->has('contacts')
            ->where('contacts', Contact::with(['spreadsheet'])->paginate())
            ->has('showModalForm')
            ->where('showModalForm', true)
            ->has('contact')
            ->where('contact', $contact)
    );
});

it('must to update a contact', function () {
    $contact = Contact::factory()->create();
    $contact->name = 'João Doe';
    $response = $this
        ->actingAs(User::factory()->create())
        ->put(
            route('contacts.update', ['contact' => $contact->id]),
            $contact->only([
                'name',
                'email',
                'phone',
                'document'
            ])
        );
    $response->assertStatus(HttpResponse::HTTP_FOUND);
    $response->assertRedirect(route('contacts.index'));
    $this->assertDatabaseHas(Contact::class, $contact->only(['name', 'email']));
});

it('must delete a contact', function () {
    $contact = Contact::factory()->create();
    $response = $this
        ->actingAs(User::factory()->create())
        ->delete(route('contacts.destroy', ['contact' => $contact->id]));
    $response->assertStatus(HttpResponse::HTTP_FOUND);
    $response->assertRedirect(route('contacts.index'));
    $this->assertDatabaseMissing(Contact::class, ['id' => $contact->id]);
});
