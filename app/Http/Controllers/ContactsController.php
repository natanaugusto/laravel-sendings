<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\ContactStoreRequest;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ContactRequest $request): InertiaResponse
    {
        return Inertia::render('Contacts', [
            'contacts' => $this->getPagiantion($request)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ContactRequest $request): InertiaResponse
    {
        return Inertia::render('Contacts', [
            'contacts' => $this->getPagiantion($request),
            'showModalForm' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactStoreRequest $request): RedirectResponse
    {
        Contact::create($request->all());
        return Redirect::back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactRequest $request, Contact $contact): InertiaResponse
    {
        return Inertia::render('Contacts', [
            'contacts' => $this->getPagiantion($request),
            'showModalForm' => true,
            'contact' => $contact
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactStoreRequest $request, Contact $contact): RedirectResponse
    {
        $contact->fill($request->all());
        $contact->saveOrFail();
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->deleteOrFail();
        return Redirect::back();
    }

    /**
     * Mount the pagination for contacts
     */
    private function getPagiantion(ContactRequest $request): Paginator
    {
        [$column, $direction] = $request->sort();
        return Contact::orderBy($column, $direction)
            ->with(['spreadsheet'])
            ->paginate()
            ->appends(['sort' => $request->query('sort')]);
    }
}
