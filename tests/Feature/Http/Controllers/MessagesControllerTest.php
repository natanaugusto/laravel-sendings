<?php

use App\Models\User;
use App\Models\Message;
use App\Jobs\SendMessageJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

it('has a list page', function () {
    Message::factory(20)->create();
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('messages.index'));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Messages')
            ->has('messages')
            ->where(
                'messages',
                Message::orderBy('id', 'asc')
                    ->with(['user'])
                    ->paginate()
                    ->toArray()
            )
    );
});

it('must order by parameter from querystring', function () {
    Message::factory(20)->create();
    $requester = $this
        ->actingAs(User::factory()->create());
    $response = $requester
        ->get(route('messages.index', ['sort' => 'subject']));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Messages')
            ->has('messages')
            ->where(
                'messages',
                Message::orderBy('subject', 'asc')
                    ->with(['user'])
                    ->paginate()
                    ->appends(['sort' => 'subject'])
                    ->toArray()
            )
    );

    $response = $requester
        ->get(route('messages.index', ['sort' => 'subject|desc']));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Messages')
            ->has('messages')
            ->where(
                'messages',
                Message::orderBy('subject', 'desc')
                    ->with(['user'])
                    ->paginate()
                    ->appends(['sort' => 'subject|desc'])
                    ->toArray()
            )
    );
});

it('has a create page', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('messages.create'));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Messages')
            ->has('messages')
            ->where('messages', Message::paginate()->toArray())
            ->has('showModalForm')
            ->where('showModalForm', true)
    );
});

it('must create a new message', function () {
    $message = Message::factory()->make();
    $response = $this->actingAs(User::factory()->create())
        ->post(
            route('messages.store'),
            $message->toArray()
        );
    $response->assertStatus(HttpResponse::HTTP_FOUND);
    $response->assertRedirect();
    $this->assertDatabaseHas(Message::class, $message->only(['subject']));
});

it('must to have an edit page for message', function () {
    $message = Message::factory()->create()->refresh();
    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('messages.edit', ['message' => $message->id]));
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Messages')
            ->has('messages')
            ->where('messages', Message::with(['user'])->paginate())
            ->has('showModalForm')
            ->where('showModalForm', true)
            ->has('message')
            ->where('message', $message)
    );
});

it('must to update a message', function () {
    $message = Message::factory()->create();
    $message->subject = 'New subject';
    $data = Arr::only($message->toArray(), [
        'subject',
        'body',
    ]);
    $response = $this
        ->actingAs(User::factory()->create())
        ->put(
            route('messages.update', ['message' => $message->id]),
            $data
        );
    $response->assertStatus(HttpResponse::HTTP_FOUND);
    $response->assertRedirect();
    $this->assertDatabaseHas(
        Message::class,
        [
            'id' => $message->id,
            'subject' => $data['subject']
        ]
    );
});

it('must delete a message', function () {
    $message = Message::factory()->create();
    $response = $this
        ->actingAs(User::factory()->create())
        ->delete(route('messages.destroy', ['message' => $message->id]));
    $response->assertStatus(HttpResponse::HTTP_FOUND);
    $response->assertRedirect();
    $this->assertDatabaseMissing(Message::class, ['id' => $message->id]);
});

it('must fails on validate the message store and update validations', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->post(route('messages.store'), [
            'subject' => null,
            'body' => null,
        ]);
    $response->assertRedirect()->withErrors(['subject', 'body']);
    $response = $this
        ->actingAs(User::factory()->create())
        ->put(
            route(
                'messages.update',
                ['message' => Message::factory()->create()->id]
            ),
            [
                'subject' => null,
                'body' => null,
            ]
        );
    $response->assertRedirect()->withErrors(['subject', 'body']);
});

it('must send message for all contacts by email', function () {
    $message = Message::factory()->create()->refresh();
    Queue::fake(SendMessageJob::class);
    $response = $this
        ->actingAs($message->user)
        ->post(route('messages.send', ['message' => $message]));
    $response->assertRedirect();
    Queue::assertPushedOn(Message::getQueueConnection(), SendMessageJob::class);
});

it('can have attatched files', function () {
    Storage::fake(Message::getStorageDisk());
    $message = Message::factory()->make();
    $files = [
        'attaches' => [
            UploadedFile::fake()->create('file1.txt'),
            UploadedFile::fake()->create('file2.txt')
        ]
    ];
    $response = $this->actingAs(User::factory()->create())
        ->post(
            route('messages.store'),
            $message->toArray() + $files
        );
    $response->assertStatus(HttpResponse::HTTP_FOUND);
    $response->assertRedirect();
    Storage::disk(Message::getStorageDisk())->assertExists(now()
        ->format('YmdHi') . '_' . $files['attaches'][0]->getClientOriginalName());
    // Storage::disk(Message::getStorageDisk())->assertExists(now()
    //     ->format('YmdHi') . '_' . $files['attaches'][1]->getClientOriginalName());
    $this->assertDatabaseHas(Message::class, $message->only(['subject']));
});
