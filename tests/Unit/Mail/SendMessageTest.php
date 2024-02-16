<?php

use App\Models\User;
use App\Models\Contact;
use App\Models\Message;
use App\Mail\SendMessage;
use Illuminate\Support\Facades\Mail;

it('send a mail using the message body as html/blade', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();
    $mailable = new SendMessage(
        $user,
        Message::factory()->create([
            'body' => '<p>{{ $name }}</p>'
        ]),
        $contact
    );
    Mail::to($contact->email)->send($mailable);
    $mailable->assertTo($contact->email);
    $mailable->assertFrom($user->email);
    $mailable->assertSeeInHtml($contact->name);
});
