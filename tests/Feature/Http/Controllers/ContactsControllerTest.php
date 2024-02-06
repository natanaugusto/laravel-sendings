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
});
