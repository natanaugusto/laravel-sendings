<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\MessageRequest;
use App\Http\Requests\MessageStoreRequest;
use App\Jobs\SendMessageJob;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MessageRequest $request): InertiaResponse
    {
        return Inertia::render('Messages', [
            'messages' => $this->getPagiantion($request)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(MessageRequest $request): InertiaResponse
    {
        return Inertia::render('Messages', [
            'messages' => $this->getPagiantion($request),
            'showModalForm' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MessageStoreRequest $request): RedirectResponse
    {
        Message::create($request->all());
        return Redirect::back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MessageRequest $request, Message $message): InertiaResponse
    {
        return Inertia::render('Messages', [
            'messages' => $this->getPagiantion($request),
            'showModalForm' => true,
            'message' => $message
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MessageStoreRequest $request, Message $message): RedirectResponse
    {
        $message->fill($request->all());
        $message->saveOrFail();
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message): RedirectResponse
    {
        $message->deleteOrFail();
        return Redirect::back();
    }

    public function send(MessageRequest $request, Message $message): RedirectResponse
    {
        SendMessageJob::dispatch($request->user(), $message);
        return Redirect::back();
    }

    /**
     * Mount the pagination for messages
     */
    private function getPagiantion(MessageRequest $request): Paginator
    {
        [$column, $direction] = $request->sort();
        return Message::orderBy($column, $direction)
            ->with(['user'])
            ->paginate()
            ->appends(['sort' => $request->query('sort')]);
    }
}
