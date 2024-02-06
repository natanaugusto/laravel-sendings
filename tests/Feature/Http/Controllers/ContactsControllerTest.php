<?php

use App\Models\User;
use App\Models\Contact;

use Inertia\Testing\AssertableInertia;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

it('has controllers/http/contactscontroller page', function () {
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
