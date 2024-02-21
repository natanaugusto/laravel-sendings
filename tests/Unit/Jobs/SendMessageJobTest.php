<?php

use App\Jobs\SendMessageJob;
use App\Mail\SendMessage;
use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

it('must enqueue a SendMessage for the first chunk', function () {
    $count = 20;
    Contact::factory($count)->create();
    Bus::fake();
    SendMessageJob::dispatch(
        User::factory()->create(),
        Message::factory()->create(),
    )->onQueue(Message::getQueueConnection());
    Bus::assertDispatched(SendMessageJob::class, function ($job) use ($count) {
        $chunk = (int)config('excel.exports.chunk_size');
        Mail::fake();
        $job->handle();
        Mail::assertQueued(SendMessage::class);
        Mail::assertQueuedCount($chunk);
        expect($job->queue)->toBe(Message::getQueueConnection());
        expect($job->count)->toBe($count);
        expect($job->chunk)->toBe($chunk);
        return (bool)$job;
    });
});


it('must enqueue a SendMessage for all the contacts', function () {
    $count = 20;
    Contact::factory($count)->create();
    $job = new SendMessageJob(
        User::factory()->create(),
        Message::factory()->create()
    );
    Queue::fake(SendMessageJob::class);
    $job->handle();
    Queue::assertPushedOn(
        Message::getQueueConnection(),
        SendMessageJob::class,
        function ($job) {
            expect($job->offset)->toBeGreaterThan(0);
            return true;
        }
    );
});
