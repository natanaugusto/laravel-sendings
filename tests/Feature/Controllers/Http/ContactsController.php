<?php

it('has controllers/http/contactscontroller page', function () {
    $response = $this->get('/controllers/http/contactscontroller');

    $response->assertStatus(200);
});
